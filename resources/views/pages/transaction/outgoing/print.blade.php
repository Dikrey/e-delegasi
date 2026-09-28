<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.4cm 1.2cm 1.6cm;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1c1c2e;
        }
        .print-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            border-bottom: 3px double #222;
            padding-bottom: 10px;
        }
        .inst-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0 0 2px;
            text-transform: uppercase;
        }
        .inst-addr {
            font-size: 11px;
            color: #444;
            margin: 0;
        }
        .print-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 6px;
            padding: 8px 0;
            border-bottom: 1px solid #ccc;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin: 4px 0 10px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #666;
        }
        th {
            background: #2b2488;
            color: #fff;
            padding: 8px 9px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        td {
            padding: 7px 9px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background: #f4f3fb;
        }
        .num-col { width: 4%; text-align: center; }
        .strong-col { font-weight: 600; }
        .print-foot {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 28px;
            font-size: 11px;
        }
        .sign-block { text-align: center; }
        .sign-block .sign-line { margin: 52px 0 0; }
    </style>
</head>
<body onload="window.print()">

<div class="print-head">
    <div>
        <p class="inst-name">{{ $config['institution_name'] ?? config('app.name') }}</p>
        <p class="inst-addr">{{ $config['institution_address'] ?? '' }}</p>
        @if(!empty($config['institution_phone']) || !empty($config['institution_email']))
            <p class="inst-addr">
                {{ trim(($config['institution_phone'] ?? '') . ($config['institution_phone'] && $config['institution_email'] ? ' | ' : '') . ($config['institution_email'] ?? '')) }}
            </p>
        @endif
    </div>
    @if($config['pic'] ?? false)
        <div style="text-align: right;">
            <img src="{{ $config['pic'] }}" alt="" style="max-height: 64px;">
        </div>
    @endif
</div>

<h2 class="print-title">{{ $title }}</h2>

<div class="meta">
    @if($since && $until && $filter)
        <span><strong>{{ __('model.letter.' . $filter) }}:</strong> {{ "$since - $until" }}</span>
    @else
        <span><strong>{{ __('menu.agenda.agenda_range') }}:</strong> {{ __('menu.agenda.all') }}</span>
    @endif
    <span><strong>{{ __('menu.agenda.total_records') }}:</strong> {{ count($data) }}</span>
</div>

<table>
    <thead>
    <tr>
        <th class="num-col">No</th>
        <th>{{ __('model.letter.agenda_number') }}</th>
        <th>{{ __('model.letter.reference_number') }}</th>
        <th>{{ __('model.letter.to') }}</th>
        <th>{{ __('model.letter.letter_date') }}</th>
        <th>{{ __('model.letter.description') }}</th>
        <th>{{ __('model.letter.note') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $letter)
        <tr>
            <td class="num-col">{{ $index + 1 }}</td>
            <td class="strong-col">{{ $letter->agenda_number }}</td>
            <td>{{ $letter->reference_number }}</td>
            <td>{{ $letter->to }}</td>
            <td>{{ $letter->formatted_letter_date }}</td>
            <td>{{ $letter->description }}</td>
            <td>{{ $letter->note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="print-foot">
    <span>Dicetak pada {{ now()->isoFormat('dddd, D MMMM YYYY, HH:mm') }} oleh {{ auth()->user()->name }}</span>
    <div class="sign-block">
        <div>{{ $config['institution_name'] ?? config('app.name') }}, {{ now()->isoFormat('D MMMM YYYY') }}</div>
        <div class="sign-line">( {{ auth()->user()->name }} )</div>
    </div>
</div>

</body>
</html>