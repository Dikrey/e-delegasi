<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('model.disposition.print_title') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 16mm;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Georgia, 'Segoe UI', Arial, serif;
            font-size: 12px;
            color: #1b1f2b;
            background: #e8ebf3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== Toolbar (layar) ===== */
        .toolbar {
            position: sticky; top: 0; z-index: 50;
            display: flex; justify-content: center; gap: 10px;
            background: #1f2340; padding: 12px; box-shadow: 0 4px 18px rgba(20,23,60,.25);
        }
        .toolbar .btn-back,
        .toolbar .btn-print {
            border: none; border-radius: 8px; padding: 9px 22px;
            font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; font-family: 'Segoe UI', sans-serif;
        }
        .toolbar .btn-back { background: #eef0f7; color: #1f2340; }
        .toolbar .btn-print { background: #1f7a3d; color: #fff; }

        /* ===== Lembar ===== */
        .sheet {
            max-width: 210mm;
            margin: 22px auto 0;
            background: #fff;
            padding: 0 0 34px;
            box-shadow: 0 14px 45px rgba(20,23,60,.22);
        }

        /* Pita atas berwarna */
        .accent-band {
            height: 10px;
            background: linear-gradient(90deg, #14532d, #1f7a3d 45%, #3aa15f 80%, #7bc47f);
            position: relative;
        }

        /* Kop surat */
        .kop {
            text-align: center;
            padding: 20px 32px 12px;
            border-bottom: 3px double #22272e;
            position: relative;
        }
        .kop .kop-badge {
            width: 52px; height: 52px;
            margin: 0 auto 8px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            border: 2px solid #1f7a3d;
            box-shadow: 0 0 0 3px rgba(31,122,61,.12);
            overflow: hidden;
        }
        .kop .kop-badge img {
            width: 44px; height: 44px;
            object-fit: contain;
            display: block;
        }
        .kop .prov {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 0 2px;
            color: #14532d;
        }
        .kop .dinas {
            font-size: 15.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin: 0 0 4px;
        }
        .kop .alamat {
            font-size: 10px;
            color: #4b5563;
            margin: 0;
        }

        /* Judul lembar */
        .sheet-title-wrap {
            padding: 14px 32px 4px;
            text-align: center;
        }
        .sheet-title {
            display: inline-block;
            position: relative;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #14532d;
            margin: 0;
            padding: 0 28px 6px;
        }
        .sheet-title::after {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 130px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #1f7a3d, transparent);
        }

        .sheet-body {
            padding: 14px 32px 0;
        }

        /* Kotak dengan judul-header berwarna */
        .box {
            border: 1.5px solid #c9d6cd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }
        .box > .box-title {
            background: #14532d;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 12px;
        }
        .box > .box-body {
            padding: 10px 12px;
        }

        /* Grid utama */
        .dispo-grid {
            display: grid;
            grid-template-columns: 55% 45%;
            gap: 12px;
            align-items: stretch;
        }
        @media (max-width: 560px) {
            .dispo-grid { grid-template-columns: 1fr; }
        }

        .meta-row {
            display: flex;
            align-items: baseline;
            margin-bottom: 6px;
            font-size: 11.5px;
            gap: 6px;
        }
        .meta-row .meta-label {
            font-weight: 700;
            min-width: 96px;
            color: #374151;
        }
        .meta-row .meta-value {
            flex: 1;
            word-break: break-word;
        }
        .meta-row .meta-value .divider-dot {
            font-weight: 700;
            margin-right: 4px;
        }

        .big-label {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 10px 0 3px;
            font-size: 10.5px;
            color: #14532d;
        }
        .big-content {
            min-height: 46px;
            line-height: 1.55;
            white-space: pre-wrap;
            border-top: 1px dashed #cdd7d0;
            padding-top: 5px;
        }

        .check-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .check-list li {
            display: flex;
            align-items: baseline;
            gap: 8px;
            padding: 3.5px 0;
            font-size: 11.5px;
        }
        .check-list .box-mark {
            flex-shrink: 0;
            width: 13px; height: 13px;
            border: 1.5px solid #1f7a3d;
            border-radius: 3px;
            display: inline-block;
            position: relative;
            top: 2px;
        }
        .check-list li.checked .box-mark {
            background: #1f7a3d;
        }
        .check-list li.checked .box-mark::after {
            content: "\2713";
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            position: absolute;
            left: 1px;
            top: -2px;
        }
        .check-list li.checked { font-weight: 600; }
        .no-check { color: #9aa3ad; }

        /* Instruksi */
        .full-box { margin-top: 12px; }
        .full-box .instruksi-content {
            min-height: 52px;
            white-space: pre-wrap;
            line-height: 1.55;
        }

        /* Verifikasi */
        .verify-box { margin-top: 12px; }
        .verify-box .verify-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 18px;
        }
        @media (max-width: 560px) {
            .verify-box .verify-grid { grid-template-columns: 1fr; }
        }
        .verify-box .verify-note {
            grid-column: 1 / -1;
            white-space: pre-wrap;
            line-height: 1.5;
            padding-top: 6px;
            border-top: 1px dashed #cdd7d0;
            margin-top: 4px;
        }

        /* Tanda tangan */
        .sign-area {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 26px;
            gap: 30px;
            padding: 0 32px;
        }
        .sign-block {
            text-align: center;
            font-size: 11.5px;
            flex: 1;
        }
        .sign-block .sign-role {
            font-size: 11.5px;
        }
        .sign-block .sign-line {
            margin-top: 74px;
            border-bottom: 1.5px solid #22272e;
            padding: 0 26px 2px;
            min-width: 170px;
        }
        .sign-block .sign-name {
            margin-top: 3px;
            font-weight: 700;
            text-transform: uppercase;
            word-break: break-word;
            padding: 0 6px;
        }

        /* Footer otomatis */
        .foot-auto {
            margin: 24px 32px 0;
            padding-top: 9px;
            border-top: 1px dashed #b8c2bb;
            font-size: 9.5px;
            color: #6b7280;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .sheet { margin: 0; max-width: none; box-shadow: none; }
            .accent-band { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @media screen and (max-width: 560px) {
            .kop { padding: 16px 18px 12px; }
            .sheet-body { padding: 12px 16px 0; }
            .sign-area { padding: 0 16px; gap: 14px; flex-direction: column; align-items: stretch; }
            .sign-block .sign-line { min-width: 0; }
            .foot-auto { margin: 20px 16px 0; }
        }
    </style>
</head>
<body>

<div class="no-print toolbar">
    <a href="{{ route('transaction.disposition.index', $letter) }}" class="btn-back">&larr; {{ __('menu.general.back') }}</a>
    <button class="btn-print" onclick="window.print()">{{ __('menu.general.print') }}</button>
</div>

<div class="sheet">

    <div class="accent-band"></div>

    {{-- Kop dinas --}}
    <div class="kop">
        <div class="kop-badge">
            <img src="{{ asset('img/logo/sumaterautara.png') }}" alt="Logo Sumatera Utara">
        </div>
        <p class="prov">Pemerintah Provinsi Sumatera Utara</p>
        <p class="dinas">Dinas Pertanian dan Ketahanan Pangan</p>
        <p class="alamat">
            {{ $config['institution_address'] ?? 'Jl. ...' }}
            @if(!empty($config['institution_phone']) || !empty($config['institution_email']))
                &nbsp;|&nbsp;
                {{ trim(($config['institution_phone'] ?? '') . ($config['institution_phone'] && $config['institution_email'] ? ' - ' : '') . ($config['institution_email'] ?? '')) }}
            @endif
        </p>
    </div>

    <div class="sheet-title-wrap">
        <h2 class="sheet-title">{{ __('model.disposition.print_title') }}</h2>
    </div>

    <div class="sheet-body">

        <div class="dispo-grid">
            {{-- Kolom kiri: identitas surat --}}
            <div class="box">
                <div class="box-title">{{ __('model.disposition.reference_number') }}</div>
                <div class="box-body">
                    <div class="meta-row">
                        <span class="meta-label">{{ __('model.letter.agenda_number') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->letter?->agenda_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">{{ __('model.disposition.reference_number') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->letter?->reference_number }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">{{ __('model.disposition.letter_date') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->letter?->formatted_letter_date }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">{{ __('model.disposition.received_at') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->formatted_received_at ?: '-' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">{{ __('model.disposition.due_date') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->formatted_due_date }}</span>
                    </div>

                    <div class="big-label">{{ __('model.disposition.content') }}</div>
                    <div class="big-content">{{ $data->content }}</div>

                    @if($data->note)
                        <div class="big-label">{{ __('model.disposition.note') }}</div>
                        <div class="big-content">{{ $data->note }}</div>
                    @endif
                </div>
            </div>

            {{-- Kolom kanan: diteruskan & hormat --}}
            <div style="display:grid; grid-template-rows:auto 1fr; gap:12px;">
                <div class="box">
                    <div class="box-title">{{ __('model.disposition.forwarded_to') }}</div>
                    <div class="box-body">
                        <ul class="check-list">
                            @forelse($data->forwarded_labels as $label)
                                <li class="checked"><span class="box-mark"></span><span>{{ $label }}</span></li>
                            @empty
                                <li class="no-check"><span class="box-mark"></span><span>-</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="box">
                    <div class="box-title">{{ __('model.disposition.honor') }}</div>
                    <div class="box-body">
                        <ul class="check-list">
                            @forelse($data->honor_labels as $label)
                                <li class="checked"><span class="box-mark"></span><span>{{ $label }}</span></li>
                            @empty
                                <li class="no-check"><span class="box-mark"></span><span>-</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instruksi --}}
        <div class="box full-box">
            <div class="box-title">{{ __('model.disposition.instruction') }}</div>
            <div class="box-body">
                @if($data->instruction)
                    <div class="instruksi-content">{{ $data->instruction }}</div>
                @else
                    <div class="instruksi-content no-check">{{ __('model.disposition.options.no_instruction') }}</div>
                @endif
            </div>
        </div>

        {{-- Verifikasi sekretaris --}}
        <div class="box verify-box">
            <div class="box-title">{{ __('model.disposition.verify_sheet') }}</div>
            <div class="box-body verify-grid">
                @if($data->verified_at)
                    <div class="meta-row">
                        <span class="meta-label" style="min-width:130px;">{{ __('model.disposition.is_received') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span><strong>{{ $data->is_received ? __('model.disposition.received_yes') : __('model.disposition.received_no') }}</strong></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label" style="min-width:130px;">{{ __('model.disposition.options.direction') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span><strong>{{ $data->direction }}</strong></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label" style="min-width:130px;">{{ __('model.disposition.verified_by') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->verifier?->name }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label" style="min-width:130px;">{{ __('model.disposition.verified_at') }}</span>
                        <span class="meta-value"><span class="divider-dot">:</span>{{ $data->verified_at->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</span>
                    </div>
                    @if($data->verification_note)
                        <div class="verify-note"><strong>{{ __('model.disposition.verification_note') }}:</strong><br>{{ $data->verification_note }}</div>
                    @endif
                @else
                    <div class="verify-note no-check">{{ __('model.disposition.not_verified') }}.</div>
                @endif
            </div>
        </div>

        {{-- Tanda tangan --}}
        <div class="sign-area">
            <div class="sign-block">
                <div class="sign-role">{{ __('model.disposition.forwarded_to_options.sekretaris') }}</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $data->verifier?->name ?? '( ........ )' }}</div>
            </div>
            <div class="sign-block">
                <div class="sign-role">{{ $config['institution_name'] ?? config('app.name') }}</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $data->user?->name }}</div>
            </div>
        </div>

        <div class="foot-auto">
            <span>{{ __('model.disposition.print_foot', ['system_name' => $config['app_name'] ?? config('app.name')]) }}</span>
            <span>{{ now()->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</span>
        </div>

    </div>

</div>

</body>
</html>