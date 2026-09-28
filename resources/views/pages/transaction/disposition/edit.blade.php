@extends('layout.main')

@section('content')
    <x-breadcrumb
        :values="[__('menu.transaction.menu'), $letter->reference_number, __('menu.transaction.disposition_letter'), __('menu.general.edit')]">
    </x-breadcrumb>

    <style>
        .dispo-opt .form-check-input {
            width: 1.15em;
            height: 1.15em;
            margin-top: .3em;
            cursor: pointer;
        }
        .dispo-opt .form-check {
            padding: .65rem .75rem .65rem 2.1rem;
            margin: 0;
            border-radius: .65rem;
            transition: background-color .15s ease, box-shadow .15s ease;
        }
        .dispo-opt .form-check:hover {
            background: rgba(109, 103, 228, .05);
        }
        .dispo-opt .form-check-input:checked + .form-check-label {
            font-weight: 600;
        }
        .letter-tile {
            border: 1px solid #e3e5ef;
            border-radius: .75rem;
            padding: .9rem 1rem;
            background: #fbfbfe;
            height: 100%;
        }
        .letter-tile .tile-label {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b6f8d;
            font-weight: 700;
        }
        .letter-tile .tile-value {
            font-size: 1.05rem;
            font-weight: 700;
            color: #26324e;
            word-break: break-word;
        }
        .section-head {
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .section-head .sec-ico {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 17px;
            flex-shrink: 0;
        }
    </style>

    <div class="alert alert-primary alert-dismissible" role="alert">
        {{ __('model.disposition.notice_me', ['reference_number' => $letter->reference_number]) }} <a
            href="{{ route('transaction.incoming.show', $letter) }}" class="fw-bold">{{ __('menu.general.view') }}</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    {{-- Header surat --}}
    <div class="card mb-4 position-relative overflow-hidden">
        <div class="card-body py-md-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                <div class="flex-shrink-0 rounded-4 p-1 border"
                     style="width:58px;height:58px;background:#fff;box-shadow:0 4px 14px rgba(20,23,60,.14);display:grid;place-items:center;">
                    <img src="{{ asset('img/logo/sumaterautara.png') }}" alt="Logo Sumatera Utara"
                         style="width:44px;height:44px;object-fit:contain;">
                </div>
                <div class="flex-grow-1">
                    <div class="text-uppercase small text-secondary fw-bold mb-1">{{ __('menu.transaction.incoming_letter') }}</div>
                    <h4 class="mb-1 fw-bold text-wrap">{{ $letter->reference_number }}</h4>
                    <div class="text-secondary">{{ __('model.letter.from') }}:
                        <span class="fw-semibold text-dark">{{ $letter->from }}</span>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="letter-tile">
                        <div class="tile-label">{{ __('model.letter.agenda_number') }}</div>
                        <div class="tile-value font-monospace">{{ $letter->agenda_number }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="letter-tile">
                        <div class="tile-label">{{ __('model.disposition.letter_date') }}</div>
                        <div class="tile-value">{{ $letter->formatted_letter_date }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="letter-tile">
                        <label for="received_at" class="tile-label d-block">{{ __('model.disposition.received_at') }}</label>
                        <input type="date" id="received_at" name="received_at"
                               class="form-control form-control-lg border-0 p-0 tile-value fw-semibold {{ $errors->has('received_at') ? 'is-invalid' : '' }}"
                               value="{{ old('received_at', $data->received_at?->format('Y-m-d')) }}">
                        <span class="error invalid-feedback">{{ $errors->first('received_at') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <form action="{{ route('transaction.disposition.update', [$letter, $data]) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Data disposisi --}}
            <div class="card-header py-3 border-bottom">
                <h6 class="mb-0 fw-bold section-head">
                    <span class="sec-ico" style="background:linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-share-alt"></i></span>
                    {{ __('menu.transaction.disposition_letter') }}
                </h6>
            </div>
            <div class="card-body row g-3">
                <div class="col-sm-12 col-md-6 col-xl-4">
                    <div class="mb-3">
                        <label for="to" class="form-label">{{ __('model.disposition.to') }}</label>
                        <input type="text" id="to" name="to"
                               class="form-control form-control-lg @error('to') is-invalid @enderror"
                               value="{{ old('to', $data->to) }}"
                               placeholder="{{ __('model.disposition.forwarded_to_options.sekretaris') }}">
                        <span class="error invalid-feedback">{{ $errors->first('to') }}</span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-4">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">{{ __('model.disposition.due_date') }}</label>
                        <input type="date" id="due_date" name="due_date"
                               class="form-control form-control-lg @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date', $data->due_date?->format('Y-m-d')) }}">
                        <span class="error invalid-feedback">{{ $errors->first('due_date') }}</span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-xl-4">
                    <div class="mb-3">
                        <label for="letter_status" class="form-label">{{ __('model.disposition.status') }}</label>
                        <select class="form-select form-select-lg" id="letter_status" name="letter_status">
                            @foreach($statuses as $status)
                                <option
                                    value="{{ $status->id }}"
                                    @selected(old('letter_status', $data->letter_status) == $status->id)>
                                    {{ $status->status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="content" class="form-label">{{ __('model.disposition.content') }}</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content"
                                  name="content" rows="3">{{ old('content', $data->content) }}</textarea>
                        <span class="error invalid-feedback">{{ $errors->first('content') }}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="note" class="form-label">{{ __('model.disposition.note') }}</label>
                        <input type="text" id="note" name="note"
                               class="form-control @error('note') is-invalid @enderror"
                               value="{{ old('note', $data->note ?? '') }}">
                        <span class="error invalid-feedback">{{ $errors->first('note') }}</span>
                    </div>
                </div>
            </div>

            {{-- Opsi lembar disposisi --}}
            <div class="card-header py-3 border-bottom">
                <h6 class="mb-0 fw-bold section-head">
                    <span class="sec-ico" style="background:linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-note"></i></span>
                    {{ __('model.disposition.options.direction') }}
                </h6>
            </div>
            <div class="card-body row g-3">
                <div class="col-12 col-lg-6">
                    <div class="h-100">
                        <label class="form-label">{{ __('model.disposition.forwarded_to') }}</label>
                        <div class="form-text mb-3">{{ __('model.disposition.forwarded_to_hint') }}</div>
                        @php($selectedForwarded = old('forwarded_to', $data->forwarded_to ?? []))
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="row g-2">
                                @foreach(__('model.disposition.forwarded_to_options') as $key => $label)
                                    <div class="col-12">
                                        <div class="form-check dispo-opt">
                                            <input class="form-check-input" type="checkbox" name="forwarded_to[]"
                                                   value="{{ $key }}" id="forwarded_to_{{ $key }}"
                                                   @checked(in_array($key, $selectedForwarded))>
                                            <label class="form-check-label" for="forwarded_to_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr class="my-2">
                            <div class="form-check dispo-opt mb-2">
                                <input class="form-check-input" type="checkbox" name="forwarded_to[]"
                                       value="upt_custom" id="forwarded_to_upt_custom"
                                       @checked(in_array('upt_custom', $selectedForwarded) || old('forwarded_to_custom', $data->forwarded_to_custom))>
                                <label class="form-check-label" for="forwarded_to_upt_custom">{{ __('model.disposition.forwarded_to_custom') }}</label>
                                <input type="text" class="form-control mt-2 {{ $errors->has('forwarded_to_custom') ? 'is-invalid' : '' }}"
                                       name="forwarded_to_custom"
                                       value="{{ old('forwarded_to_custom', $data->forwarded_to_custom) }}"
                                       placeholder="{{ __('model.disposition.forwarded_to_custom_placeholder') }}">
                                <span class="error invalid-feedback">{{ $errors->first('forwarded_to_custom') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="h-100">
                        <label class="form-label">{{ __('model.disposition.honor') }}</label>
                        <div class="form-text mb-3">{{ __('model.disposition.honor_hint') }}</div>
                        @php($selectedHonor = old('honor', $data->honor ?? []))
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="row g-2">
                                @foreach(__('model.disposition.honor_options') as $key => $label)
                                    <div class="col-12">
                                        <div class="form-check dispo-opt">
                                            <input class="form-check-input" type="checkbox" name="honor[]"
                                                   value="{{ $key }}" id="honor_{{ $key }}"
                                                   @checked(in_array($key, $selectedHonor))>
                                            <label class="form-check-label" for="honor_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr class="my-2">
                            <div class="form-check dispo-opt mb-2">
                                <input class="form-check-input" type="checkbox" name="honor[]"
                                       value="custom" id="honor_custom_check"
                                       @checked(in_array('custom', $selectedHonor) || old('honor_custom', $data->honor_custom))>
                                <label class="form-check-label" for="honor_custom_check">{{ __('model.disposition.honor_custom') }}</label>
                                <input type="text" class="form-control mt-2 {{ $errors->has('honor_custom') ? 'is-invalid' : '' }}"
                                       name="honor_custom"
                                       value="{{ old('honor_custom', $data->honor_custom) }}"
                                       placeholder="{{ __('model.disposition.honor_custom_placeholder') }}">
                                <span class="error invalid-feedback">{{ $errors->first('honor_custom') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="instruction" class="form-label">{{ __('model.disposition.instruction') }}</label>
                        <textarea class="form-control @error('instruction') is-invalid @enderror" id="instruction"
                                  name="instruction" rows="4"
                                  placeholder="{{ __('model.disposition.instruction_placeholder') }}">{{ old('instruction', $data->instruction) }}</textarea>
                        <span class="error invalid-feedback">{{ $errors->first('instruction') }}</span>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('transaction.disposition.index', $letter) }}" class="btn btn-outline-secondary">
                    {{ __('menu.general.back') }}
                </a>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bx bx-save me-1"></i>{{ __('menu.general.update') }}
                </button>
            </div>
        </form>
    </div>
@endsection