<!DOCTYPE html>
<html lang="id" data-theme="theme-default">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>Halaman Tidak Ditemukan | {{ config('app.name') }}</title>
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

        .error-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 18px;
        }

        .error-shell {
            width: 100%;
            max-width: 520px;
            text-align: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 52px 40px;
            box-shadow: 0 32px 80px -32px rgba(40, 43, 96, 0.35);
            animation: shellIn 0.85s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes shellIn {
            from { opacity: 0; transform: translateY(34px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .error-emoji {
            width: 84px;
            height: 84px;
            margin: 0 auto 22px;
            display: grid;
            place-items: center;
            border-radius: 26px;
            font-size: 40px;
            color: #fff;
            background: linear-gradient(135deg, #6d67e4, #4f46d5);
            box-shadow: 0 18px 40px -12px rgba(109, 103, 228, 0.7);
        }

        .error-code {
            font-size: 88px;
            font-weight: 800;
            letter-spacing: -3px;
            line-height: 1;
            color: var(--primary);
        }
        .error-code .zero {
            color: var(--accent);
        }

        .error-title {
            margin-top: 10px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .error-desc {
            margin-top: 12px;
            font-size: 14px;
            line-height: 1.7;
            color: var(--muted);
        }

        .error-actions {
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 22px;
            font-size: 14.5px;
            font-weight: 700;
            font-family: inherit;
            border-radius: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.25s, background 0.2s;
        }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #6d67e4, #4f46d5);
            border: none;
            box-shadow: 0 14px 30px -10px rgba(109, 103, 228, 0.7);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 20px 40px -12px rgba(109, 103, 228, 0.85); }
        .btn-ghost {
            color: var(--ink);
            background: #f7f8fd;
            border: 1.5px solid var(--line);
        }
        .btn-ghost:hover { background: #eceefb; }

        .error-foot {
            margin-top: 26px;
            font-size: 12.5px;
            color: #a3a8c3;
        }

        @media (max-width: 480px) {
            .error-wrap { padding: 14px; }
            .error-shell { padding: 40px 22px; border-radius: 22px; }
            .error-code { font-size: 68px; }
        }
    </style>
</head>
<body>
<div class="bg-wrap">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
</div>

<div class="error-wrap">
    <div class="error-shell">
        <div class="error-emoji"><i class="bx bx-map-alt"></i></div>
        <div class="error-code">4<span class="zero">0</span>4</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
            Halaman yang Anda cari mungkin telah dihapus, dipindahkan,
            atau tidak pernah tersedia. Periksa kembali alamatnya atau kembali ke beranda.
        </p>

        <div class="error-actions">
            <a class="btn btn-ghost" href="#" onclick="history.length > 1 ? history.back() : (location.href = '{{ route('home') }}'); return false;">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
            @auth
                <a class="btn btn-primary" href="{{ route('home') }}">
                    <i class="bx bx-home-alt"></i> Ke Beranda
                </a>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">
                    <i class="bx bx-log-in"></i> Masuk
                </a>
            @endauth
        </div>

        <div class="error-foot">{{ config('app.name') }}</div>
    </div>
</div>
</body>
</html>