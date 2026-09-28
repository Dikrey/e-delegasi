<?php

namespace App\Http\Controllers;

use App\Enums\Config as ConfigEnum;
use App\Models\Config;
use App\Models\Letter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchiveController extends Controller
{
    /**
     * Display the combined archive of incoming & outgoing letters.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type'   => ['nullable', 'string', 'in:incoming,outgoing'],
            'since'  => ['nullable', 'date'],
            'until'  => ['nullable', 'date', 'after_or_equal:since'],
        ]);

        $since = $validated['since'] ?? null;
        $until = $validated['until'] ?? null;

        $letters = Letter::query()
            ->with(['classification', 'attachments'])
            ->when(!empty($validated['type']), function ($query) use ($validated) {
                return $query->where('type', $validated['type']);
            })
            ->when($since && $until, function ($query) use ($since, $until) {
                return $query->whereBetween(DB::raw('DATE(letter_date)'), [$since, $until]);
            })
            ->search($validated['search'] ?? null)
            ->latest('letter_date')
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $validated['search'] ?? null,
                'type'   => $validated['type'] ?? null,
                'since'  => $since,
                'until'  => $until,
            ]);

        return view('pages.archive.index', [
            'data' => $letters,
            'search' => $validated['search'] ?? null,
            'type'   => $validated['type'] ?? null,
            'since'  => $since,
            'until'  => $until,
            'totalIncoming' => Letter::incoming()->count(),
            'totalOutgoing' => Letter::outgoing()->count(),
        ]);
    }

    /**
     * Export the (optionally filtered) archive as CSV.
     *
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type'   => ['nullable', 'string', 'in:incoming,outgoing'],
            'since'  => ['nullable', 'date'],
            'until'  => ['nullable', 'date', 'after_or_equal:since'],
        ]);

        $since = $validated['since'] ?? null;
        $until = $validated['until'] ?? null;

        $letters = Letter::query()
            ->with(['classification'])
            ->when(!empty($validated['type']), function ($query) use ($validated) {
                return $query->where('type', $validated['type']);
            })
            ->when($since && $until, function ($query) use ($since, $until) {
                return $query->whereBetween(DB::raw('DATE(letter_date)'), [$since, $until]);
            })
            ->search($validated['search'] ?? null)
            ->latest('letter_date')
            ->get();

        $filename = 'arsip-surat-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($letters) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                __('menu.archive.type'),
                __('model.letter.reference_number'),
                __('model.letter.agenda_number'),
                __('model.letter.from'),
                __('model.letter.to'),
                __('model.letter.letter_date'),
                __('model.letter.received_date'),
                __('model.letter.description'),
                __('model.letter.note'),
                __('model.classification.type'),
                __('model.general.created_at'),
            ]);

            foreach ($letters as $letter) {
                fputcsv($handle, [
                    $letter->type == 'incoming' ? __('menu.transaction.incoming_letter') : __('menu.transaction.outgoing_letter'),
                    $letter->reference_number,
                    $letter->agenda_number,
                    $letter->from,
                    $letter->to,
                    $letter->letter_date?->format('Y-m-d'),
                    $letter->received_date?->format('Y-m-d'),
                    $letter->description,
                    $letter->note,
                    $letter->classification?->type ?? '',
                    $letter->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}