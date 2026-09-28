<!DOCTYPE html>
<html lang="id" data-theme="theme-default">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('logo-black.png') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}"/>

    <style>
        :root {
            --primary: #6d67e4;
            --primary-dark: #4f46d5;
            --primary-deep: #2b2488;
            --accent: #00cff3;
            --green: #22c55e;
            --ink: #23274b;
            --muted: #6a7090;
            --line: #e7e9f5;
            --bg: #f6f7fe;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', 'Public Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        /* ---------- SOFT LIGHT BACKGROUND ---------- */
        .bg-wrap {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            background:
                radial-gradient(52% 52% at 12% 8%, rgba(109, 103, 228, 0.14), transparent 70%),
                radial-gradient(46% 46% at 88% 12%, rgba(0, 207, 243, 0.12), transparent 70%),
                radial-gradient(44% 44% at 88% 88%, rgba(255, 107, 203, 0.10), transparent 70%),
                radial-gradient(40% 40% at 14% 90%, rgba(54, 241, 205, 0.10), transparent 70%);
        }
        .bg-wrap .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(8px);
            opacity: 0.55;
            animation: drift 16s ease-in-out infinite alternate;
        }
        .bg-wrap .blob.b1 { width: 320px; height: 320px; top: -90px; right: -60px; background: radial-gradient(circle at 30% 30%, rgba(109, 103, 228, 0.28), transparent 70%); }
        .bg-wrap .blob.b2 { width: 240px; height: 240px; bottom: -70px; left: -40px; background: radial-gradient(circle at 30% 30%, rgba(0, 207, 243, 0.22), transparent 70%); animation-duration: 20s; animation-direction: alternate-reverse; }
        .bg-wrap .blob.b3 { width: 140px; height: 140px; top: 22%; left: 72%; background: radial-gradient(circle at 30% 30%, rgba(255, 107, 203, 0.18), transparent 70%); animation-duration: 12s; }
        @keyframes drift {
            from { transform: translate3d(0, 0, 0) scale(1); }
            to   { transform: translate3d(-28px, 30px, 0) scale(1.1); }
        }

        /* ---------- LAYOUT ---------- */
        .auth-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 18px;
        }

        .auth-shell {
            display: grid;
            grid-template-columns: 46% 1fr;
            width: 100%;
            max-width: 1040px;
            min-height: 600px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 32px 80px -32px rgba(40, 43, 96, 0.35);
            animation: shellIn 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes shellIn {
            from { opacity: 0; transform: translateY(34px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ---------- BRAND PANEL ---------- */
        .auth-brand {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px 42px;
            color: #fff;
            background: linear-gradient(158deg, #6d67e4 0%, #4f46d5 48%, #2b2488 100%);
            animation: panelIn 0.9s 0.15s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes panelIn {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .auth-brand::before,
        .auth-brand::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            filter: blur(10px);
        }
        .auth-brand::before { width: 300px; height: 300px; top: -110px; right: -90px; animation: blobFloat 12s ease-in-out infinite alternate; }
        .auth-brand::after { width: 200px; height: 200px; bottom: -80px; left: -50px; animation: blobFloat 14s ease-in-out infinite alternate-reverse; }
        @keyframes blobFloat {
            from { transform: translateY(0) scale(1); }
            to   { transform: translateY(24px) scale(1.15); }
        }

        .brand-top { position: relative; z-index: 1; display: flex; align-items: center; gap: 12px; }
        .brand-logo {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 24px;
            backdrop-filter: blur(4px);
        }
        .brand-name { font-size: 19px; font-weight: 800; letter-spacing: -0.3px; }

        .brand-hero { position: relative; z-index: 1; margin-top: 40px; }
        .brand-hero h2 {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.28;
            letter-spacing: -0.6px;
        }
        .brand-hero h2 .hero-accent { color: #7dffdd; text-shadow: 0 0 22px rgba(54, 241, 205, 0.55); }
        .brand-hero p {
            margin-top: 16px;
            font-size: 15px;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.92);
        }
        .brand-feats { list-style: none; margin-top: 28px; display: grid; gap: 15px; }
        .brand-feats li {
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }
        .brand-feats li > i {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            margin-top: 1px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            font-size: 16px;
            background: rgba(54, 241, 205, 0.18);
            border: 1px solid rgba(54, 241, 205, 0.35);
            color: #7dffdd;
        }
        .brand-feats li .feat-text { display: flex; flex-direction: column; gap: 3px; }
        .brand-feats li .feat-text strong {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.1px;
        }
        .brand-feats li .feat-text span {
            font-size: 12.5px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.78);
        }

        .brand-footer {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.75);
        }
        .brand-footer i { font-size: 18px; color: #7dffdd; }

        /* ---------- FORM PANEL ---------- */
        .auth-form {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 56px;
            animation: formIn 0.9s 0.28s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes formIn {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-card { width: 100%; max-width: 400px; }

        .form-head .form-mobile-logo {
            display: none;
        }
        .form-head h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .form-head p {
            margin-top: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        form { margin-top: 30px; display: flex; flex-direction: column; gap: 18px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--ink);
        }

        .control { position: relative; }
        .control i.bx {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 19px;
            color: #a3a8c3;
            pointer-events: none;
            transition: color 0.25s ease;
        }
        .control input {
            width: 100%;
            padding: 13.5px 52px 13.5px 46px;
            font-size: 15px;
            font-family: inherit;
            color: var(--ink);
            background: #f7f8fd;
            border: 1.5px solid var(--line);
            border-radius: 13px;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
        }
        .control input::placeholder { color: #b5b9cf; }
        .control input:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(109, 103, 228, 0.14);
        }
        .control input:focus ~ i.bx { color: var(--primary); }
        .control input.steel { padding-right: 52px; }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            background: none;
            border: none;
            color: #a3a8c3;
            font-size: 19px;
            border-radius: 9px;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
        }
        .toggle-pass:hover { color: var(--primary); background: rgba(109, 103, 228, 0.08); }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
        }
        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: var(--muted);
        }
        .check input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .check:hover { color: var(--ink); }

        .btn-submit {
            position: relative;
            width: 100%;
            padding: 14.5px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            color: #fff;
            background: linear-gradient(135deg, #6d67e4, #4f46d5);
            border: none;
            border-radius: 13px;
            cursor: pointer;
            overflow: hidden;
            transition: transform 0.2s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.25s;
            box-shadow: 0 14px 30px -10px rgba(109, 103, 228, 0.7);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 20px 40px -12px rgba(109, 103, 228, 0.85); }
        .btn-submit:active { transform: translateY(0) scale(0.985); }
        .btn-submit .spinner { display: none; }
        .btn-submit.loading { pointer-events: none; opacity: 0.75; }
        .btn-submit.loading .label { display: none; }
        .btn-submit.loading .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ---------- ALERTS ---------- */
        .alerts { margin-top: 22px; display: grid; gap: 10px; }
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13.5px;
            line-height: 1.5;
            animation: shake 0.5s ease;
        }
        .alert.error {
            border: 1px solid rgba(255, 62, 29, 0.25);
            background: rgba(255, 62, 29, 0.07);
            color: #d1432c;
        }
        .alert.info {
            border: 1px solid rgba(3, 195, 236, 0.35);
            background: rgba(3, 195, 236, 0.08);
            color: #0b7fa0;
        }
        .alert.success {
            border: 1px solid rgba(54, 241, 165, 0.5);
            background: rgba(54, 241, 165, 0.12);
            color: #12944f;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%      { transform: translateX(-7px); }
            40%      { transform: translateX(7px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }

        .form-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12.5px;
            color: #a3a8c3;
        }

        .hint {
            margin-top: 12px;
            text-align: center;
        }
        .hint span {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            border: 1px dashed #cfd3e8;
            border-radius: 999px;
            color: var(--muted);
            background: #f7f8fd;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 860px) {
            .auth-shell { grid-template-columns: 1fr; max-width: 440px; }
            .auth-brand { display: none; }
            .form-mobile-logo { display: grid !important; }
        }
        @media (max-width: 480px) {
            .auth-wrap { padding: 14px; }
            .auth-shell { border-radius: 22px; }
            .auth-form { padding: 34px 24px; }
        }
    </style>
</head>
<body>
<div class="bg-wrap">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
</div>

<div class="auth-wrap">
    <div class="auth-shell">

        {{-- Brand panel --}}
        <div class="auth-brand">
            <div class="brand-top">
                <div class="brand-logo"><i class="bx bxs-envelope-open"></i></div>
                <span class="brand-name">{{ config('app.name') }}</span>
            </div>

            <div class="brand-hero">
                <h2>{!! __('menu.auth.hero_title') !!}</h2>
                <p>{{ __('menu.auth.hero_desc') }}</p>
                <ul class="brand-feats">
                    <li>
                        <i class="bx bx-envelope-open"></i>
                        <div class="feat-text">
                            <strong>{{ __('menu.auth.feature_1') }}</strong>
                            <span>{{ __('menu.auth.feature_1_desc') }}</span>
                        </div>
                    </li>
                    <li>
                        <i class="bx bx-list-check"></i>
                        <div class="feat-text">
                            <strong>{{ __('menu.auth.feature_2') }}</strong>
                            <span>{{ __('menu.auth.feature_2_desc') }}</span>
                        </div>
                    </li>
                    <li>
                        <i class="bx bx-calendar-event"></i>
                        <div class="feat-text">
                            <strong>{{ __('menu.auth.feature_3') }}</strong>
                            <span>{{ __('menu.auth.feature_3_desc') }}</span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="brand-footer">
                <i class="bx bxs-shield-alt-2"></i>
                {{ __('menu.auth.secure_note') }}
            </div>
        </div>

        {{-- Form panel --}}
        <div class="auth-form">
            <div class="form-card">
                <div class="form-head">
                    <div class="form-mobile-logo brand-top mb-4" style="display:none; color: var(--ink);">
                        <div class="brand-logo" style="background: linear-gradient(135deg,#6d67e4,#4f46d5); border:none; color:#fff;"><i class="bx bxs-envelope-open"></i></div>
                        <span class="brand-name">{{ config('app.name') }}</span>
                    </div>
                    <h1>{{ __('menu.auth.login') }}</h1>
                    <p>{{ __('menu.auth.subtitle') }}</p>
                </div>

                <div class="alerts">
                    @if($errors->any())
                        <div class="alert error">
                            <i class="bx bxs-error-circle fs-4"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert info">
                            <i class="bx bxs-info-circle fs-4"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert success">
                            <i class="bx bxs-check-circle fs-4"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}" autocomplete="off">
                    @csrf

                    <div class="field">
                        <label for="email">{{ __('model.user.email') }}</label>
                        <div class="control">
                            <i class="bx bx-envelope"></i>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="{{ __('menu.auth.email_placeholder') }}"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">{{ __('model.user.password') }}</label>
                        <div class="control">
                            <i class="bx bx-lock-alt"></i>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="steel"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            />
                            <button type="button" class="toggle-pass" aria-label="Toggle password" onclick="togglePassword(this)">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row-between">
                        <label class="check">
                            <input type="checkbox" name="remember" value="1"/>
                            {{ __('menu.auth.remember_me') }}
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span class="label">{{ __('menu.auth.login_button') }}</span>
                        <span class="spinner"></span>
                    </button>
                </form>

                <div class="form-footer">
                    {{ __('menu.auth.secure_note') }}
                </div>

                @if(app()->environment('local'))
                    <div class="hint">
                        <span>admin@admin.com &nbsp;•&nbsp; admin</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(btn) {
        const input = document.getElementById('password');
        const icon = btn.querySelector('i');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('bx-show');
        icon.classList.toggle('bx-hide');
    }

    document.getElementById('loginForm').addEventListener('submit', function (event) {
        this.querySelector('.btn-submit').classList.add('loading');
    });
</script>
</body>
</html>