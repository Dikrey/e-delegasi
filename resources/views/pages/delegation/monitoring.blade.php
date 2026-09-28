@extends('layout.main')

@push('style')
    <style>
        .board { display: flex; gap: 1rem; overflow-x: auto; align-items: flex-start; padding-bottom: 1rem; }
        .board-col { min-width: 280px; width: 280px; flex-shrink: 0; }
        .board-col .head { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.75rem; background: #fff; border-radius: 0.9rem; box-shadow: 0 6px 16px -6px rgba(38,42,71,0.25); font-weight: 700; font-size: 0.85rem; }
        .head .count { margin-left: auto; font-weight: 700; font-size: 0.8rem; padding: 2px 10px; border-radius: 999px; background: rgba(109,103,228,0.12); color: var(--surat-primary); }
        .board-item { background: #fff; border-radius: 0.9rem; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 6px 16px -6px rgba(38,42,71,0.2); border-left: 4px solid var(--surat-primary); }
        .board-empty { border: 2px dashed rgba(38,42,71,0.15); border-radius: 0.9rem; text-align: center; color: #aaa; padding: 1.25rem 0.5rem; font-size: 0.85rem; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('delegation.monitoring')" :subtitle="__('delegation.monitoring_subtitle')" icon="bx-columns">
                <x-slot:actions>
                    <a href="{{ route('delegation.index') }}" class="btn btn-hero"><i class="bx bx-list-ul me-1"></i>{{ __('delegation.list') }}</a>
                    <a href="{{ route('delegation.create') }}" class="btn btn-hero"><i class="bx bx-plus me-1"></i>{{ __('delegation.create_btn') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            @php $items = collect($data->items()); @endphp
            <div class="card">
                <div class="card-body">
                    <div class="board">
                        @foreach(\App\Enums\DelegationStatus::options() as $statusKey => $statusLabel)
                            @php $columnItems = $items->where('status', $statusKey); @endphp
                            <div class="board-col">
                                <div class="head">
                                    <span>{{ $statusLabel }}</span>
                                    <span class="count">{{ $columnItems->count() }}</span>
                                </div>
                                @forelse($columnItems as $delegation)
                                    <a href="{{ route('delegation.show', $delegation->id) }}" class="board-item d-block text-decoration-none text-dark">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="small">{{ $delegation->title }}</strong>
                                            <span class="badge {{ \App\Enums\Priority::badge($delegation->priority) }}">{{ $delegation->priority_label }}</span>
                                        </div>
                                        @if($delegation->letter)
                                            <div class="text-muted small mb-2"><i class="bx bx-envelope me-1"></i>{{ $delegation->letter->reference_number }}</div>
                                        @endif
                                        @php $avg = $delegation->tasks->where('status', '<>', 'ditolak')->avg('progress'); @endphp
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar bg-primary" style="width:{{ round($avg ?? 0) }}%"></div></div>
                                            <small class="fw-bold">{{ round($avg ?? 0) }}%</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @foreach($delegation->tasks as $task)
                                                    <span class="badge bg-label-primary me-1">{{ $task->staff?->name ?? '-' }}</span>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">{{ $delegation->formatted_deadline ?? '-' }}</small>
                                        </div>
                                    </a>
                                @empty
                                    <div class="board-empty">{{ __('delegation.board_empty') }}</div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection