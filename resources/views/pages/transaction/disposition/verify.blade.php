@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), $letter->reference_number, __('menu.transaction.disposition_letter'), __('model.disposition.verify_sheet')]">
    </x-breadcrumb>

    <style>
        .dl-info {
            margin-bottom: 0;
        }
        .dl-info dt {
            font-weight: 600;
            color: #6b6f8d;
        }
        .dl-info dd {
            color: #26324e;
            word-break: break-word;
        }
        .verify-radio .form-check-input {
            cursor: pointer;
        }
    </style>

    <div class="alert alert-info alert-dismissible" role="alert">
        <i class="bx bx-info-circle me-1"></i>
        {{ __('model.disposition.notice_me', ['reference_number' => $letter->reference_number]) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row g-4">
        {{-- Ringkasan disposisi --}}
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bx bx-note me-1"></i>{{ __('model.disposition.print_title') }}</h6>
                </div>
                <div class="card-body">
                    <dl class="row dl-info g-2 g-md-3">
                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.reference_number') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->letter?->reference_number }}</dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.letter_date') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->letter?->formatted_letter_date }}</dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.received_at') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->formatted_received_at ?: '-' }}</dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.due_date') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->formatted_due_date }}</dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.content') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->content }}</dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.forwarded_to') }}</dt>
                        <dd class="col-7 col-md-8">
                            @forelse($data->forwarded_labels as $label)
                                <span class="badge bg-label-primary me-1 mb-1">{{ $label }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.honor') }}</dt>
                        <dd class="col-7 col-md-8">
                            @forelse($data->honor_labels as $label)
                                <span class="badge bg-label-info me-1 mb-1">{{ $label }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </dd>

                        <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.instruction') }}</dt>
                        <dd class="col-7 col-md-8">{{ $data->instruction ?: __('model.disposition.options.no_instruction') }}</dd>

                        @if($data->note)
                            <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.note') }}</dt>
                            <dd class="col-7 col-md-8">{{ $data->note }}</dd>
                        @endif

                        @if($data->verified_at)
                            <dt class="col-5 col-md-4 text-break">{{ __('model.disposition.verified_at') }}</dt>
                            <dd class="col-7 col-md-8">{{ $data->verified_at->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</dd>
                        @endif
                    </dl>

                    <hr>

                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-outline-primary"
                           href="{{ route('transaction.disposition.print', [$letter, $data]) }}"
                           target="_blank">
                            <i class="bx bx-printer me-1"></i>{{ __('model.disposition.print_sheet') }}
                        </a>
                        <a class="btn btn-outline-secondary"
                           href="{{ route('transaction.disposition.index', $letter) }}">
                            {{ __('menu.general.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form verifikasi --}}
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bx bx-shield-quarter me-1"></i>{{ __('model.disposition.verify_sheet') }}</h6>
                </div>

                @if($data->verified_at)
                    <div class="card-body alert-success rounded-3 m-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-glyph" style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#36f1a5,#00b874);display:grid;place-items:center;color:#fff;">
                                <i class="bx bx-check-double" style="font-size:22px;"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ __('model.disposition.verified') }}</div>
                                <small class="text-muted">
                                    {{ $data->verifier?->name }} &middot; {{ $data->verified_at->isoFormat('D MMMM YYYY, HH:mm') }}
                                </small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row small mb-1">
                            <span class="col-6 text-muted">{{ __('model.disposition.is_received') }}</span>
                            <span class="col-6 fw-semibold text-end">{{ $data->is_received ? __('model.disposition.received_yes') : __('model.disposition.received_no') }}</span>
                        </div>
                        <div class="row small mb-1">
                            <span class="col-6 text-muted">{{ __('model.disposition.options.direction') }}</span>
                            <span class="col-6 fw-semibold text-end">{{ $data->direction }}</span>
                        </div>
                        @if($data->verification_note)
                            <div class="small">
                                <span class="text-muted">{{ __('model.disposition.verification_note') }}:</span>
                                <div>{{ $data->verification_note }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('transaction.disposition.verified', [$letter, $data]) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- Arah --}}
                        <div class="mb-3">
                            <label for="direction" class="form-label">{{ __('model.disposition.options.direction') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('direction') is-invalid @enderror"
                                   id="direction" name="direction" list="direction-options"
                                   value="{{ old('direction', $data->direction) }}"
                                   placeholder="{{ __('model.disposition.forwarded_to_options.sekretaris') . ' / ...' }}">
                            <datalist id="direction-options">
                                @foreach(__('model.disposition.forwarded_to_options') as $label)
                                    <option value="{{ $label }}"></option>
                                @endforeach
                                @if($data->forwarded_to_custom)
                                    <option value="{{ $data->forwarded_to_custom }}"></option>
                                @endif
                            </datalist>
                            <span class="error invalid-feedback">{{ $errors->first('direction') }}</span>
                        </div>

                        {{-- Diterima / tidak --}}
                        <div class="mb-3">
                            <label class="form-label">{{ __('model.disposition.is_received') }} <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check verify-radio">
                                    <input class="form-check-input @error('is_received') is-invalid @enderror"
                                           type="radio" name="is_received" id="received_yes"
                                           value="1"
                                           @checked(old('is_received', $data->is_received === true))>
                                    <label class="form-check-label" for="received_yes">
                                        <i class="bx bx-check-circle text-success me-1"></i>{{ __('model.disposition.received_yes') }}
                                    </label>
                                </div>
                                <div class="form-check verify-radio">
                                    <input class="form-check-input @error('is_received') is-invalid @enderror"
                                           type="radio" name="is_received" id="received_no"
                                           value="0"
                                           @checked(old('is_received', $data->is_received === false))>
                                    <label class="form-check-label" for="received_no">
                                        <i class="bx bx-x-circle text-danger me-1"></i>{{ __('model.disposition.received_no') }}
                                    </label>
                                </div>
                            </div>
                            <span class="error invalid-feedback">{{ $errors->first('is_received') }}</span>
                        </div>

                        {{-- Catatan verifikasi --}}
                        <div class="mb-3">
                            <label for="verification_note" class="form-label">{{ __('model.disposition.verification_note') }}</label>
                            <textarea class="form-control @error('verification_note') is-invalid @enderror"
                                      id="verification_note" name="verification_note" rows="3"
                                      placeholder="{{ __('model.disposition.instruction_placeholder') }}">{{ old('verification_note', $data->verification_note) }}</textarea>
                            <span class="error invalid-feedback">{{ $errors->first('verification_note') }}</span>
                        </div>
                    </div>
                    <div class="card-footer pt-0 d-flex gap-2 flex-wrap">
                        <button class="btn btn-success" type="submit">
                            <i class="bx bx-check-double me-1"></i>{{ __('model.disposition.verify_sheet') }}
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ route('transaction.disposition.index', $letter) }}">
                            {{ __('menu.general.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection