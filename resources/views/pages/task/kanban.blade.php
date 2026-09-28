@extends('layout.main')

@push('style')
    <style>
        .board-x {
            display: flex;
            gap: 1.1rem;
            overflow-x: auto;
            align-items: flex-start;
            padding: 0.2rem 0 1rem;
            scroll-snap-type: x proximity;
        }
        .board-x-col {
            min-width: 300px;
            width: 300px;
            flex-shrink: 0;
            border-radius: 1.1rem;
            padding: 0.9rem;
            background: var(--surat-bg);
            scroll-snap-align: start;
            transition: background-color 0.2s ease;
        }
        .board-x-col.drag-over { outline: 2px dashed var(--surat-primary); outline-offset: 2px; background: rgba(105, 108, 255, 0.05); }
        .board-x-head { display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.7rem; padding: 0.1rem 0.25rem 0.5rem; border-bottom: 1px solid rgba(38, 42, 71, 0.08); }
        .board-x-head .dot { width: 9px; height: 9px; border-radius: 999px; }
        .board-x-head .count { margin-left: auto; font-weight: 700; font-size: 0.78rem; padding: 2px 10px; border-radius: 999px; background: rgba(38, 42, 71, 0.08); }
        .board-x-empty { text-align: center; color: var(--bs-secondary-color, #a0a3b5); font-size: 0.8rem; padding: 1.2rem 0.5rem; border: 1px dashed rgba(38, 42, 71, 0.15); border-radius: 0.8rem; }
        .task-card {
            background: #fff;
            border-radius: 0.9rem;
            padding: 0.9rem;
            margin-bottom: 0.8rem;
            box-shadow: 0 6px 18px -6px rgba(38, 42, 71, 0.22);
            border-left: 4px solid var(--surat-primary);
            cursor: grab;
            transition: transform 0.18s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.18s ease, opacity 0.18s ease;
            animation: cardIn 0.3s ease both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .task-card:active { cursor: grabbing; }
        .task-card:hover { transform: translateY(-3px); box-shadow: 0 16px 30px -10px rgba(38, 42, 71, 0.32); }
        .task-card.dragging { opacity: 0.5; transform: scale(0.98) rotate(1.5deg); }
        .task-card.denied { cursor: not-allowed; opacity: 0.72; }
        .task-card .kbtn { font-size: 0.72rem; padding: 0.18rem 0.55rem; border-radius: 999px; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('task.kanban')" :subtitle="__('task.kanban_subtitle')" icon="bx-columns">
                <x-slot:actions>
                    <a href="{{ route('task.index') }}" class="btn btn-hero"><i class="bx bx-list-ul me-1"></i>{{ __('task.list') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        @unless($isStaff)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="text-muted small"><i class="bx bx-user me-1"></i>{{ __('delegation.staff') }}:</span>
                            <select name="staff_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                <option value="">{{ __('menu.general.all') }}</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ (int) $selectedStaff === $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            <noscript><button class="btn btn-primary btn-sm">{{ __('menu.general.filter') }}</button></noscript>
                        </form>
                    </div>
                </div>
            </div>
        @endunless

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="board-x">
                        @foreach(\App\Enums\TaskStatus::kanbanColumns() as $colKey => $colLabel)
                            @php($color = match ($colKey) {
                                'baru' => '#696cff',
                                'dalam_pengerjaan' => '#0f9d8a',
                                'menunggu_review' => '#f59e0b',
                                'selesai' => '#02bc7d',
                                default => '#696cff',
                            })
                            <div class="board-x-col" data-column="{{ $colKey }}" style="{{ !$isSekretaris && $colKey === 'selesai' ? 'pointer-events:none;' : '' }}">
                                <div class="board-x-head">
                                    <span class="dot" style="background:{{ $color }}"></span>
                                    <span style="color:{{ $color }}">{{ $colLabel }}</span>
                                    <span class="count">{{ ($columns[$colKey] ?? collect())->count() }}</span>
                                </div>
                                <div class="board-x-body">
                                    @forelse(($columns[$colKey] ?? collect()) as $task)
                                        <div class="task-card {{ !$isSekretaris && $colKey === 'selesai' ? 'denied' : '' }}"
                                             draggable="{{ (!$isSekretaris && $colKey === 'selesai') ? 'false' : 'true' }}"
                                             data-id="{{ $task->id }}">
                                            @if($task->delegation)
                                                <div class="text-muted mb-1" style="font-size:0.7rem;line-height:1.3;">
                                                    <i class="bx bx-link-alt me-1"></i>{{ \Illuminate\Support\Str::limit($task->delegation->title, 46) }}
                                                </div>
                                            @endif
                                            <a href="{{ route('task.show', $task->id) }}" class="text-decoration-none fw-semibold text-dark small d-inline-block">
                                                {{ $task->title }}
                                            </a>
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <span class="badge {{ \App\Enums\Priority::badge($task->priority) }}">{{ $task->priority_label }}</span>
                                                <span class="badge bg-label-info ms-auto">{{ $task->staff?->name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted"><i class="bx bx-calendar me-1"></i>{{ $task->formatted_deadline ?? '-' }}</small>
                                                <small class="fw-bold">{{ $task->progress }}%</small>
                                            </div>
                                            <div class="progress mt-1" style="height:5px;">
                                                <div class="progress-bar {{ $colKey === 'selesai' ? 'bg-success' : 'bg-primary' }}" style="width:{{ $task->progress }}%"></div>
                                            </div>

                                            @if(($isAdmin || $isSekretaris) && in_array($task->status, ['diterima', 'dalam_pengerjaan']))
                                                <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1">
                                                    <button type="button" class="btn btn-primary kbtn btn-review"
                                                            data-id="{{ $task->id }}"
                                                            data-title="{{ $task->title }}">
                                                        <i class="bx bx-send me-1"></i>{{ __('task.submit_review') }}
                                                    </button>
                                                </div>
                                            @endif
                                            @if($isSekretaris && $task->status === 'menunggu_review')
                                                <div class="mt-2 pt-2 border-top d-flex flex-wrap gap-1">
                                                    <button type="button" class="btn btn-success kbtn btn-verify"
                                                            data-id="{{ $task->id }}"
                                                            data-title="{{ $task->title }}">
                                                        <i class="bx bx-check-shield me-1"></i>{{ __('task.verify') }}
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="board-x-empty">{{ __('task.empty') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bx bx-check-shield text-success me-2"></i>{{ __('task.verify') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-lg rounded-circle bg-label-success mx-auto d-grid place-items-center mb-2">
                            <i class="bx bxs-check-shield fs-1 text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-1">APAKAH YAKIN INGIN MEMVERIFIKASI</h4>
                        <p class="text-muted mb-0" id="verifyTaskTitle"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                    <button type="button" class="btn btn-success" id="verifyConfirmBtn"><i class="bx bx-check me-1"></i>{{ __('task.verify') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            var cols = document.querySelectorAll('.board-x-col');

            function updateCounts() {
                cols.forEach(function (col) {
                    var badge = col.querySelector('.count');
                    if (badge) badge.textContent = col.querySelectorAll('.task-card:not(.dragging)').length;
                });
            }

            var csrf = '{{ csrf_token() }}';
            var baseUrl = '{{ route("task.kanban.update", ["task" => "__ID__"]) }}';
            var isSekretaris = {{ $isSekretaris ? 'true' : 'false' }};

            function apiMove(taskId, status) {
                return fetch(baseUrl.replace('__ID__', taskId), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: status })
                }).then(function (res) {
                    return res.json().then(function (data) { return { ok: res.ok, data: data }; });
                });
            }

            function toast(msg, ok) {
                var el = document.createElement('div');
                el.className = 'position-fixed top-0 start-50 translate-middle-x alert ' + (ok ? 'alert-success' : 'alert-danger') + ' shadow';
                el.style.zIndex = '3000';
                el.style.marginTop = '1rem';
                el.textContent = msg;
                document.body.appendChild(el);
                setTimeout(function () { el.remove(); }, 2500);
            }

            var card = null;

            document.querySelectorAll('.task-card[draggable="true"]').forEach(function (el) {
                el.addEventListener('dragstart', function (e) {
                    card = el;
                    el.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', el.dataset.id);
                });
                el.addEventListener('dragend', function () {
                    el.classList.remove('dragging');
                    card = null;
                    cols.forEach(function (c) { c.classList.remove('drag-over'); });
                });
            });

            cols.forEach(function (col) {
                if (col.style.pointerEvents === 'none') return;
                col.addEventListener('dragover', function (e) { e.preventDefault(); col.classList.add('drag-over'); });
                col.addEventListener('dragleave', function () { col.classList.remove('drag-over'); });
                col.addEventListener('drop', function (e) {
                    e.preventDefault();
                    col.classList.remove('drag-over');
                    if (!card) return;

                    var targetStatus = col.dataset.column;
                    var currentStatus = card.closest('.board-x-col').dataset.column;
                    if (targetStatus === currentStatus) return;

                    if (targetStatus === 'selesai' && !isSekretaris) {
                        toast('{{ __("delegation.forbidden") }}', false);
                        card.classList.add('dragging');
                        setTimeout(function () { card.classList.remove('dragging'); }, 300);
                        return;
                    }

                    var taskId = card.dataset.id;

                    apiMove(taskId, targetStatus).then(function (res) {
                        if (res.ok && res.data.status) {
                            col.querySelector('.board-x-body').appendChild(card);
                            updateCounts();
                        } else {
                            toast((res.data && res.data.message) || '{{ __("menu.general.error") }}', false);
                            window.location.reload();
                        }
                    }).catch(function () { window.location.reload(); });
                });
            });

            var verifyModalEl = document.getElementById('verifyModal');
            var verifyConfirmBtn = document.getElementById('verifyConfirmBtn');
            var pendingVerify = null;

            if (verifyModalEl && window.bootstrap) {
                document.querySelectorAll('.btn-verify').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        pendingVerify = btn.dataset.id;
                        document.getElementById('verifyTaskTitle').textContent = btn.dataset.title;
                        bootstrap.Modal.getOrCreateInstance(verifyModalEl).show();
                    });
                });

                verifyConfirmBtn.addEventListener('click', function () {
                    if (!pendingVerify) return;
                    var id = pendingVerify;
                    pendingVerify = null;
                    bootstrap.Modal.getOrCreateInstance(verifyModalEl).hide();
                    apiMove(id, 'selesai').then(function (res) {
                        if (res.ok && res.data.status) {
                            toast('{{ __("menu.general.success") }}', true);
                            setTimeout(function () { window.location.reload(); }, 500);
                        } else {
                            toast((res.data && res.data.message) || '{{ __("menu.general.error") }}', false);
                        }
                    }).catch(function () {});
                });
            }

            document.querySelectorAll('.btn-review').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var id = btn.dataset.id;
                    btn.disabled = true;
                    apiMove(id, 'menunggu_review').then(function (res) {
                        btn.disabled = false;
                        if (res.ok && res.data.status) {
                            toast('{{ __("menu.general.success") }}', true);
                            setTimeout(function () { window.location.reload(); }, 500);
                        } else {
                            toast((res.data && res.data.message) || '{{ __("menu.general.error") }}', false);
                        }
                    }).catch(function () { btn.disabled = false; });
                });
            });
        })();
    </script>
@endpush