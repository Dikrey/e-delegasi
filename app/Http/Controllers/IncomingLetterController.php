<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Helpers\ActivityLogHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Http\Requests\VerifyLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Delegation;
use App\Models\Letter;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.transaction.incoming.index', [
            'data' => Letter::incoming()->render($request->search),
            'search' => $request->search,
        ]);
    }

    /**
     * Halaman verifikasi surat masuk (sekretaris).
     *
     * @param Letter $incoming
     * @return View
     */
    public function verify(Letter $incoming): View
    {
        return view('pages.transaction.incoming.verify', [
            'data' => $incoming->load(['classification', 'user', 'attachments', 'delegations', 'verifier']),
            'delegations' => $incoming->delegations()->with('creator')->latest()->get(),
        ]);
    }

    /**
     * Proses verifikasi surat masuk.
     *
     * @param VerifyLetterRequest $request
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function verified(VerifyLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $incoming->update([
                'verification_status' => $request->verification_status,
                'verification_note' => $request->verification_note,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $hasDelegation = $incoming->delegations()->where('status', '<>', 'draft')->exists();

            ActivityLogHelper::log(
                __('activity.letter_verify', ['status' => \App\Enums\LetterVerification::options()[$request->verification_status] ?? $request->verification_status]),
                'letter',
                $incoming->id,
                ['reference_number' => $incoming->reference_number]
            );

            if (!$hasDelegation) {
                NotificationHelper::send(
                    \App\Models\User::role(\App\Enums\Role::SEKRETARIS)->pluck('id')->all(),
                    'system',
                    __('notify.letter_verified_title'),
                    __('notify.letter_verified_message', ['reference' => $incoming->reference_number]),
                    route('transaction.incoming.show', $incoming->id)
                );
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display a listing of the incoming letter agenda.
     *
     * @param Request $request
     * @return View
     */
    public function agenda(Request $request): View
    {
        return view('pages.transaction.incoming.agenda', [
            'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->render($request->search),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'query' => $request->getQueryString(),
        ]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.incoming_letter');
        $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";
        return view('pages.transaction.incoming.print', [
            'data' => Letter::incoming()->agenda($request->since, $request->until, $request->filter)->get(),
            'search' => $request->search,
            'since' => $request->since,
            'until' => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value','code')->toArray(),
            'title' => $title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('pages.transaction.incoming.create', [
            'classifications' => Classification::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLetterRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            $user = auth()->user();

            if ($request->type != LetterType::INCOMING->type()) throw new \Exception(__('menu.transaction.incoming_letter'));
            $newLetter = $request->validated();
            $newLetter['user_id'] = $user->id;
            $letter = Letter::create($newLetter);
            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;
                    $filename = time() . '-'. $attachment->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $attachment->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => $user->id,
                        'letter_id' => $letter->id,
                    ]);
                }
            }

            $secretaries = \App\Models\User::role(\App\Enums\Role::SEKRETARIS)->pluck('id')->all();
            if (!empty($secretaries)) {
                NotificationHelper::send(
                    $secretaries,
                    'letter',
                    __('notify.letter_new_title'),
                    __('notify.letter_new_message', ['reference' => $letter->reference_number]),
                    route('transaction.incoming.show', $letter->id)
                );

                NotificationHelper::send(
                    $secretaries,
                    'system',
                    __('notify.letter_verify_title'),
                    __('notify.letter_verify_message', ['reference' => $letter->reference_number]),
                    route('transaction.incoming.verify', $letter->id)
                );
            }

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function show(Letter $incoming): View
    {
        return view('pages.transaction.incoming.show', [
            'data' => $incoming->load(['classification', 'user', 'attachments']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Letter $incoming
     * @return View
     */
    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data' => $incoming,
            'classifications' => Classification::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLetterRequest $request
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $incoming->update($request->validated());
            if ($request->hasFile('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $extension = $attachment->getClientOriginalExtension();
                    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) continue;
                    $filename = time() . '-'. $attachment->getClientOriginalName();
                    $filename = str_replace(' ', '-', $filename);
                    $attachment->storeAs('public/attachments', $filename);
                    Attachment::create([
                        'filename' => $filename,
                        'extension' => $extension,
                        'user_id' => auth()->user()->id,
                        'letter_id' => $incoming->id,
                    ]);
                }
            }
            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Letter $incoming
     * @return RedirectResponse
     */
    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            $incoming->delete();
            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
