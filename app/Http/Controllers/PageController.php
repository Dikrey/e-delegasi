<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Enums\Role;
use App\Helpers\GeneralHelper;
use App\Http\Requests\UpdateConfigRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Agenda;
use App\Models\Attachment;
use App\Models\Config;
use App\Models\Delegation;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskUpdate;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    /**
     * Dashboard home.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        if ($user->role === Role::SEKRETARIS->status()) {
            return $this->dashboardSekretaris($request);
        }

        if ($user->role === Role::STAFF->status()) {
            return $this->dashboardStaff($request);
        }

        return $this->dashboardAdmin($request);
    }

    /**
     * Dashboard khusus Sekretaris — pusat E-Delegasi.
     *
     * @param Request $request
     * @return View
     */
    protected function dashboardSekretaris(Request $request): View
    {
        $stats = (object) [
            'waitingVerification' => Letter::incoming()->waitingVerification()->count(),
            'activeDelegation' => Delegation::active()->count(),
            'runningTask' => Task::query()
                ->whereIn('status', ['baru', 'diterima', 'dalam_pengerjaan'])
                ->count(),
            'lateTask' => Task::late()->count(),
            'doneTask' => Task::done()->count(),
            'todayAgenda' => Agenda::today()->where('status', 'terjadwal')->count(),
        ];

        $todayAgendas = Agenda::today()
            ->where('status', 'terjadwal')
            ->with(['delegation.letter'])
            ->orderBy('start_time')
            ->get();

        $approachingTasks = Task::approaching(3)
            ->with(['delegation.letter', 'staff'])
            ->orderBy('deadline')
            ->get();

        $activeDelegations = Delegation::active()
            ->with(['tasks.staff', 'letter'])
            ->latest()
            ->limit(6)
            ->get();

        $recentAgendas = Agenda::with(['delegation.letter'])
            ->latest('date')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'title' => $a->title,
                'subtitle' => $a->formatted_date . ' · ' . ($a->location ?? '-'),
                'time' => $a->created_at->diffForHumans(),
                'icon' => 'bx-calendar-event',
                'color' => 'primary',
                'sort_at' => $a->created_at,
            ]);

        $recentDeliveries = Delegation::latest()
            ->with('creator')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'title' => $d->title,
                'subtitle' => __('model.user.' . ($d->creator?->role ?? 'staff')),
                'time' => $d->created_at->diffForHumans(),
                'icon' => 'bx-task',
                'color' => 'success',
                'sort_at' => $d->created_at,
            ]);

        $activities = $recentAgendas
            ->concat($recentDeliveries)
            ->sortByDesc('sort_at')
            ->values()
            ->take(8);

        return view('pages.dashboard-sekretaris', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'stats' => $stats,
            'todayAgendas' => $todayAgendas,
            'approachingTasks' => $approachingTasks,
            'activeDelegations' => $activeDelegations,
            'activities' => $activities,
        ]);
    }

    /**
     * Dashboard khusus Staff — fokus pada tugas pribadi.
     *
     * @param Request $request
     * @return View
     */
    protected function dashboardStaff(Request $request): View
    {
        $staffId = auth()->id();

        $stats = (object) [
            'baru' => Task::forStaff($staffId)->new()->count(),
            'dalam_pengerjaan' => Task::forStaff($staffId)->inProgress()->count(),
            'menunggu_review' => Task::forStaff($staffId)->pendingReview()->count(),
            'selesai' => Task::forStaff($staffId)->done()->count(),
            'terlambat' => Task::forStaff($staffId)->late()->count(),
        ];

        $highPriority = Task::forStaff($staffId)
            ->whereIn('priority', ['tinggi', 'urgent'])
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->with(['delegation.letter'])
            ->orderBy('deadline')
            ->get();

        $nearestDeadline = Task::forStaff($staffId)
            ->whereNotNull('deadline')
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->with(['delegation.letter'])
            ->orderBy('deadline')
            ->limit(6)
            ->get();

        $todayAgendas = Agenda::today()
            ->where('status', 'terjadwal')
            ->whereNotNull('delegation_id')
            ->with(['delegation.tasks' => fn ($q) => $q->where('staff_id', $staffId), 'letter'])
            ->get()
            ->filter(fn ($a) => $a->delegation?->tasks?->isNotEmpty());

        $recentTasks = Task::forStaff($staffId)
            ->with(['delegation.letter'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($t) => [
                'title' => $t->title,
                'subtitle' => $t->delegation?->letter?->reference_number ?? __('menu.general.empty'),
                'time' => $t->created_at->diffForHumans(),
                'icon' => 'bx-task',
                'color' => 'primary',
                'sort_at' => $t->created_at,
            ]);

        return view('pages.dashboard-staff', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'stats' => $stats,
            'highPriority' => $highPriority,
            'nearestDeadline' => $nearestDeadline,
            'todayAgendas' => $todayAgendas,
            'activities' => $recentTasks,
        ]);
    }

    /**
     * Dashboard Admin — tetap menampilkan statistik surat + ringkasan delegasi.
     *
     * @param Request $request
     * @return View
     */
    protected function dashboardAdmin(Request $request): View
    {
        $todayIncomingLetter = Letter::incoming()->today()->count();
        $todayOutgoingLetter = Letter::outgoing()->today()->count();
        $todayDispositionLetter = Disposition::today()->count();
        $todayLetterTransaction = $todayIncomingLetter + $todayOutgoingLetter + $todayDispositionLetter;

        $yesterdayIncomingLetter = Letter::incoming()->yesterday()->count();
        $yesterdayOutgoingLetter = Letter::outgoing()->yesterday()->count();
        $yesterdayDispositionLetter = Disposition::yesterday()->count();
        $yesterdayLetterTransaction = $yesterdayIncomingLetter + $yesterdayOutgoingLetter + $yesterdayDispositionLetter;

        $delegationStats = (object) [
            'total' => Delegation::count(),
            'active' => Delegation::active()->count(),
            'done' => Delegation::done()->count(),
            'late' => Delegation::late()->count(),
            'taskLate' => Task::late()->count(),
            'todayAgenda' => Agenda::today()->where('status', 'terjadwal')->count(),
        ];

        $weekLabels = [];
        $incomingPerDay = [];
        $outgoingPerDay = [];
        $dispositionPerDay = [];
        $start = now()->startOfDay()->subDays(6);

        $incomingWeekly = Letter::incoming()
            ->whereDate('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $outgoingWeekly = Letter::outgoing()
            ->whereDate('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $dispositionWeekly = Disposition::whereDate('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $key = $day->toDateString();
            $weekLabels[] = $day->isoFormat('dddd');
            $incomingPerDay[] = $incomingWeekly[$key] ?? 0;
            $outgoingPerDay[] = $outgoingWeekly[$key] ?? 0;
            $dispositionPerDay[] = $dispositionWeekly[$key] ?? 0;
        }

        $recentLetters = Letter::with('classification')
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(function ($letter) {
                $label = $letter->type === 'incoming'
                    ? __('dashboard.incoming_letter')
                    : __('dashboard.outgoing_letter');
                $icon  = $letter->type === 'incoming' ? 'bxs-inbox' : 'bx-mail-send';
                $color = $letter->type === 'incoming' ? 'info' : 'primary';
                return [
                    'title'   => $letter->reference_number,
                    'subtitle' => $label . ' — ' . ($letter->type === 'incoming' ? $letter->from : $letter->to),
                    'time'    => $letter->created_at->diffForHumans(),
                    'icon'    => $icon,
                    'color'   => $color,
                    'sort_at' => $letter->created_at,
                ];
            });

        $recentDispositions = Disposition::with('letter', 'user')
            ->latest('created_at')
            ->limit(4)
            ->get()
            ->map(function ($d) {
                return [
                    'title'    => $d->to,
                    'subtitle' => $d->letter?->reference_number . ' — ' . mb_substr($d->content, 0, 60),
                    'time'     => $d->created_at->diffForHumans(),
                    'icon'     => 'bx-share-alt',
                    'color'    => 'warning',
                    'sort_at'  => $d->created_at,
                ];
            });

        $recentDelegations = Delegation::with('creator')
            ->latest()
            ->limit(4)
            ->get()
            ->map(function ($d) {
                return [
                    'title'    => $d->title,
                    'subtitle' => __('activity.delegation_create', ['title' => $d->title]),
                    'time'     => $d->created_at->diffForHumans(),
                    'icon'     => 'bx-task',
                    'color'    => 'success',
                    'sort_at'  => $d->created_at,
                ];
            });

        $recentActivities = $recentLetters
            ->concat($recentDispositions)
            ->concat($recentDelegations)
            ->sortByDesc('sort_at')
            ->values()
            ->take(8);

        $totalLettersThisMonth = Letter::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalLettersThisYear = Letter::whereYear('created_at', now()->year)->count();

        $recentDelegationRows = Delegation::with(['tasks.staff', 'letter'])
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.dashboard', [
            'greeting' => GeneralHelper::greeting(),
            'currentDate' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'todayIncomingLetter' => $todayIncomingLetter,
            'todayOutgoingLetter' => $todayOutgoingLetter,
            'todayDispositionLetter' => $todayDispositionLetter,
            'todayLetterTransaction' => $todayLetterTransaction,
            'activeUser' => User::active()->count(),
            'percentageIncomingLetter' => GeneralHelper::calculateChangePercentage($yesterdayIncomingLetter, $todayIncomingLetter),
            'percentageOutgoingLetter' => GeneralHelper::calculateChangePercentage($yesterdayOutgoingLetter, $todayOutgoingLetter),
            'percentageDispositionLetter' => GeneralHelper::calculateChangePercentage($yesterdayDispositionLetter, $todayDispositionLetter),
            'percentageLetterTransaction' => GeneralHelper::calculateChangePercentage($yesterdayLetterTransaction, $todayLetterTransaction),
            'weekLabels' => $weekLabels,
            'incomingPerDay' => $incomingPerDay,
            'outgoingPerDay' => $outgoingPerDay,
            'dispositionPerDay' => $dispositionPerDay,
            'recentActivities' => $recentActivities,
            'totalLettersThisMonth' => $totalLettersThisMonth,
            'totalLettersThisYear' => $totalLettersThisYear,
            'delegationStats' => $delegationStats,
            'recentDelegationRows' => $recentDelegationRows,
        ]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function profile(Request $request): View
    {
        $currentSessionId = $request->session()->getId();

        return view('pages.profile', [
            'data' => auth()->user(),
            'currentSessionId' => $currentSessionId,
        ]);
    }

    /**
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function profileUpdate(UpdateUserRequest $request): RedirectResponse
    {
        try {
            $newProfile = $request->validated();

            $payload = [
                'name' => $newProfile['name'],
                'email' => $newProfile['email'],
                'phone' => $newProfile['phone'] ?? null,
            ];

            if ($request->hasFile('profile_picture')) {
//               DELETE OLD PICTURE
                $oldPicture = auth()->user()->profile_picture;
                if (str_contains($oldPicture, '/storage/avatars/')) {
                    $url = parse_url($oldPicture, PHP_URL_PATH);
                    Storage::delete(str_replace('/storage', 'public', $url));
                }

//                UPLOAD NEW PICTURE
                $filename = time() .
                    '-' . $request->file('profile_picture')->getFilename() .
                    '.' . $request->file('profile_picture')->getClientOriginalExtension();
                $request->file('profile_picture')->storeAs('public/avatars', $filename);
                $payload['profile_picture'] = asset('storage/avatars/' . $filename);
            }

            $passwordChanged = !empty($newProfile['password']);
            if ($passwordChanged) {
                $payload['password'] = Hash::make($newProfile['password']);
            }

            auth()->user()->update($payload);

            // Changing your own password signs every device out immediately.
            if ($passwordChanged) {
                auth()->user()->revokeAllSessions();
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('info', __('auth.password_changed'));
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @return RedirectResponse
     */
    public function deactivate(): RedirectResponse
    {
        try {
            $name = auth()->user()?->name ?? '';
            auth()->user()->update(['is_active' => false]);
            auth()->user()->revokeAllSessions();
            Auth::logout();
            return redirect()->route('blocked')->with('blocked_user', $name);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function settings(Request $request): View
    {
        return view('pages.setting', [
            'configs' => Config::all(),
        ]);
    }

    /**
     * @param UpdateConfigRequest $request
     * @return RedirectResponse
     */
    public function settingsUpdate(UpdateConfigRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->validated() as $code => $value) {
                Config::where('code', $code)->update(['value' => $value]);
            }
            DB::commit();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeAttachment(Request $request): RedirectResponse
    {
        try {
            $attachment = Attachment::find($request->id);
            $oldPicture = $attachment->path_url;
            if (str_contains($oldPicture, '/storage/attachments/')) {
                $url = parse_url($oldPicture, PHP_URL_PATH);
                Storage::delete(str_replace('/storage', 'public', $url));
            }
            $attachment->delete();
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}