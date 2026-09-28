@extends('layout.main')

@section('content')
    @php
        $sortUrl = function ($key) use ($sort, $direction) {
            $dir = ($sort === $key && $direction === 'asc') ? 'desc' : 'asc';
            return route('task.index', array_merge(request()->query(), ['sort' => $key, 'direction' => $dir]));
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
            <x-page-hero :title="__('task.my_tasks')" :subtitle="__('task.index_subtitle')" icon="bx-list-check">
                <x-slot:actions>
                    <a href="{{ route('task.kanban') }}" class="btn btn-hero"><i class="bx bx-columns me-1"></i>{{ __('task.kanban') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="d-flex flex-wrap gap-2 mb-3">
                        <input type="text" name="search" value="{{ $search }}" class="form-control w-auto" placeholder="{{ __('menu.general.search') }}">
                        <select name="status" class="form-select w-auto">
                            <option value="">{{ __('menu.general.all') }}</option>
                            @foreach(\App\Enums\TaskStatus::options() as $key => $label)
                                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="form-select w-auto">
                            <option value="">{{ __('delegation.priority') }} : {{ __('menu.general.all') }}</option>
                            @foreach(\App\Enums\Priority::options() as $key => $label)
                                <option value="{{ $key }}" {{ $priority === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i>{{ __('menu.general.filter') }}</button>
                        <a href="{{ route('task.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset me-1"></i>{{ __('menu.general.reset') }}</a>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('title') }}">{{ __('task.task') }}{!! $sortIcon('title') !!}</a></th>
                                    <th>{{ __('delegation.source_letter') }}</th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('priority') }}">{{ __('delegation.priority') }}{!! $sortIcon('priority') !!}</a></th>
                                    <th>{{ __('delegation.task_category') }}</th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('deadline') }}">{{ __('delegation.deadline') }}{!! $sortIcon('deadline') !!}</a></th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('progress') }}">{{ __('delegation.progress') }}{!! $sortIcon('progress') !!}</a></th>
                                    <th><a class="text-dark text-decoration-none" href="{{ $sortUrl('status') }}">{{ __('delegation.status') }}{!! $sortIcon('status') !!}</a></th>
                                    <th class="text-end">{{ __('menu.general.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('task.show', $task->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $task->title }}</a>
                                            <div class="small text-muted">{{ $task->delegation?->title }}</div>
                                        </td>
                                        <td class="small">
                                            <span>{{ $task->delegation?->letter?->reference_number ?? '-' }}</span>
                                        </td>
                                        <td><span class="badge {{ \App\Enums\Priority::badge($task->priority) }}">{{ $task->priority_label }}</span></td>
                                        <td class="small">
                                            @if($task->taskCategory)
                                                <span class="badge rounded-pill" style="background:{{ $task->taskCategory->color }};color:#fff;">{{ $task->taskCategory->name }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="small">
                                            {{ $task->formatted_deadline ?? '-' }}
                                            @if($task->deadline_status_label)
                                                <span class="badge {{ $task->deadline_status_badge }} ms-1">{{ $task->deadline_status_label }}</span>
                                            @endif
                                        </td>
                                        <td style="min-width:120px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar bg-primary" style="width:{{ $task->progress }}%"></div></div>
                                                <small class="fw-bold">{{ $task->progress }}%</small>
                                            </div>
                                        </td>
                                        <td><span class="badge {{ \App\Enums\TaskStatus::badge($task->status) ?? 'bg-label-primary' }}">{{ $task->status_label }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('task.show', $task->id) }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-show"></i></a>
                                            @if(in_array($task->status, ['baru', 'ditolak']))
                                                <form action="{{ route('task.accept', $task->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success"><i class="bx bx-check-circle"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">{{ __('task.empty') }}</td></tr>
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