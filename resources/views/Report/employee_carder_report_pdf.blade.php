<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Carder Report</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #000; padding: 3px 4px; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .title { background: #1F4E78; color: #fff; font-size: 17px; font-weight: bold; text-align: center; padding: 9px 0; border: 1px solid #000; }
        .meta { font-weight: bold; margin: 5px 0 2px 0; }
        .meta-light { margin: 0 0 8px 0; }
        .section { background: #D9EAD3; font-weight: bold; text-align: left; font-size: 10px; }
        .section-blue { background: #C5D3FF; font-weight: bold; text-align: left; font-size: 12px; }
        .head-blue th { background: #1F4E78; color: #fff; text-align: center; }
        .head-orange th { background: #FFC000; color: #000; text-align: center; }
        .center { text-align: center; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .total-yellow td { background: #FFFF00; font-weight: bold; text-align: center; font-size: 10px; }
        .total-green td { background: #92D050; font-weight: bold; text-align: center; font-size: 10px; }
        .grand td { background: #FFFF00; font-weight: bold; text-align: center; font-size: 14px; }
        .empty { text-align: center; color: #555; font-style: italic; }
        .note { font-size: 8px; font-style: italic; margin: -8px 0 12px 0; }
        .block { page-break-inside: avoid; }
        table.fx { table-layout: fixed; margin-bottom: 0; margin-top: -1px; page-break-inside: avoid; }
        table.fx.first { margin-top: 0; }
        .gap { height: 14px; }
        .note-yellow { background: #FFFF00; font-weight: bold; text-align: left; }
        .note-green { background: #92D050; font-weight: bold; text-align: left; }
    </style>
</head>
<body>

<div class="title">EMPLOYEE CARDER REPORT</div>
<div class="meta">Period - {{ $report['from_label'] }} to {{ $report['to_label'] }}</div>
<div class="meta-light">Generated on - {{ $report['generated_at'] }}</div>

{{-- ============ Recruitments / Resignations / Confirmed ============ --}}
@php
    $movementSections = array(
        array('Recruitments', 'Joined Date', $report['recruitments']),
        array('Resignations', 'Resignation Date', $report['resignations']),
        array('Confirmed Recruitments', 'Confirmed Date', $report['confirmed']),
    );
@endphp

@foreach ($movementSections as $section)
    @php $no = 0; @endphp
    <table class="fx first">
        <colgroup><col style="width:7%"><col><col style="width:28%"><col style="width:15%"><col style="width:9%"></colgroup>
        <thead>
            <tr><th colspan="5" class="section">{{ $section[0] }}</th></tr>
            <tr class="head-blue">
                <th>No</th>
                <th>Designation</th>
                <th>Company</th>
                <th>{{ $section[1] }}</th>
                <th>Count</th>
            </tr>
        </thead>
        @if (count($section[2]) == 0)
            <tbody><tr><td colspan="5" class="empty">No records for the selected period</td></tr></tbody>
        @endif
    </table>
    @foreach ($section[2] as $g)
        <table class="fx">
            <colgroup><col style="width:7%"><col><col style="width:28%"><col style="width:15%"><col style="width:9%"></colgroup>
            <tbody>
                @foreach ($g['rows'] as $row)
                    @php $no++; @endphp
                    <tr>
                        <td class="center">{{ $no }}</td>
                        <td class="left">{{ $row['designation'] }}</td>
                        @if ($loop->first)
                            <td class="left" rowspan="{{ count($g['rows']) }}">{{ $g['company'] }}</td>
                        @endif
                        <td class="center">{{ $row['date'] }}</td>
                        <td class="center">{{ $row['count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
    <div class="gap"></div>
@endforeach

{{-- ============ Recruitments and Resignations (summary) ============ --}}
<table style="width:45%" class="block">
    <thead>
        <tr><th colspan="2" class="section">Recruitments and Resignations</th></tr>
        <tr class="head-blue"><th>Metric</th><th style="width:25%">Count</th></tr>
    </thead>
    <tbody>
        @foreach ($report['summary'] as $row)
            <tr>
                <td class="bold left">{{ $row[0] }}</td>
                <td class="bold center">{{ $row[1] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ============ Age Carder Summary ============ --}}
<table class="fx first">
    <colgroup><col style="width:6%"><col style="width:26%"><col><col style="width:11%"><col style="width:11%"><col style="width:11%"></colgroup>
    <thead>
        <tr><th colspan="6" class="section">Age Carder Summary</th></tr>
        <tr class="head-blue">
            <th>No</th>
            <th>Business Unit</th>
            <th>Company</th>
            <th>Male</th>
            <th>Female</th>
            <th>Count</th>
        </tr>
    </thead>
    @if (count($report['carder']['groups']) == 0)
        <tbody><tr><td colspan="6" class="empty">No records for the selected period</td></tr></tbody>
    @endif
</table>

@foreach ($report['carder']['groups'] as $g)
    <table class="fx">
        <colgroup><col style="width:6%"><col style="width:26%"><col><col style="width:11%"><col style="width:11%"><col style="width:11%"></colgroup>
        <tbody>
            @foreach ($g['rows'] as $row)
                <tr>
                    @if ($loop->first)
                        <td class="center" rowspan="{{ count($g['rows']) }}">{{ $loop->parent->iteration }}</td>
                        <td class="left" rowspan="{{ count($g['rows']) }}">{{ $g['business_unit'] }}</td>
                    @endif
                    <td class="left">{{ $row['company'] }}</td>
                    <td class="center">{{ $row['male'] }}</td>
                    <td class="center">{{ $row['female'] }}</td>
                    <td class="center">{{ $row['count'] }}</td>
                </tr>
            @endforeach
            @if (count($g['rows']) > 1)
                <tr class="total-yellow">
                    <td colspan="3">Total - {{ $g['business_unit'] }}</td>
                    <td>{{ $g['male'] }}</td>
                    <td>{{ $g['female'] }}</td>
                    <td>{{ $g['count'] }}</td>
                </tr>
            @endif
        </tbody>
    </table>
@endforeach

@if (count($report['carder']['groups']) > 0)
    <table class="fx">
        <colgroup><col style="width:6%"><col style="width:26%"><col><col style="width:11%"><col style="width:11%"><col style="width:11%"></colgroup>
        <tbody>
            <tr class="total-green">
                <td colspan="3">Total - Male / Female</td>
                <td>{{ $report['carder']['male'] }}</td>
                <td>{{ $report['carder']['female'] }}</td>
                <td>{{ $report['carder']['count'] }}</td>
            </tr>
            <tr class="grand">
                <td colspan="3">Grand Total</td>
                <td colspan="3">{{ $report['carder']['count'] }}</td>
            </tr>
        </tbody>
    </table>
@endif
<div class="gap"></div>

{{-- ============ Service Period / Age Wise ============ --}}
@php
    $matrixSections = array(
        array('Service Period Analysis', $report['service']),
        array('Age Wise Analysis', $report['age']),
    );
@endphp

@foreach ($matrixSections as $section)
    @php
        $m = $section[1];
        $span = count($m['headers']) + 2;
    @endphp
    <table>
        <thead>
            <tr><th colspan="{{ $span }}" class="section-blue">{{ $section[0] }}</th></tr>
            <tr class="head-orange">
                <th>Company</th>
                @foreach ($m['headers'] as $h)
                    <th style="width:11%">{{ $h }}</th>
                @endforeach
                <th style="width:9%">Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($m['rows'] as $row)
                <tr>
                    <td class="left">{{ $row['label'] }}</td>
                    @foreach ($row['values'] as $v)
                        <td class="center">{{ $v }}</td>
                    @endforeach
                    <td class="center bold">{{ $row['count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ $span }}" class="empty">No records for the selected period</td></tr>
            @endforelse

            @if (count($m['rows']) > 0)
                <tr class="total-yellow">
                    <td class="left">Grand Total</td>
                    @foreach ($m['totals'] as $v)
                        <td>{{ $v }}</td>
                    @endforeach
                    <td>{{ $m['count'] }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    @if (!empty($m['missing_dob']))
        <div class="note">Note: {{ $m['missing_dob'] }} employee(s) have no valid birthday and are not included in this table.</div>
    @endif
@endforeach

{{-- ============ Additional Notes ============ --}}
<div class="block">
    <table class="fx first">
        <tbody>
            <tr><td class="note-yellow">Additional Notes</td></tr>
            <tr><td class="note-green">Employee Disciplinary Update</td></tr>
            @foreach ($report['notes'] as $note)
                <tr><td class="left">* {{ $note }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:14px; font-size:10px;">Thank you</div>
    @if (!empty($report['prepared_by']))
        <div style="margin-top:28px; font-size:10px; font-weight:bold;">{{ $report['prepared_by'] }}</div>
    @endif
</div>

</body>
</html>