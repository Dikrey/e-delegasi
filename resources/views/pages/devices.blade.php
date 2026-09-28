@extends('layout.main')

@push('style')
    <style>
        .device-item {
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s;
            border: none !important;
            border-radius: 1rem !important;
        }
        .device-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px -16px rgba(38, 42, 71, 0.4);
        }
        .device-item-enter {
            animation: deviceIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes deviceIn {
            from { opacity: 0; transform: translateY(14px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .dev-glyph {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            font-size: 26px;
            color: #fff;
            flex-shrink: 0;
        }
        .dot-live {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #36f1a5;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(54, 241, 165, 0.6);
            animation: livePulse 1.8s ease-out infinite;
        }
        @keyframes livePulse {
            0% { box-shadow: 0 0 0 0 rgba(54, 241, 165, 0.55); }
            70% { box-shadow: 0 0 0 8px rgba(54, 241, 165, 0); }
            100% { box-shadow: 0 0 0 0 rgba(54, 241, 165, 0); }
        }
        .skeleton {
            position: relative;
            overflow: hidden;
            background: #f1f2f8;
            border-radius: 1rem;
        }
        .skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
            animation: shimmerS 1.4s infinite;
        }
        @keyframes shimmerS { 100% { transform: translateX(100%); } }
    </style>
@endpush

@push('script')
    <script>
        const CURRENT_SESSION_ID = '{{ $currentSessionId ?? '' }}';
        const langCurrent = '{{ __('device.current') }}';
        const langThisDevice = '{{ __('device.this_device') }}';
        const langLastActive = '{{ __('device.last_active') }}';
        const langActive = '{{ __('device.active_now') }}';
        const langLogout = '{{ __('device.logout') }}';

        function renderSessions(payload) {
            const container = document.getElementById('sessionList');
            const empty = document.getElementById('sessionEmpty');
            const count = document.getElementById('sessionCount');

            if (!payload || !Array.isArray(payload.sessions)) return;

            count.textContent = payload.count;

            if (payload.sessions.length === 0) {
                container.innerHTML = '';
                empty.classList.remove('d-none');
                return;
            }
            empty.classList.add('d-none');

            container.innerHTML = payload.sessions.map((s, i) => `
                <div class="card device-item device-item-enter mb-3" style="animation-delay:${i * 60}ms">
                    <div class="card-body d-flex align-items-center gap-3 flex-wrap">
                        <div class="dev-glyph" style="background:${s.is_current ? 'linear-gradient(135deg,#6d67e4,#5448d6)' : 'linear-gradient(135deg,#9e9ebd,#6d6d8f)'}">
                            <i class="${s.icon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold">${s.device} — ${s.browser}</span>
                                ${s.is_current ? `<span class="badge bg-success rounded-pill"><span class="dot-live me-1"></span>${langCurrent}</span>` : ''}
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap text-muted small mt-1">
                                <span><i class="bx bxl-windows me-1"></i>${s.platform}</span>
                                <span><i class="bx bx-globe me-1"></i>${s.location || '-'}</span>
                                <span><i class="bx bx-map-pin me-1"></i>${s.ip_address || '-'}</span>
                            </div>
                            <div class="text-muted small mt-1">
                                ${s.is_current ? langThisDevice : langLastActive}: <span data-iso="${s.last_activity_raw}">${s.last_activity}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            ${s.is_current
                                ? `<span class="badge bg-label-info rounded-pill"><i class="bx bxs-check-circle me-1"></i>${langActive}</span>`
                                : `<button class="btn btn-outline-danger btn-sm" onclick="logoutDevice(${s.id})"><i class="bx bx-log-out-circle me-1"></i>${langLogout}</button>`}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function refreshSessions() {
            fetch('{{ route('devices.data') }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(payload => renderSessions(payload))
                .catch(() => { /* silently retry next tick */ });
        }

        function logoutDevice(id) {
            if (!confirm('{{ __('device.confirm_logout_device') }}')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('devices.index') }}/${id}/logout`;
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function () {
            refreshSessions();
            setInterval(refreshSessions, 10000);
        });
    </script>
@endpush

@section('content')
    <x-breadcrumb :values="[__('navbar.profile.devices')]">
        <button type="button" class="btn btn-danger" onclick="if(confirm('{{ __('device.confirm_logout_all') }}')){ const f=document.createElement('form');f.method='POST';f.action='{{ route('devices.logout_others') }}';const t=document.createElement('input');t.type='hidden';t.name='_token';t.value='{{ csrf_token() }}';f.appendChild(t);document.body.appendChild(f);f.submit();}">
            <i class="bx bx-log-out me-1"></i>{{ __('device.logout_all_others') }}
        </button>
    </x-breadcrumb>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border: none; border-radius: 1.1rem;">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="mb-0 fw-bold">{{ __('device.active_sessions') }}</h5>
                        <small class="text-muted">{{ __('device.active_sessions_hint') }}</small>
                    </div>
                    <span class="badge bg-label-primary rounded-pill" id="sessionCount">0</span>
                </div>
                <div class="card-body">
                    <div id="sessionList">
                        <div class="skeleton" style="height: 88px;"></div>
                        <div class="skeleton mt-3" style="height: 88px;"></div>
                        <div class="skeleton mt-3" style="height: 88px;"></div>
                    </div>
                    <div id="sessionEmpty" class="d-none text-center py-5">
                        <i class="bx bxs-devices display-4 text-muted"></i>
                        <p class="text-muted mt-2">{{ __('device.no_sessions') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" style="border: none; border-radius: 1.1rem;">
                <div class="card-header py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-shield-alt me-1"></i>{{ __('device.security_tip') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
                        <i class="bx bxs-error-circle fs-4 lh-1"></i>
                        <div class="small">{{ __('device.password_change_note') }}</div>
                    </div>
                    <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
                        <i class="bx bxs-info-circle fs-4 lh-1"></i>
                        <div class="small">{{ __('device.session_realtime_note') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection