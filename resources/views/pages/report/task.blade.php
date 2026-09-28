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
            <x-page-hero :title="__('report.task')" :subtitle="__('report.task_subtitle')" icon="bx-chart">
                <x-slot:actions>
                    <a href="{{ route('report.task.export', request()->query()) }}" class="btn btn-hero"><i class="bx bx-download me-1"></i>{{ __('menu.general.export') }}</a>
                    <button class="btn btn-hero" onclick="window.print()"><i class="bx bx-printer me-1"></i>{{ __('report.print') }}</button>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            <div class="card rp-card">
                <div class="card-body">
                    <form class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">{{ __('report.from') }}</label>
                            <input type="date" name="since" value="{{ $since }}" class="form-control">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">{{ __('report.to') }}</label>
                            <input type="date" name="until" value="{{ $until }}" class="form-control">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">{{ __('delegation.staff') }}</label>
                            <select name="staff_id" class="form-select">
                                <option value="">{{ __('menu.general.all') }}</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ $staffId == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label small text-muted mb-1">{{ __('delegation.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="all">{{ __('menu.general.all') }}</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i>{{ __('menu.general.filter') }}</button>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('report.task') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card rp-card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-primary">{{ $summary->total }}</div><div class="lbl">{{ __('task.total') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-success">{{ $summary->done }}</div><div class="lbl">{{ __('task.done') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-warning">{{ $summary->in_progress }}</div><div class="lbl">{{ __('task.in_progress') }}</div></div></div>
                        <div class="col-6 col-lg-3"><div class="stat-box"><div class="val text-danger">{{ $summary->late }}</div><div class="lbl">{{ __('task.late') }}</div></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="printArea">
            <div class="card rp-card">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h5 class="mb-0 fw-bold">{{ __('report.task_list') }}</h5>
                    <small class="text-muted">{{ now()->isoFormat('dddd, DD MMMM YYYY') }}</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>{{ __('task.task') }}</th>
                                    <th>{{ __('task.assigned_to') }}</th>
                                    <th>{{ __('delegation.source_letter') }}</th>
                                    <th>{{ __('delegation.priority') }}</th>
                                    <th>{{ __('delegation.deadline') }}</th>
                                    <th>{{ __('delegation.progress') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                    <th class="text-end d-print-none">{{ __('menu.general.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $t)
                                    <tr>
                                        <td class="small"><a href="{{ route('task.show', $t->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $t->title }}</a></td>
                                        <td class="small">{{ $t->staff?->name }}</td>
                                        <td class="small">{{ $t->delegation?->letter?->reference_number ?? '-' }}</td>
                                        <td><span class="badge {{ \App\Enums\Priority::badge($t->priority) }}">{{ $t->priority_label }}</span></td>
                                        <td class="small">{{ $t->formatted_deadline ?? '-' }}</td>
                                        <td style="min-width:130px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;">
                                                    <div class="progress-bar {{ $t->status === 'selesai' ? 'bg-success' : 'bg-primary' }}" style="width: {{ $t->progress }}%"></div>
                                                </div>
                                                <small class="fw-bold">{{ $t->progress }}%</small>
                                            </div>
                                        </td>
                                        <td><span class="badge {{ \App\Enums\TaskStatus::badge($t->status) }}">{{ $t->status_label }}</span></td>
                                        <td class="text-end d-print-none">
                                            <a href="{{ route('task.show', $t->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-show"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">{{ __('task.empty') }}</td></tr>
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