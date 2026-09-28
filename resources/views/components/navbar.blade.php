<nav
    class="layout-navbar container-xxl zindex-5 navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <form action="{{ url()->current() }}">
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-control border-0 shadow-none"
                        placeholder="{{ __('navbar.search') }}"
                        aria-label="{{ __('navbar.search') }}"
                    />

                </div>
            </div>
        </form>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Notifications -->
            <li class="nav-item navbar-dropdown dropdown me-3">
                <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0);"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-bell fs-4"></i>
                    <span id="notif-badge"
                          class="badge badge-center rounded-pill bg-danger w-px-18 h-px-18 position-absolute"
                          style="top: 2px; right: 6px; font-size: 10px; {{ \App\Models\Notification::forUser()->unread()->count() == 0 ? 'display:none;' : '' }}">
                        {{ \App\Models\Notification::forUser()->unread()->count() > 99 ? '99+' : \App\Models\Notification::forUser()->unread()->count() }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                    <li class="d-flex justify-content-between align-items-center gap-2 px-3 py-2 border-bottom">
                        <span class="fw-bold">{{ __('navbar.notify.title') }}</span>
                        <span class="badge bg-label-danger rounded-pill notif-count">{{ \App\Models\Notification::forUser()->unread()->count() }}</span>
                    </li>
                    <li class="notif-scroll">
                        @php $notifs = \App\Models\Notification::forUser()->latest()->limit(6)->get(); @endphp
                        @forelse($notifs as $notif)
                            <div class="dropdown-item notif-item d-flex align-items-center gap-1 {{ $notif->is_read ? 'notif-read' : '' }}">
                                <a class="d-flex align-items-start gap-2 flex-grow-1 min-width-0 text-decoration-none"
                                   href="{{ $notif->link ? route('notification.read', $notif->id) : '#' }}">
                                    <span class="notif-icon notif-{{ $notif->type }}">
                                        <i class="bx {{ match($notif->type) {
                                            'task' => 'bx-task',
                                            'deadline' => 'bx-time',
                                            'agenda' => 'bx-calendar-event',
                                            'delegation' => 'bx-share-alt',
                                            default => 'bx-info-circle',
                                        } }}"></i>
                                    </span>
                                    <span class="flex-grow-1 min-width-0">
                                        <span class="d-block fw-semibold small text-dark text-truncate">{{ $notif->title }}</span>
                                        <span class="d-block text-muted small text-truncate">{{ $notif->message }}</span>
                                        <span class="d-block text-muted" style="font-size: 0.68rem;">{{ $notif->time_ago }}</span>
                                    </span>
                                </a>
                                <form action="{{ route('notification.destroy', $notif->id) }}" method="post" class="m-0 notif-del">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm p-1 text-muted border-0 bg-transparent notif-del-btn"
                                            title="{{ __('menu.general.delete') }}">
                                        <i class="bx bx-x fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-bell-off fs-2"></i>
                                <p class="mb-0 small">{{ __('navbar.notify.empty') }}</p>
                            </div>
                        @endforelse
                    </li>
                    <li class="border-top">
                        <div class="d-grid gap-2 p-2">
                            <form action="{{ route('notification.read_all') }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bx bx-check-double me-1"></i>{{ __('navbar.notify.read_all') }}
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- /Notifications -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                   data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ auth()->user()->profile_picture }}" alt
                             class="w-px-40 h-auto rounded-circle"/>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex account-drop-top">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ auth()->user()->profile_picture }}" alt
                                             class="w-px-40 h-auto rounded-circle"/>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted text-capitalize d-block mb-1">{{ __('model.user.' . auth()->user()->role) }}</small>
                                    <span class="online-pill"><span class="status-dot"></span>{{ __('navbar.online') }}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">{{ __('navbar.profile.profile') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('devices.index') }}">
                            <i class="bx bx-devices me-2"></i>
                            <span class="align-middle">{{ __('navbar.profile.devices') }}</span>
                        </a>
                    </li>
                    @if(auth()->user()->role == 'admin')
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.show') }}">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">{{ __('navbar.profile.settings') }}</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="dropdown-item cursor-pointer">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">{{ __('navbar.profile.logout') }}</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

@push('script')
    <script>
        (function () {
            var badge = document.getElementById('notif-badge');
            if (!badge) return;

            function refreshNotifications() {
                fetch("{{ route('notification.poll') }}", { headers: { 'Accept': 'application/json' } })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.unread > 0) {
                            badge.style.display = '';
                            badge.textContent = data.unread > 99 ? '99+' : data.unread;
                        } else {
                            badge.style.display = 'none';
                        }
                        document.querySelectorAll('.notif-count').forEach(function (el) {
                            el.textContent = data.unread || 0;
                        });
                    })
                    .catch(function () {});
            }

            setInterval(refreshNotifications, 60000);
        })();
    </script>
@endpush