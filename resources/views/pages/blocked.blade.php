<!DOCTYPE html>
<html lang="id" data-theme="theme-default">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>Akun Diblokir | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('logo-black.png') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,700;0,800&display=swap"
        rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}"/>

    <style>
        :root {
            --primary: #6d67e4;
            --primary-dark: #4f46d5;
            --ink: #23274b;
            --muted: #6a7090;
            --line: #e7e9f5;
            --bg: #f6f7fe;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Public Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(52% 52% at 12% 8%, rgba(109, 103, 228, 0.12), transparent 70%),
                radial-gradient(46% 46% at 88% 12%, rgba(0, 207, 243, 0.10), transparent 70%),
                radial-gradient(40% 40% at 90% 90%, rgba(255, 107, 107, 0.10), transparent 70%),
                var(--bg);
            color: var(--ink);
            position: relative;
            overflow: hidden;
        }

        .card {
            position: relative;
            width: 100%;
            max-width: 460px;
            margin: 18px;
            text-align: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 26px;
            padding: 48px 40px 42px;
            box-shadow: 0 32px 80px -32px rgba(40, 43, 96, 0.35);
            animation: cardIn 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(34px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .icon-wrap {
            width: 92px;
            height: 92px;
            margin: 0 auto 24px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 107, 107, 0.12);
            border: 1.5px solid rgba(255, 107, 107, 0.35);
            animation: pulse 2.4s ease-in-out infinite;
        }

        .icon-wrap i {
            font-size: 46px;
            color: #e8584f;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.30); transform: scale(1); }
            50%      { box-shadow: 0 0 0 18px rgba(255, 107, 107, 0); transform: scale(1.04); }
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        p {
            margin-top: 12px;
            color: var(--muted);
            font-size: 14.5px;
            line-height: 1.7;
        }

        .muted {
            margin-top: 8px;
            font-size: 13px;
            color: #a3a8c3;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 26px;
            padding: 14px 30px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #6d67e4, #4f46d5);
            border-radius: 14px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 14px 30px -10px rgba(109, 103, 228, 0.7);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(109, 103, 228, 0.85);
        }
    </style>
</head>
<body>

<div class="card">
    <div class="icon-wrap">
        <i class="bx bxs-lock-alt"></i>
    </div>
    <h1>{{ __('auth.account_blocked') }}</h1>
    <p>
        @if($name)
            {{ __('auth.blocked_message_named', ['name' => $name]) }}
        @else
            {{ __('auth.blocked_message') }}
        @endif
    </p>
    <p class="muted">{{ __('auth.blocked_hint') }}</p>
    <a href="{{ route('login') }}" class="btn">
        <i class="bx bx-log-in-circle"></i>
        {{ __('auth.back_to_login') }}
    </a>
</div>
</body>
</html>