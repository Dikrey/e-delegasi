<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <img src="{{ asset('logo-black.png') }}" alt="{{ config('app.name') }}" width="35">
            <span class="app-brand-text demo text-black fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Home -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="{{ __('menu.home') }}">{{ __('menu.home') }}</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.delegation') }}</span>
        </li>

        @if(in_array(auth()->user()->role, ['admin', 'sekretaris']))
            <!-- Agenda Pimpinan -->
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda-pimpinan.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <div data-i18n="{{ __('menu.agenda_pimpinan.menu') }}">{{ __('menu.agenda_pimpinan.menu') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda-pimpinan.index') ? 'active' : '' }}">
                        <a href="{{ route('agenda-pimpinan.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.agenda_pimpinan.calendar') }}">{{ __('menu.agenda_pimpinan.calendar') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda-pimpinan.list') && request()->query('tab', 'today') === 'today' ? 'active' : '' }}">
                        <a href="{{ route('agenda-pimpinan.list', ['tab' => 'today']) }}" class="menu-link">
                            <div data-i18n="{{ __('menu.agenda_pimpinan.today') }}">{{ __('menu.agenda_pimpinan.today') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda-pimpinan.list') && request()->query('tab', 'today') === 'upcoming' ? 'active' : '' }}">
                        <a href="{{ route('agenda-pimpinan.list', ['tab' => 'upcoming']) }}" class="menu-link">
                            <div data-i18n="{{ __('menu.agenda_pimpinan.upcoming') }}">{{ __('menu.agenda_pimpinan.upcoming') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda-pimpinan.list') && request()->query('tab', 'today') === 'done' ? 'active' : '' }}">
                        <a href="{{ route('agenda-pimpinan.list', ['tab' => 'done']) }}" class="menu-link">
                            <div data-i18n="{{ __('menu.agenda_pimpinan.done') }}">{{ __('menu.agenda_pimpinan.done') }}</div>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Delegasi -->
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-task"></i>
                    <div data-i18n="{{ __('menu.delegation.menu') }}">{{ __('menu.delegation.menu') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.index') ? 'active' : '' }}">
                        <a href="{{ route('delegation.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.delegation.all') }}">{{ __('menu.delegation.all') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.create') ? 'active' : '' }}">
                        <a href="{{ route('delegation.create') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.delegation.create') }}">{{ __('menu.delegation.create') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.monitoring') ? 'active' : '' }}">
                        <a href="{{ route('delegation.monitoring') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.delegation.monitoring') }}">{{ __('menu.delegation.monitoring') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.index') && request()->query('status', 'all') === 'active' ? 'active' : '' }}">
                        <a href="{{ route('delegation.index', ['status' => 'active']) }}" class="menu-link">
                            <div data-i18n="{{ __('delegation.active') }}">{{ __('delegation.active') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.index') && request()->query('status', 'all') === 'done' ? 'active' : '' }}">
                        <a href="{{ route('delegation.index', ['status' => 'done']) }}" class="menu-link">
                            <div data-i18n="{{ __('delegation.done') }}">{{ __('delegation.done') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('delegation.index') && request()->query('status', 'all') === 'late' ? 'active' : '' }}">
                        <a href="{{ route('delegation.index', ['status' => 'late']) }}" class="menu-link">
                            <div data-i18n="{{ __('delegation.late') }}">{{ __('delegation.late') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <!-- Tugas -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('task.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-list-check"></i>
                <div data-i18n="{{ __('menu.task.menu') }}">{{ __('menu.task.menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('task.kanban') ? 'active' : '' }}">
                    <a href="{{ route('task.kanban') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.task.kanban') }}">{{ __('menu.task.kanban') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('task.index') ? 'active' : '' }}">
                    <a href="{{ route('task.index') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.task.my') }}">{{ __('menu.task.my') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.support') }}</span>
        </li>

        <!-- Surat -->
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaction.*') || \Illuminate\Support\Facades\Route::is('disposition.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-mail-send"></i>
                <div data-i18n="{{ __('menu.transaction.menu') }}">{{ __('menu.transaction.menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaction.incoming.*') ? 'active' : '' }}">
                    <a href="{{ route('transaction.incoming.index') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.transaction.incoming_letter') }}">{{ __('menu.transaction.incoming_letter') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('transaction.outgoing.*') ? 'active' : '' }}">
                    <a href="{{ route('transaction.outgoing.index') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.transaction.outgoing_letter') }}">{{ __('menu.transaction.outgoing_letter') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Laporan -->
        @if(in_array(auth()->user()->role, ['admin', 'sekretaris']))
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('report.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                    <div data-i18n="{{ __('menu.report.menu') }}">{{ __('menu.report.menu') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('report.delegation') ? 'active' : '' }}">
                        <a href="{{ route('report.delegation') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.report.delegation') }}">{{ __('menu.report.delegation') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('report.task') ? 'active' : '' }}">
                        <a href="{{ route('report.task') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.report.task') }}">{{ __('menu.report.task') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('report.agenda') ? 'active' : '' }}">
                        <a href="{{ route('report.agenda') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.report.agenda') }}">{{ __('menu.report.agenda') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('archive.*') ? 'active' : '' }}">
            <a href="{{ route('archive.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="{{ __('menu.archive.menu') }}">{{ __('menu.archive.menu') }}</div>
            </a>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="{{ __('menu.agenda.menu') }}">{{ __('menu.agenda.menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda.incoming') ? 'active' : '' }}">
                    <a href="{{ route('agenda.incoming') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.agenda.incoming_letter') }}">{{ __('menu.agenda.incoming_letter') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('agenda.outgoing') ? 'active' : '' }}">
                    <a href="{{ route('agenda.outgoing') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.agenda.outgoing_letter') }}">{{ __('menu.agenda.outgoing_letter') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('menu.header.other_menu') }}</span>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('devices.*') ? 'active' : '' }}">
            <a href="{{ route('devices.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-devices"></i>
                <div data-i18n="{{ __('navbar.profile.devices') }}">{{ __('navbar.profile.devices') }}</div>
            </a>
        </li>
        <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-images"></i>
                <div data-i18n="{{ __('menu.gallery.menu') }}">{{ __('menu.gallery.menu') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.incoming') ? 'active' : '' }}">
                    <a href="{{ route('gallery.incoming') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.gallery.incoming_letter') }}">{{ __('menu.gallery.incoming_letter') }}</div>
                    </a>
                </li>
                <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('gallery.outgoing') ? 'active' : '' }}">
                    <a href="{{ route('gallery.outgoing') }}" class="menu-link">
                        <div data-i18n="{{ __('menu.gallery.outgoing_letter') }}">{{ __('menu.gallery.outgoing_letter') }}</div>
                    </a>
                </li>
            </ul>
        </li>

        @if(auth()->user()->role == 'admin')
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('activity-log.*') ? 'active' : '' }}">
                <a href="{{ route('activity-log.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-history"></i>
                    <div data-i18n="{{ __('menu.activity_log') }}">{{ __('menu.activity_log') }}</div>
                </a>
            </li>
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-analyse"></i>
                    <div data-i18n="{{ __('menu.reference.menu') }}">{{ __('menu.reference.menu') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.classification.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.classification.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.classification') }}">{{ __('menu.reference.classification') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.status.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.status.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.status') }}">{{ __('menu.reference.status') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.department.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.department.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.department') }}">{{ __('menu.reference.department') }}</div>
                        </a>
                    </li>
                    <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('reference.task-category.*') ? 'active' : '' }}">
                        <a href="{{ route('reference.task-category.index') }}" class="menu-link">
                            <div data-i18n="{{ __('menu.reference.task_category') }}">{{ __('menu.reference.task_category') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('user.*') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-pin"></i>
                    <div data-i18n="{{ __('menu.users') }}">{{ __('menu.users') }}</div>
                </a>
            </li>
            <li class="menu-item {{ \Illuminate\Support\Facades\Route::is('settings.show') ? 'active' : '' }}">
                <a href="{{ route('settings.show') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div data-i18n="{{ __('navbar.profile.settings') }}">{{ __('navbar.profile.settings') }}</div>
                </a>
            </li>
        @endif
    </ul>

    <!-- Account card -->
    <div class="sidebar-account">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="account-avatar">
                <img src="{{ auth()->user()->profile_picture }}" alt="{{ auth()->user()->name }}">
                <span class="status-dot position-absolute" style="bottom: 2px; right: 2px;"></span>
            </div>
            <div class="text-truncate">
                <div class="account-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="account-role">{{ __('model.user.' . auth()->user()->role) }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="online-pill"><span class="status-dot"></span>{{ __('navbar.online') }}</span>
            <a href="{{ route('profile.show') }}" class="small text-primary fw-semibold text-decoration-none">
                {{ __('navbar.account') }}
            </a>
        </div>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="btn-account-logout">
                <i class="bx bx-log-out"></i>{{ __('navbar.profile.logout') }}
            </button>
        </form>
    </div>
</aside>