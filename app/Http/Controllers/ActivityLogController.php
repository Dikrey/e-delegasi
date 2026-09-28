<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::query()->with('user');

        if ($request->get('module') && $request->get('module') !== 'all') {
            $query->module($request->get('module'));
        }

        $data = $query
            ->when($request->search, function ($q, $s) {
                return $q->where(function ($q2) use ($s) {
                    return $q2
                        ->where('action', 'LIKE', '%' . $s . '%')
                        ->orWhere('module', 'LIKE', '%' . $s . '%')
                        ->orWhereHas('user', fn ($q) => $q->where('name', 'LIKE', '%' . $s . '%'));
                });
            })
            ->latest()
            ->paginate(\App\Models\Config::getValueByCode(\App\Enums\Config::PAGE_SIZE))
            ->withQueryString();

        return view('pages.activity-log.index', [
            'data' => $data,
            'search' => $request->search,
            'module' => $request->get('module', 'all'),
            'modules' => ActivityLog::query()->distinct()->orderBy('module')->pluck('module'),
        ]);
    }
}