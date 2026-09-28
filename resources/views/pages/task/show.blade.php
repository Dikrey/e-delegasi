@extends('layout.main')

@push('style')
    <style>
        .tk-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38, 42, 71, 0.3); }
        .pbar-lg { height: 12px; border-radius: 999px; }
        .tk-update { transition: background-color .2s ease; border-radius: .75rem; }
        .tk-update:hover { background: rgba(105, 108, 255, .04); }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="$data->title" :subtitle="$data->delegation?->title" icon="bx-list-check">
                <x-slot:actions>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">{{ $data->status_label }}</span>
                    @if(in_array($data->status, ['baru', 'ditolak']) && $canManage)
                        <form action="{{ route('task.accept', $data->id) }}" method="post" class="d-inline">
                            @csrf
                            <button class="btn btn-hero"><i class="bx bx-check-circle me-1"></i>{{ __('task.accept') }}</button>
                        </form>
                        <form action="{{ route('task.reject', $data->id) }}" method="post" class="d-inline"
                              onsubmit="return confirm('{{ __('menu.general.delete_confirm') }}');">
                            @csrf
                            <button class="btn btn-hero"><i class="bx bx-x-circle me-1"></i>{{ __('task.reject') }}</button>
                        </form>
                    @endif
                    @if($data->status == 'menunggu_review' && $isSekretaris)
                        <button type="button" class="btn btn-hero" data-bs-toggle="modal" data-bs-target="#verifyModal">
                            <i class="bx bx-check-shield me-1"></i>{{ __('task.verify') }}
                        </button>
                    @endif
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-lg-7">
            <div class="card tk-card mb-4">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('task.overview') }}</h5></div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-task text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.status') }}</small>
                                    <span class="fw-semibold">{{ $data->status_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-user text-info fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('task.assigned_to') }}</small>
                                    <span class="fw-semibold">{{ $data->staff?->name }}</span>
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
                                <i class="bx bx-category text-info fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.task_category') }}</small>
                                    @if($data->taskCategory)
                                        <span class="badge rounded-pill" style="background:{{ $data->taskCategory->color }};color:#fff;">{{ $data->taskCategory->name }}</span>
                                    @else
                                        <span class="fw-semibold">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <i class="bx bx-calendar text-danger fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('delegation.deadline') }}</small>
                                    <span class="fw-semibold">{{ $data->formatted_deadline ?? '-' }}</span>
                                    @if($data->deadline_status_label)
                                        <span class="badge {{ $data->deadline_status_badge }} ms-1">{{ $data->deadline_status_label }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-semibold">{{ __('delegation.progress') }}</small>
                                <strong class="fs-5 text-primary">{{ $data->progress }}%</strong>
                            </div>
                            <div class="progress pbar-lg"><div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:{{ $data->progress }}%"></div></div>
                        </div>
                    </div>
                    @if($data->description)
                        <hr>
                        <label class="form-label fw-semibold text-muted">{{ __('delegation.description') }}</label>
                        <p class="mb-0">{!! nl2br(e($data->description)) !!}</p>
                    @endif
                    @if($data->attachment)
                        <a href="{{ route('task.download', $data->id) }}" class="btn btn-sm btn-outline-primary mt-3"><i class="bx bx-download me-1"></i>{{ __('task.download_attachment') }}</a>
                    @endif
                </div>
            </div>

            <div class="card tk-card">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('task.progress_updates') }}</h5></div>
                <div class="card-body">
                    @forelse($data->updates as $update)
                        <div class="d-flex gap-3 py-3 border-bottom tk-update">
                            <div class="avatar avatar-sm rounded-circle bg-primary text-white d-grid place-items-center fw-bold">{{ strtoupper(substr($update->user?->name ?? '?', 0, 1)) }}</div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between flex-wrap gap-1">
                                    <strong class="small">{{ $update->user?->name }}</strong>
                                    <small class="text-muted">{{ $update->created_at?->isoFormat('DD MMM, HH:mm') }}</small>
                                </div>
                                <span class="badge bg-label-primary my-1">{{ __('delegation.progress') }}: {{ $update->progress }}%</span>
                                @if($update->note)
                                    <p class="text-muted mb-1 small">{{ $update->note }}</p>
                                @endif
                                @if($update->document)
                                    <div class="mt-1 d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-primary"
                                                data-preview-url="{{ asset($update->document) }}"
                                                data-preview-name="{{ basename($update->document) }}">
                                            <i class="bx bx-show me-1"></i>{{ __('menu.general.preview') }}
                                        </button>
                                        <a href="{{ route('task.update-download', [$data->id, $update->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-download me-1"></i>{{ __('task.download_work_document') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">{{ __('task.no_updates') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @if($canManage)
                <div class="card tk-card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">{{ __('task.update_progress') }}</h5>
                        <span class="badge bg-label-primary">{{ $data->progress }}%</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('task.progress', $data->id) }}" enctype="multipart/form-data">
                            @csrf
                            <label class="form-label fw-semibold">{{ __('task.new_progress') }}</label>
                            <input type="range" class="form-range" id="progressRange" name="progress" min="0" max="100" step="5" value="{{ $data->progress }}">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">0%</span>
                                <strong class="text-primary" id="progressValue">{{ $data->progress }}%</strong>
                                <span class="text-muted small">100%</span>
                            </div>

                            <label class="form-label fw-semibold">{{ __('task.note') }}</label>
                            <textarea name="note" rows="3" class="form-control mb-2" placeholder="{{ __('task.note_placeholder') }}">{{ old('note') }}</textarea>
                            @error('note') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror

                            <label class="form-label fw-semibold">{{ __('task.work_document') }}</label>
                            <input type="file" name="document" class="form-control mb-1" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                            <small class="text-muted d-block mb-2">{{ __('task.work_document_hint') }}</small>
                            @error('document') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="action" value="update"><i class="bx bx-save me-1"></i>{{ __('menu.general.save') }}</button>
                                @if($data->progress == 100)
                                    <button type="submit" class="btn btn-success" name="action" value="review"><i class="bx bx-send me-1"></i>{{ __('task.submit_review') }}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card tk-card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-info-circle text-muted" style="font-size:2.4rem;"></i>
                        <p class="text-muted mb-0 mt-2">{{ __('delegation.forbidden') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($isSekretaris && $data->status == 'menunggu_review')
        <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('task.verify', $data->id) }}">
                    @csrf
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
                            <p class="text-muted mb-0">{{ $data->title }}</p>
                        </div>
                        <label class="form-label fw-semibold">{{ __('task.note') }}</label>
                        <textarea name="note" rows="2" class="form-control" placeholder="{{ __('task.note_placeholder') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
                        <button type="submit" class="btn btn-success"><i class="bx bx-check me-1"></i>{{ __('task.verify') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        (function () {
            var range = document.getElementById('progressRange');
            var value = document.getElementById('progressValue');
            if (range) {
                range.addEventListener('input', function () { value.textContent = this.value + '%'; });
            }
        })();
    </script>
@endpush