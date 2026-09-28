@extends('layout.main')

@push('style')
    <style>
        .det-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
        .timeline { position: relative; padding-left: 1.6rem; }
        .timeline::before { content: ''; position: absolute; left: 10px; top: 6px; bottom: 6px; width: 2px; background: linear-gradient(180deg, rgba(109,103,228,0.5), rgba(54,241,205,0.2)); }
        .tl-node { position: relative; padding: 0 0 1.1rem 0; }
        .tl-node::before { content: ''; position: absolute; left: -1.44rem; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 3px solid var(--surat-primary); box-shadow: 0 0 0 4px rgba(109,103,228,0.15); }
        .tl-node:last-child { padding-bottom: 0; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">

        <div class="col-12">
            <x-page-hero :title="$data->title" :subtitle="$data->description" icon="bx-task">
                <x-slot:actions>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                        {{ $data->status_label }}
                    </span>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                        <i class="bx bx-flag me-1"></i>{{ $data->priority_label }}
                    </span>
                    <a href="{{ route('delegation.edit', $data->id) }}" class="btn btn-hero"><i class="bx bx-edit me-1"></i>{{ __('menu.general.edit') }}</a>
                    @if($data->status == 'draft')
                        <form action="{{ route('delegation.send', $data->id) }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn btn-hero"><i class="bx bx-send me-1"></i>{{ __('delegation.send') }}</button>
                        </form>
                    @endif
                    @if($data->status == 'ditolak')
                        <form action="{{ route('delegation.redelegate', $data->id) }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn btn-hero"><i class="bx bx-refresh me-1"></i>{{ __('delegation.re_delegate') }}</button>
                        </form>
                    @endif
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-lg-8">
            {{-- Informasi Delegasi --}}
            <div class="card det-card mb-4">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.detail_info') }}</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-user text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.creator') }}</small>
                                    <span class="fw-semibold">{{ $data->creator?->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-flag text-warning fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.priority') }}</small>
                                    <span class="fw-semibold">{{ $data->priority_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-calendar text-danger fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.deadline') }}</small>
                                    <span class="fw-semibold">{{ $data->formatted_deadline ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-check-shield text-success fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.status') }}</small>
                                    <span class="fw-semibold">{{ $data->status_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-user-check text-info fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.staff') }}</small>
                                    <span class="fw-semibold">{{ $data->tasks->pluck('staff.name')->implode(', ') ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @if($data->attachment)
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <i class="bx bx-paperclip text-secondary fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('delegation.attachment') }}</small>
                                        <a class="fw-semibold" href="{{ asset($data->attachment) }}" target="_blank"><i class="bx bx-download me-1"></i>{{ basename($data->attachment) }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($data->instruction)
                        <hr>
                        <small class="text-muted d-block mb-2 fw-semibold">{{ __('delegation.instruction') }}</small>
                        <p class="mb-0">{!! nl2br(e($data->instruction)) !!}</p>
                    @endif
                </div>
            </div>

            {{-- Sumber Surat --}}
            @if($data->letter)
                <div class="card det-card mb-4">
                    <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.source') }}</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><th class="text-muted" style="width:180px;">{{ __('delegation.letter_number') }}</th><td>{{ $data->letter->reference_number }}</td></tr>
                                    <tr><th class="text-muted">{{ __('delegation.letter_subject') }}</th><td>{{ $data->letter->description }}</td></tr>
                                    <tr><th class="text-muted">{{ __('delegation.letter_from') }}</th><td>{{ $data->letter->from }}</td></tr>
                                    <tr><th class="text-muted">{{ __('delegation.letter_date') }}</th><td>{{ $data->letter->letter_date?->isoFormat('D MMMM YYYY') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('transaction.incoming.show', $data->letter->id) }}" class="btn btn-sm btn-outline-primary mt-3"><i class="bx bx-show me-1"></i>{{ __('delegation.view_letter') }}</a>
                    </div>
                </div>
            @endif

            {{-- Task List --}}
            <div class="card det-card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ __('delegation.tasks') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead><tr>
                                <th>{{ __('delegation.task') }}</th>
                                <th>{{ __('delegation.staff') }}</th>
                                <th>{{ __('delegation.priority') }}</th>
                                <th>{{ __('delegation.progress') }}</th>
                                <th>{{ __('delegation.status') }}</th>
                            </tr></thead>
                            <tbody>
                                @forelse($data->tasks as $task)
                                    <tr>
                                        <td><a href="{{ route('task.show', $task->id) }}" class="fw-semibold text-decoration-none">{{ $task->title }}</a></td>
                                        <td><span class="badge bg-label-primary">{{ $task->staff?->name }}</span></td>
                                        <td><span class="badge {{ \App\Enums\Priority::badge($task->priority) }}">{{ $task->priority_label }}</span></td>
                                        <td style="min-width:140px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar bg-primary" style="width: {{ $task->progress }}%"></div></div>
                                                <small class="fw-bold">{{ $task->progress }}%</small>
                                            </div>
                                        </td>
                                        <td><span class="badge {{ \App\Enums\TaskStatus::badge($task->status) ?? 'bg-label-primary' }}">{{ $task->status_label }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">{{ __('task.empty') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Agenda --}}
            <div class="card det-card mb-4">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.agenda') }}</h5></div>
                <div class="card-body">
                    @if($data->agenda)
                        <div class="d-flex gap-2 mb-3">
                            <i class="bx bx-calendar-event text-primary fs-3"></i>
                            <div>
                                <a href="{{ route('agenda-pimpinan.show', $data->agenda->id) }}" class="fw-semibold text-decoration-none">{{ $data->agenda->title }}</a>
                                <div class="text-muted small">{{ $data->agenda->formatted_date }}</div>
                                <div class="text-muted small">{{ $data->agenda->time_window }} · {{ $data->agenda->location }}</div>
                            </div>
                        </div>
                        <a href="{{ route('agenda-pimpinan.edit', $data->agenda->id) }}" class="btn btn-sm btn-outline-primary w-100"><i class="bx bx-edit me-1"></i>{{ __('delegation.edit_agenda') }}</a>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-calendar-x fs-2"></i>
                            <p class="mb-1">{{ __('delegation.no_agenda') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card det-card">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.timeline') }}</h5></div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($timeline as $event)
                            <div class="tl-node">
                                <small class="text-muted d-block mb-1">{{ \Carbon\Carbon::parse($event['time'])->isoFormat('HH:mm') }}</small>
                                <span class="fw-semibold small">{{ $event['title'] }}</span>
                                @if(!empty($event['desc']))
                                    <p class="text-muted small mb-0">{{ $event['desc'] }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">{{ __('delegation.no_timeline') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection