@extends('layout.main')

@push('style')
    <style>
        .stat-card-hover {
            border: none !important; border-radius: 1rem !important;
            box-shadow: 0 10px 26px -12px rgba(38, 42, 71, 0.32);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -16px rgba(38,42,71,0.45); }
        .stat-glyph { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 13px; font-size: 21px; color: #fff; }
    </style>
@endpush

@section('content')
    @php
        $sortUrl = function ($key) use ($sort, $direction) {
            $dir = ($sort === $key && $direction === 'asc') ? 'desc' : 'asc';
            return route('delegation.index', array_merge(request()->query(), ['sort' => $key, 'direction' => $dir]));
        };
        $sortIcon = function ($key) use ($sort, $direction) {
            if ($sort !== $key) {
                return '<i class="bx bx-sm bx-chevrons-up-down text-muted ms-1"></i>';
            }
            return $direction === 'asc'
                ? '<i class="bx bx-sm bx-chevron-up ms-1"></i>'
                : '<i class="bx bx-sm bx-chevron-down ms-1"></i>';
        };
    @endphp
    <div class="row gy-4">

        <div class="col-12">
            <x-page-hero :title="__('delegation.menu')" :subtitle="__('delegation.index_subtitle')" icon="bx-task">
                <x-slot:actions>
                    <a href="{{ route('delegation.create') }}" class="btn btn-hero"><i class="bx bx-plus me-1"></i>{{ __('delegation.create_btn') }}</a>
                    <a href="{{ route('delegation.monitoring') }}" class="btn btn-hero"><i class="bx bx-columns me-1"></i>{{ __('delegation.monitoring') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        {{-- STAT --}}
        <div class="col-lg-6 col-xl-3 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold text-muted small">{{ __('delegation.total') }}</span>
                        <h3 class="mb-0 fw-bold">{{ $stats->total }}</h3>
                    </div>
                    <div class="stat-glyph" style="background:linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-layer"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold text-muted small">{{ __('delegation.active') }}</span>
                        <h3 class="mb-0 fw-bold">{{ $stats->active }}</h3>
                    </div>
                    <div class="stat-glyph" style="background:linear-gradient(135deg,#36c2f1,#0288d1);"><i class="bx bx-cycling"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold text-muted small">{{ __('delegation.done') }}</span>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats->done }}</h3>
                    </div>
                    <div class="stat-glyph" style="background:linear-gradient(135deg,#36f1a5,#00b874);"><i class="bx bx-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold text-muted small">{{ __('delegation.late') }}</span>
                        <h3 class="mb-0 fw-bold text-danger">{{ $stats->late }}</h3>
                    </div>
                    <div class="stat-glyph" style="background:linear-gradient(135deg,#ff6b6b,#e41e1e);"><i class="bx bx-time"></i></div>
                </div>
            </div>
        </div>

        {{-- RINGKASAN HARI INI --}}
        <div class="col-12">
            <div class="card stat-card-hover">
                <div class="card-header py-3 d-flex align-items-center gap-2" style="background:linear-gradient(90deg, rgba(109,103,228,0.08), rgba(54,241,205,0.06));border:none;">
                    <i class="bx bx-sun fs-4 text-primary"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ __('delegation.today_summary') }}</h5>
                        <small class="text-muted">{{ \Illuminate\Support\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-xl-3">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(109,103,228,0.07);">
                                <i class="bx bx-plus-circle fs-3 text-primary"></i>
                                <div>
                                    <h4 class="mb-0 fw-bold">{{ $today->created }}</h4>
                                    <small class="text-muted">{{ __('delegation.today_created') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,62,29,0.07);">
                                <i class="bx bx-hourglass fs-3 text-danger"></i>
                                <div>
                                    <h4 class="mb-0 fw-bold">{{ $today->deadline }}</h4>
                                    <small class="text-muted">{{ __('delegation.today_deadline') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(245,158,11,0.09);">
                                <i class="bx bx-task fs-3 text-warning"></i>
                                <div>
                                    <h4 class="mb-0 fw-bold">{{ $today->task }}</h4>
                                    <small class="text-muted">{{ __('delegation.today_task') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(2,188,125,0.08);">
                                <i class="bx bx-calendar-event fs-3 text-success"></i>
                                <div>
                                    <h4 class="mb-0 fw-bold">{{ $today->agenda }}</h4>
                                    <small class="text-muted">{{ __('delegation.today_agenda') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                    <div class="dropdown me-auto">
                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" type="button">
                            {{ __('delegation.filter_status') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('delegation.index', ['status' => 'all']) }}">{{ __('delegation.all') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('delegation.index', ['status' => 'active']) }}">{{ __('delegation.active') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('delegation.index', ['status' => 'done']) }}">{{ __('delegation.done') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('delegation.index', ['status' => 'late']) }}">{{ __('delegation.late') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('delegation.index', ['status' => 'draft']) }}">{{ __('delegation.draft') }}</a></li>
                        </ul>
                    </div>
                    <form class="d-flex gap-2 ms-auto">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('menu.general.search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bx bx-search"></i></button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('title') }}">{{ __('delegation.delegation') }}{!! $sortIcon('title') !!}</a></th>
                                    <th>{{ __('delegation.staff') }}</th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('priority') }}">{{ __('delegation.priority') }}{!! $sortIcon('priority') !!}</a></th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('deadline') }}">{{ __('delegation.deadline') }}{!! $sortIcon('deadline') !!}</a></th>
                                    <th>{{ __('delegation.progress') }}</th>
                                    <th>{{ __('delegation.status') }}</th>
                                    <th class="text-end">{{ __('menu.general.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $delegation)
                                    <tr>
                                        <td>
                                            <a href="{{ route('delegation.show', $delegation->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $delegation->title }}</a>
                                            @if($delegation->letter)
                                                <div class="small text-muted"><i class="bx bx-envelope me-1"></i>{{ $delegation->letter->reference_number }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($delegation->tasks as $task)
                                                <span class="badge bg-label-primary me-1">{{ $task->staff?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td><span class="badge {{ \App\Enums\Priority::badge($delegation->priority) }}">{{ $delegation->priority_label }}</span></td>
                                        <td class="small">{{ $delegation->formatted_deadline ?? '-' }}</td>
                                        <td style="min-width:140px;">
                                            @php $avg = $delegation->tasks->where('status', '<>', 'ditolak')->avg('progress'); @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ round($avg ?? 0) }}%"></div>
                                                </div>
                                                <small class="fw-bold">{{ round($avg ?? 0) }}%</small>
                                            </div>
                                        </td>
                                        <td><span class="badge {{ \App\Enums\DelegationStatus::badge($delegation->status) ?? 'bg-label-primary' }}">{{ $delegation->status_label }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('delegation.show', $delegation->id) }}"><i class="bx bx-show me-1"></i>{{ __('menu.general.view') }}</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('delegation.edit', $delegation->id) }}"><i class="bx bx-edit me-1"></i>{{ __('menu.general.edit') }}</a></li>
                                                    @if($delegation->status == 'draft')
                                                        <li>
                                                            <form action="{{ route('delegation.send', $delegation->id) }}" method="post">
                                                                @csrf
                                                                <button class="dropdown-item"><i class="bx bx-send me-1"></i>{{ __('delegation.send') }}</button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('delegation.destroy', $delegation->id) }}" method="post" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button class="dropdown-item text-danger btn-delete"><i class="bx bx-trash me-1"></i>{{ __('menu.general.delete') }}</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">{{ __('delegation.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection