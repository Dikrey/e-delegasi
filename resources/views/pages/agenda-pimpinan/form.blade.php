@extends('layout.main')

@push('style')
    <style>
        .ag-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <x-page-hero :title="$edit ? __('agenda.edit') : __('agenda.create_btn')" icon="bx-calendar-event"></x-page-hero>
        </div>
        <div class="col-12 col-lg-9">
            <form method="POST" action="{{ $edit ? route('agenda-pimpinan.update', $edit->id) : route('agenda-pimpinan.store') }}">
                @csrf
                @if($edit) @method('PUT') @endif

                <div class="card ag-card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('agenda.title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required value="{{ old('title', $edit->title ?? '') }}" placeholder="{{ __('agenda.title_placeholder') }}">
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('agenda.type') }}</label>
                                <select name="agenda_type" class="form-select">
                                    @foreach(__('enums.agenda_type') as $key => $label)
                                        <option value="{{ $key }}" {{ old('agenda_type', $edit->agenda_type ?? 'rapat') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('agenda.delegation_link') }}</label>
                                <select name="delegation_id" class="form-select">
                                    <option value="">— {{ __('menu.general.none') }} —</option>
                                    @foreach($delegations as $delegation)
                                        <option value="{{ $delegation->id }}" {{ old('delegation_id', $edit->delegation_id ?? '') == $delegation->id ? 'selected' : '' }}>
                                            {{ $delegation->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('agenda.date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" required value="{{ old('date', $edit?->date?->format('Y-m-d') ?? '') }}">
                                @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('agenda.start_time') }}</label>
                                <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $edit->start_time ?? '09:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('agenda.end_time') }}</label>
                                <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $edit->end_time ?? '11:00') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('agenda.location') }}</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $edit->location ?? '') }}" placeholder="{{ __('agenda.location_placeholder') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('agenda.note') }}</label>
                                <input type="text" name="note" class="form-control" value="{{ old('note', $edit->note ?? '') }}" placeholder="{{ __('agenda.note_placeholder') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('agenda.description') }}</label>
                                <textarea name="description" rows="4" class="form-control">{{ old('description', $edit->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <a href="{{ route('agenda-pimpinan.list') }}" class="btn btn-outline-secondary">{{ __('menu.general.back') }}</a>
                    <button type="submit" class="btn btn-primary px-4">{{ $edit ? __('menu.general.update') : __('agenda.create_btn') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection