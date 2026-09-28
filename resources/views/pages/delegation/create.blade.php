@extends('layout.main')

@push('style')
    <style>
        .form-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
        .section-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 700; margin-bottom: 1rem; }
        .section-title .ix { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 9px; background: linear-gradient(135deg,#6d67e4,#5448d6); color: #fff; font-size: 14px; }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <x-page-hero :title="$edit ? __('delegation.edit') : __('delegation.create')"
                         :subtitle="__('delegation.form_subtitle')" icon="bx-task">
            </x-page-hero>
        </div>
        <div class="col-12 col-lg-10">
            <form method="POST" action="{{ $edit ? route('delegation.update', $edit->id) : route('delegation.store') }}" enctype="multipart/form-data" class="needs-validation">
                @csrf
                @if($edit) @method('PUT') @endif

                <div class="card form-card mb-4">
                    <div class="card-body">
                        <div class="section-title"><span class="ix">1</span>{{ __('delegation.form.info') }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required value="{{ old('title', $edit->title ?? request('title')) }}" placeholder="{{ __('delegation.title_placeholder') }}">
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.source_letter') }}</label>
                                <select name="letter_id" class="form-select" id="letterSelect">
                                    <option value="">— {{ __('delegation.no_source_letter') }} —</option>
                                    @foreach($letters as $letter)
                                        <option value="{{ $letter->id }}"
                                            {{ (old('letter_id', $selectedLetter?->id) == $letter->id) ? 'selected' : '' }}>
                                            {{ $letter->reference_number }} — {{ $letter->description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_id') <small class="text-danger">{{ $message }}</small> @enderror

                                @if(!$letters->isEmpty())
                                    <small class="text-muted d-block mt-1" id="letterHint">{{ __('delegation.letter_hint') }}</small>
                                @else
                                    <small class="text-warning d-block mt-1">{{ __('delegation.no_letter_available') }}</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.priority') }} <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    @foreach(\App\Enums\Priority::options() as $key => $label)
                                        <option value="{{ $key }}" {{ old('priority', $edit->priority ?? 'normal') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.deadline') }}</label>
                                <input type="datetime-local" name="deadline" class="form-control"
                                       value="{{ old('deadline', isset($edit) && $edit->deadline ? $edit->deadline->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.task_category') }}</label>
                                <select name="task_category_id" class="form-select">
                                    <option value="">— {{ __('menu.general.none') }} —</option>
                                    @foreach(\App\Models\TaskCategory::orderBy('name')->get() as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('task_category_id', isset($edit) && $edit->tasks->first() ? $edit->tasks->first()->task_category_id : null) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('delegation.description') }}</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $edit->description ?? '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('delegation.instruction') }}</label>
                                <textarea name="instruction" class="form-control" rows="3" placeholder="{{ __('delegation.instruction_placeholder') }}">{{ old('instruction', $edit->instruction ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.attachment') }}</label>
                                <input type="file" name="attachment" class="form-control">
                                @if($edit && $edit->attachment)
                                    <small class="text-muted mt-1 d-block"><i class="bx bx-paperclip me-1"></i><a href="{{ asset($edit->attachment) }}" target="_blank">{{ basename($edit->attachment) }}</a></small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('delegation.staff') }} <span class="text-danger">*</span></label>
                                <select name="staff_ids[]" class="form-select" multiple size="5" required>
                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ in_array($staff->id, old('staff_ids', $edit ? $edit->tasks->pluck('staff_id')->toArray() : [])) ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('delegation.staff_multiple_hint') }}</small>
                                @error('staff_ids') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card form-card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0"><span class="ix">2</span>{{ __('delegation.form.agenda') }}</div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="addAgendaToggle" name="add_to_agenda" value="1"
                                       {{ old('add_to_agenda', isset($edit) && $edit->agenda ? 1 : 0) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="addAgendaToggle">{{ __('delegation.add_to_agenda') }}</label>
                            </div>
                        </div>
                        <div id="agendaFields" style="{{ old('add_to_agenda', isset($edit) && $edit->agenda ? 1 : 0) ? '' : 'display:none;' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ __('delegation.agenda_title') }}</label>
                                    <input type="text" name="agenda_title" class="form-control" id="agendaTitle" value="{{ old('agenda_title', $edit?->agenda?->title ?? '') }}" placeholder="{{ __('delegation.agenda_title_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ __('delegation.agenda_type') }}</label>
                                    <select name="agenda_type" class="form-select">
                                        @foreach(__('enums.agenda_type') as $key => $label)
                                            <option value="{{ $key }}" {{ old('agenda_type', $edit?->agenda?->agenda_type ?? 'rapat') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">{{ __('delegation.agenda_date') }}</label>
                                    <input type="date" name="agenda_date" class="form-control" value="{{ old('agenda_date', $edit?->agenda?->date?->format('Y-m-d') ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">{{ __('delegation.start_time') }}</label>
                                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $edit?->agenda?->start_time ?? '09:00') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">{{ __('delegation.end_time') }}</label>
                                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $edit?->agenda?->end_time ?? '11:00') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ __('delegation.location') }}</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location', $edit?->agenda?->location ?? '') }}" placeholder="{{ __('delegation.location_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ __('delegation.note') }}</label>
                                    <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @unless($edit)
                <div class="card form-card mb-4">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="send_now" value="1" id="sendNow" {{ old('send_now', 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="sendNow">{{ __('delegation.send_now') }}</label>
                        </div>
                        <small class="text-muted">{{ __('delegation.send_now_hint') }}</small>
                    </div>
                </div>
                @endunless

                <div class="d-flex justify-content-between mb-4">
                    <a href="{{ route('delegation.index') }}" class="btn btn-outline-secondary">{{ __('menu.general.back') }}</a>
                    <button type="submit" class="btn btn-primary px-4">{{ $edit ? __('menu.general.update') : __('delegation.create_btn') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            var toggle = document.getElementById('addAgendaToggle');
            var fields = document.getElementById('agendaFields');
            var titleInput = document.querySelector('input[name="title"]');
            var agendaTitle = document.getElementById('agendaTitle');

            if (toggle && fields) {
                toggle.addEventListener('change', function () {
                    fields.style.display = toggle.checked ? '' : 'none';
                });
            }

            if (titleInput && agendaTitle) {
                titleInput.addEventListener('input', function () {
                    if (!agendaTitle.value || agendaTitle.dataset.auto === '1' || !agendaTitle.value.trim()) {
                        agendaTitle.value = titleInput.value.trim();
                        agendaTitle.dataset.auto = '1';
                    }
                });
                agendaTitle.addEventListener('input', function () { agendaTitle.dataset.auto = '0'; });
            }

            var letterSelect = document.getElementById('letterSelect');
            var hint = document.getElementById('letterHint');
            if (letterSelect) {
                letterSelect.addEventListener('change', function () {
                    if (hint) {
                        hint.textContent = this.value
                            ? @json(__('delegation.letter_selected').'. ')
                            : @json(__('delegation.letter_hint').'. ');
                    }
                });
            }
        })();
    </script>
@endpush