@extends('layout.main')

@push('style')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-theme.css') }}?v=2">
    <style>
        #agenda-detail-calendar { min-height: 520px; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="$data->title" icon="bx-calendar-event">
                <x-slot:actions>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">{{ $data->agenda_status_label }}</span>
                    @if($data->status == 'terjadwal')
                        <form action="{{ route('agenda-pimpinan.status', $data->id) }}" method="post" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="selesai">
                            <button class="btn btn-hero"><i class="bx bx-check-circle me-1"></i>{{ __('agenda.mark_done') }}</button>
                        </form>
                        <a href="{{ route('agenda-pimpinan.edit', $data->id) }}" class="btn btn-hero"><i class="bx bx-edit me-1"></i>{{ __('menu.general.edit') }}</a>
                    @endif
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-lg-8">
            <div class="card ag-card">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('delegation.detail_info') }}</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2"><i class="bx bx-tag text-primary fs-4"></i>
                                <div><small class="text-muted d-block">{{ __('agenda.type') }}</small><span class="fw-semibold">{{ $data->agenda_type_label }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2"><i class="bx bx-calendar text-danger fs-4"></i>
                                <div><small class="text-muted d-block">{{ __('agenda.date') }}</small><span class="fw-semibold">{{ $data->date?->isoFormat('DDDD, DD MMMM YYYY') }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2"><i class="bx bx-time text-info fs-4"></i>
                                <div><small class="text-muted d-block">{{ __('agenda.time') }}</small><span class="fw-semibold">{{ $data->time_window }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2"><i class="bx bx-map-pin text-warning fs-4"></i>
                                <div><small class="text-muted d-block">{{ __('agenda.location') }}</small><span class="fw-semibold">{{ $data->location ?? '-' }}</span></div>
                            </div>
                        </div>
                        @if($data->delegation)
                            <div class="col-12">
                                <div class="d-flex gap-2"><i class="bx bx-task text-secondary fs-4"></i>
                                    <div><small class="text-muted d-block">{{ __('delegation.agenda') }}</small>
                                        <a href="{{ route('delegation.show', $data->delegation->id) }}" class="fw-semibold text-decoration-none">{{ $data->delegation->title }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($data->description)
                        <hr>
                        <label class="form-label fw-semibold text-muted">{{ __('delegation.description') }}</label>
                        <p class="mb-0">{!! nl2br(e($data->description)) !!}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card ag-card h-100">
                <div class="card-header py-3"><h5 class="mb-0 fw-bold">{{ __('menu.general.action') }}</h5></div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('agenda-pimpinan.edit', $data->id) }}" class="btn btn-outline-primary"><i class="bx bx-edit me-1"></i>{{ __('menu.general.edit') }}</a>
                    @if($data->status == 'terjadwal')
                        <form action="{{ route('agenda-pimpinan.status', $data->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="status" value="batal">
                            <button class="btn btn-outline-danger w-100"><i class="bx bx-x-circle me-1"></i>{{ __('agenda.cancel') }}</button>
                        </form>
                    @endif
                    <form action="{{ route('agenda-pimpinan.destroy', $data->id) }}" method="post" class="mt-auto">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100 btn-delete"><i class="bx bx-trash me-1"></i>{{ __('menu.general.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card ag-card">
                <div class="card-header py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-calendar me-2 text-primary"></i>{{ __('agenda.calendar') }}</h5>
                </div>
                <div class="card-body">
                    <div id="agenda-detail-calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('agenda-detail-calendar');
            if (!el || typeof FullCalendar === 'undefined') return;

            var color = @json($data->status === 'selesai' ? '#02bc7d' : ($data->status === 'dibatalkan' ? '#ff3e1d' : '#696cff'));

            var calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                initialDate: @json($data->date?->format('Y-m-d')),
                locale: '{{ app()->getLocale() === 'id' ? 'id' : 'en-gb' }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                height: 'auto',
                fixedWeekCount: false,
                expandRows: true,
                eventDisplay: 'block',
                nowIndicator: true,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
                events: [{
                    title: @json($data->title),
                    start: @json(($data->date?->format('Y-m-d')) . ($data->start_time ? 'T' . \Carbon\Carbon::parse($data->start_time)->format('H:i:s') : '')),
                    end: @json(($data->date?->format('Y-m-d')) . ($data->end_time ? 'T' . \Carbon\Carbon::parse($data->end_time)->format('H:i:s') : '')),
                    color: color
                }]
            });

            calendar.render();
        });
    </script>
@endpush