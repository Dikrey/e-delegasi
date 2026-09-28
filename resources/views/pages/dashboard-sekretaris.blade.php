@extends('layout.main')

@push('style')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-theme.css') }}?v=2">
    <style>
        #agenda-calendar-dashboard { min-height: 560px; }
        .edeleg-hero {
            position: relative; overflow: hidden; border-radius: 1.25rem;
            background: linear-gradient(120deg, #6d67e4 0%, #5448d6 45%, #1f1a66 100%);
            color: #fff; box-shadow: 0 18px 40px -14px rgba(109, 103, 228, 0.65);
            animation: fadeSlide 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .edeleg-hero::before { content: ''; position: absolute; width: 260px; height: 260px; right: -60px; top: -110px; border-radius: 50%; background: radial-gradient(circle, rgba(54,241,205,0.35), transparent 65%); }
        .edeleg-hero::after { content: ''; position: absolute; width: 180px; height: 180px; right: 170px; bottom: -100px; border-radius: 50%; background: radial-gradient(circle, rgba(255,107,203,0.3), transparent 65%); }
        @keyframes fadeSlide { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

        .stat-card-hover {
            border: none !important; border-radius: 1rem !important;
            box-shadow: 0 10px 26px -12px rgba(38, 42, 71, 0.32);
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s;
        }
        .stat-card-hover:hover { transform: translateY(-5px); box-shadow: 0 22px 44px -18px rgba(38, 42, 71, 0.5); }
        .stat-glyph { width: 50px; height: 50px; display: grid; place-items: center; border-radius: 14px; font-size: 23px; color: #fff; }
        .count-up { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.5px; }

        .glance-card { border: none !important; border-radius: 1rem !important; box-shadow: 0 10px 26px -12px rgba(38,42,71,0.3); }
        .tl-item { display: flex; gap: 0.8rem; padding: 0.65rem 0; border-bottom: 1px solid rgba(109,103,228,0.07); }
        .tl-item:last-child { border-bottom: none; }
        .tl-dot { flex-shrink: 0; width: 36px; height: 36px; display: grid; place-items: center; border-radius: 11px; font-size: 15px; color: #fff; }
        .deadline-badge { font-weight: 700; }
    </style>
@endpush

@section('content')
    <div class="row gy-4">

        {{-- HERO --}}
        <div class="col-12">
            <div class="edeleg-hero p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                                <i class="bx bxs-calendar me-1"></i>{{ $currentDate }}
                            </span>
                            <span class="hero-chip"><i class="bx bx-spreadsheet"></i>{{ __('dashboard.sekretaris_panel') }}</span>
                        </div>
                        <h4 class="mb-1 fw-bold" style="font-size: 1.6rem;">{{ $greeting }}, {{ auth()->user()->name }}</h4>
                        <p class="mb-0 opacity-75">{{ __('dashboard.sekretaris_welcome') }}</p>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0">
                        <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                            <a href="{{ route('delegation.create') }}" class="btn btn-light btn-hero"><i class="bx bx-plus me-1"></i>{{ __('dashboard.qa_new_delegation') }}</a>
                            <a href="{{ route('agenda-pimpinan.index') }}" class="btn btn-outline-light"><i class="bx bx-calendar-event me-1"></i>{{ __('dashboard.qa_calendar') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STAT CARDS --}}
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.waiting_verification') }}</span>
                            <div class="count-up" data-target="{{ $stats->waitingVerification }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-envelope"></i></div>
                    </div>
                    <a href="{{ route('transaction.incoming.index') }}" class="small text-primary text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.active_delegation') }}</span>
                            <div class="count-up" data-target="{{ $stats->activeDelegation }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-task"></i></div>
                    </div>
                    <a href="{{ route('delegation.index', ['status' => 'active']) }}" class="small text-primary text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.running_task') }}</span>
                            <div class="count-up" data-target="{{ $stats->runningTask }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#36c2f1,#0288d1);"><i class="bx bx-loader-circle"></i></div>
                    </div>
                    <a href="{{ route('task.index', ['status' => 'dalam_pengerjaan']) }}" class="small text-primary text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.late_task') }}</span>
                            <div class="count-up text-danger" data-target="{{ $stats->lateTask }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#ff6b6b,#e41e1e);"><i class="bx bx-time"></i></div>
                    </div>
                    <a href="{{ route('task.index', ['status' => 'terlambat']) }}" class="small text-danger text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.done_task') }}</span>
                            <div class="count-up text-success" data-target="{{ $stats->doneTask }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#36f1a5,#00b874);"><i class="bx bx-check-double"></i></div>
                    </div>
                    <a href="{{ route('task.index', ['status' => 'selesai']) }}" class="small text-success text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-semibold text-muted small">{{ __('dashboard.today_agenda') }}</span>
                            <div class="count-up" data-target="{{ $stats->todayAgenda }}">0</div>
                        </div>
                        <div class="stat-glyph" style="background: linear-gradient(135deg,#fbbf24,#f59e0b);"><i class="bx bx-calendar-star"></i></div>
                    </div>
                    <a href="{{ route('agenda-pimpinan.index') }}" class="small text-primary text-decoration-none">{{ __('dashboard.view_more') }}</a>
                </div>
            </div>
        </div>

        {{-- KALENDER AGENDA INTERAKTIF --}}
        <div class="col-12">
            <div class="card glance-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-calendar me-2 text-primary"></i>{{ __('dashboard.agenda_calendar') }}</h5>
                    <a href="{{ route('agenda-pimpinan.index') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_calendar') }}</a>
                </div>
                <div class="card-body">
                    <div id="agenda-calendar-dashboard"></div>
                </div>
            </div>
        </div>

        {{-- AGENDA HARI INI — CALENDAR --}}
        <div class="col-lg-6">
            <div class="card glance-card h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-calendar-event me-2 text-primary"></i>{{ __('dashboard.agenda_today') }}</h5>
                    <a href="{{ route('agenda-pimpinan.index') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_calendar') }}</a>
                </div>
                <div class="card-body">
                    @forelse($todayAgendas as $agenda)
                        <div class="tl-item">
                            <div class="tl-dot" style="background:linear-gradient(135deg,#fbbf24,#f59e0b);"><i class="bx bx-calendar-event"></i></div>
                            <div class="flex-grow-1">
                                <a href="{{ route('agenda-pimpinan.show', $agenda->id) }}" class="text-decoration-none fw-semibold text-dark">{{ $agenda->title }}</a>
                                <div class="text-muted small">{{ $agenda->time_window ? $agenda->time_window . ' · ' : '' }}{{ $agenda->location }}</div>
                                @if($agenda->delegation)
                                    <span class="badge bg-label-primary mt-1">{{ $agenda->delegation->title }}</span>
                                @endif
                            </div>
                            <span class="badge {{ $agenda->status == 'selesai' ? 'bg-label-success' : 'bg-label-warning' }}">{{ $agenda->status_label }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-calendar-check" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">{{ __('dashboard.no_agenda_today') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- TASK MENDAPATI DEADLINE --}}
        <div class="col-lg-6">
            <div class="card glance-card h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-time-five me-2 text-warning"></i>{{ __('dashboard.deadline_soon') }}</h5>
                    <a href="{{ route('task.kanban') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.open_kanban') }}</a>
                </div>
                <div class="card-body">
                    @forelse($approachingTasks as $task)
                        <a href="{{ route('task.show', $task->id) }}" class="text-decoration-none">
                            <div class="tl-item">
                                <div class="tl-dot" style="background:linear-gradient(135deg,#ff6b9d,#e83e8c);"><i class="bx bx-hourglass"></i></div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold text-dark d-block text-truncate">{{ $task->title }}</span>
                                    <span class="text-muted small">{{ $task->staff?->name }} · {{ $task->formatted_deadline }}</span>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge {{ $task->deadline->isPast() ? 'bg-label-danger' : 'bg-label-warning' }} deadline-badge">
                                        {{ $task->deadline->isPast() ? __('task.overdue') : $task->deadline->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-check-circle" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">{{ __('dashboard.no_deadline_soon') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- MONITORING DELEGASI --}}
        <div class="col-12">
            <div class="card glance-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-task me-2 text-primary"></i>{{ __('dashboard.delegation_monitoring') }}</h5>
                    <a href="{{ route('delegation.monitoring') }}" class="btn btn-sm btn-outline-primary">{{ __('dashboard.view_all') }}</a>
                </div>
                <div class="card-body" style="max-height: 360px; overflow-y: auto;">
                    @forelse($activeDelegations as $delegation)
                        <a href="{{ route('delegation.show', $delegation->id) }}" class="text-decoration-none">
                            <div class="tl-item">
                                <div class="tl-dot" style="background:linear-gradient(135deg,#6d67e4,#5448d6);"><i class="bx bx-share-alt"></i></div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold text-dark d-block text-truncate">{{ $delegation->title }}</span>
                                    <div class="text-muted small">
                                        @foreach($delegation->tasks as $task)
                                            <span class="badge bg-label-{{ $task->priority == 'urgent' ? 'danger' : ($task->priority == 'tinggi' ? 'warning' : 'info') }} me-1">{{ $task->staff?->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge {{ match($delegation->status) {
                                        'dikirim' => 'bg-label-info',
                                        'diterima' => 'bg-label-primary',
                                        'dalam_pengerjaan' => 'bg-label-warning',
                                        'menunggu_verifikasi' => 'bg-label-secondary',
                                        'ditolak' => 'bg-label-danger',
                                        default => 'bg-label-secondary',
                                    } }}">{{ $delegation->status_label }}</span>
                                    <div class="small text-muted mt-1 progress-bar-wrap">
                                        @php $avg = $delegation->tasks->where('status', '<>', 'ditolak')->avg('progress'); @endphp
                                        <div class="progress" style="height:6px;width:120px;">
                                            <div class="progress-bar bg-primary" style="width: {{ round($avg ?? 0) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-task" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">{{ __('dashboard.no_delegation') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- AKTIVITAS TERBARU --}}
        <div class="col-12">
            <div class="card glance-card">
                <div class="card-header py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-primary"></i>{{ __('dashboard.recent_activity') }}</h5>
                </div>
                <div class="card-body" style="max-height: 360px; overflow-y: auto;">
                    @forelse($activities as $act)
                        <div class="tl-item">
                            <div class="tl-dot" style="background:linear-gradient(135deg, var(--surat-primary), var(--surat-primary-dark));">
                                <i class="bx {{ $act['icon'] }}"></i>
                            </div>
                            <div class="tl-content flex-grow-1">
                                <h6 class="mb-0 small fw-bold text-truncate">{{ $act['title'] }}</h6>
                                <p class="text-muted mb-0 small text-truncate">{{ $act['subtitle'] }}</p>
                            </div>
                            <span class="tl-time text-muted small flex-shrink-0">{{ $act['time'] }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bx bx-time-five" style="font-size:2.5rem;"></i>
                            <p class="mt-2 mb-0">{{ __('dashboard.no_activity') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.querySelectorAll('.count-up').forEach(function (el) {
            var target = parseInt(el.dataset.target || 0);
            new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var start = performance.now();
                        function tick(now) {
                            var p = Math.min((now - start) / 900, 1);
                            var val = Math.round(target * (1 - Math.pow(1 - p, 3)));
                            el.textContent = val.toLocaleString('id-ID');
                            if (p < 1) requestAnimationFrame(tick);
                        }
                        requestAnimationFrame(tick);
                        obs.disconnect();
                    }
                });
            }, { threshold: 0.4 }).observe(el);
        });

        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('agenda-calendar-dashboard');
            if (!el || typeof FullCalendar === 'undefined') return;

            var calendar = new FullCalendar.Calendar(el, {
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
                nowIndicator: true,
                dayMaxEvents: 3,
                moreLinkClick: 'popover',
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
                events: '{{ route("agenda-pimpinan.data") }}',
                eventDidMount: function (info) {
                    var loc = info.event.extendedProps && info.event.extendedProps.location;
                    info.el.setAttribute('title', info.event.title + (loc ? ' • ' + loc : ''));
                },
                eventClick: function (info) {
                    if (info.event.extendedProps && info.event.extendedProps.url) {
                        window.location.href = info.event.extendedProps.url;
                    }
                }
            });

            calendar.render();
        });
    </script>
@endpush