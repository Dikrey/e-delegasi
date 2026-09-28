@extends('layout.main')

@push('style')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-theme.css') }}?v=2">
    <style>
        #calendar { max-width: 1100px; margin: 0 auto; }
        .calendar-legend { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
        .calendar-legend .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <x-page-hero :title="__('agenda.calendar')" :subtitle="__('agenda.calendar_subtitle')" icon="bx-calendar-event">
                <x-slot:actions>
                    <a href="{{ route('agenda-pimpinan.list') }}" class="btn btn-hero"><i class="bx bx-list-ul me-1"></i>{{ __('agenda.list') }}</a>
                    <a href="{{ route('agenda-pimpinan.create') }}" class="btn btn-hero"><i class="bx bx-plus me-1"></i>{{ __('agenda.create_btn') }}</a>
                </x-slot:actions>
            </x-page-hero>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <select id="filter-status" class="form-select w-auto">
                            <option value="all">{{ __('agenda.filter_status') }}: {{ __('menu.general.all') }}</option>
                            @foreach(\App\Enums\AgendaStatus::options() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <select id="filter-type" class="form-select w-auto">
                            <option value="all">{{ __('agenda.filter_type') }}: {{ __('menu.general.all') }}</option>
                            @foreach(__('enums.agenda_type') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="text-muted small ms-auto"><i class="bx bx-move me-1"></i>{{ __('agenda.drag_hint') }}</span>
                    </div>

                    <div id="calendar"></div>

                    <div class="calendar-legend mt-3 small text-muted">
                        <span><span class="dot" style="background:#696cff;"></span>{{ __('enums.agenda_status.scheduled') }}</span>
                        <span><span class="dot" style="background:#02bc7d;"></span>{{ __('enums.agenda_status.done') }}</span>
                        <span><span class="dot" style="background:#ff3e1d;"></span>{{ __('enums.agenda_status.cancelled') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var eventsUrl = '{{ route("agenda-pimpinan.data") }}';
            var rescheduleUrl = '{{ url("agenda-pimpinan") }}';
            var csrf = '{{ csrf_token() }}';
            var calendarEl = document.getElementById('calendar');
            var statusEl = document.getElementById('filter-status');
            var typeEl = document.getElementById('filter-type');

            if (!calendarEl || typeof FullCalendar === 'undefined') return;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
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
                editable: true,
                eventDurationEditable: false,
                navLinks: true,
                nowIndicator: true,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
                events: function (fetchInfo, successCallback, failureCallback) {
                    var params = new URLSearchParams();
                    params.set('start', fetchInfo.startStr);
                    params.set('end', fetchInfo.endStr);
                    params.set('status', statusEl.value);
                    params.set('type', typeEl.value);

                    fetch(eventsUrl + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                        .then(function (r) { return r.json(); })
                        .then(successCallback)
                        .catch(failureCallback);
                },
                eventClick: function (info) {
                    if (info.event.extendedProps && info.event.extendedProps.url) {
                        window.location.href = info.event.extendedProps.url;
                    }
                },
                eventDrop: function (info) {
                    var event = info.event;
                    var body = new URLSearchParams();
                    body.set('_token', csrf);
                    body.set('date', event.startStr.substring(0, 10));
                    if (event.start) {
                        body.set('start_time', event.startStr.length > 10 ? event.startStr.substring(11) : '');
                    }

                    fetch(rescheduleUrl + '/' + event.id + '/reschedule', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                        body: body.toString()
                    }).then(function (r) {
                        if (!r.ok) throw new Error('failed');
                        if (window.Toast) Toast.fire({ icon: 'success', title: '{{ __('menu.general.success') }}' });
                    }).catch(function () {
                        info.revert();
                        if (window.Toast) Toast.fire({ icon: 'error', title: '{{ __('menu.general.fail') }}' });
                    });
                }
            });

            calendar.render();

            statusEl.addEventListener('change', function () { calendar.refetchEvents(); });
            typeEl.addEventListener('change', function () { calendar.refetchEvents(); });
        });
    </script>
@endpush
