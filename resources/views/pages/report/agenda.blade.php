@extends('layout.main')

@push('style')
    <style>
        .rp-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
        .stat-box { text-align: center; padding: 1.25rem 0.75rem; border-radius: 0.9rem; background: var(--surat-bg); }
        .stat-box .val { font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-box .lbl { font-size: 0.8rem; color: #8696ab; }
        @media print {
            body * { visibility: hidden; }
            #printArea, #printArea * { visibility: visible; }
            #printArea { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('report.agenda')" :subtitle="__('report.agenda_subtitle')" icon="bx-chart">
                <x-slot:actions>
                    <a href="{{ route('report.agenda.export', request()->query()) }}" class="btn btn-hero"><i class="bx bx-download me-1"></i>{{ __('menu.general.export') }}</a>
                    <button class="btn btn-hero" onclick="window.print()"><i class="bx bx-printer me-1"></i>{{ __('report.print') }}</button>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            <div class="card rp-card">
                <div class="card-body">
                    <form class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted">{{ __('report.period') }}:</span>
                        @foreach(['day' => __('report.period_day'), 'week' => __('report.period_week'), 'month' => __('report.period_month'), 'upcoming' => __('report.period_upcoming'), 'done' => __('report.period_done')] as $key => $label)
                            <a href="{{ route('report.agenda', ['period' => $key]) }}"
                               class="btn btn-sm {{ $period === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card rp-card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-primary">{{ $summary->total }}</div><div class="lbl">{{ __('agenda.total') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-info">{{ $summary->this_month }}</div><div class="lbl">{{ __('report.this_month') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-success">{{ $summary->done }}</div><div class="lbl">{{ __('agenda.done') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-warning">{{ $summary->upcoming }}</div><div class="lbl">{{ __('report.upcoming_30') }}</div></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="printArea">
            <div class="card rp-card">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h5 class="mb-0 fw-bold">{{ __('report.agenda_list') }}</h5>
                    <small class="text-muted">{{ now()->isoFormat('dddd, DD MMMM YYYY') }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('agenda.agenda') }}</th>
                                    <th>{{ __('agenda.type') }}</th>
                                    <th>{{ __('agenda.date') }}</th>
                                    <th>{{ __('agenda.time') }}</th>
                                    <th>{{ __('agenda.location') }}</th>
                                    <th>{{ __('delegation.agenda') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                    <th class="text-end d-print-none">{{ __('menu.general.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $a)
                                    <tr>
                                        <td class="small"><a href="{{ route('agenda-pimpinan.show', $a->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $a->title }}</a></td>
                                        <td><span class="badge bg-label-primary">{{ $a->agenda_type_label }}</span></td>
                                        <td class="small">{{ $a->date?->isoFormat('DD MMM YYYY') }}</td>
                                        <td class="small">{{ $a->time_window }}</td>
                                        <td class="small"><i class="bx bx-map-pin text-muted me-1"></i>{{ $a->location ?? '-' }}</td>
                                        <td class="small">{{ $a->delegation?->title ?? '-' }}</td>
                                        <td><span class="badge {{ \App\Enums\AgendaStatus::badge($a->status) }}">{{ $a->agenda_status_label }}</span></td>
                                        <td class="text-end d-print-none">
                                            <a href="{{ route('agenda-pimpinan.show', $a->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-show"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">{{ __('agenda.empty') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection