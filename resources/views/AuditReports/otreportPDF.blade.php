<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .report-table, .report-table th, .report-table td {
            border: 1px solid black;
        }
        .report-table th, .report-table td {
            padding: 2px;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach ($pdfData as $data)
    <table class="report-table">
        <tr>
            <td colspan="8" style="text-align: center; font-size: 18px; font-weight: bold; padding: 10px; border-bottom: none; border-right:none; border-left:none;">
                Employee OT Report
            </td>
        </tr>
        <tr class="titlerow">
            <td style="border-top: none; border-bottom: none; border-right:none; border-left:none; text-align:left;" colspan="4"><strong>Emp No:</strong> {{ $data['employee']->emp_id }} </td>
            <td style="border-top: none; border-bottom: none; border-left:none; border-right:none; text-align:left;"  colspan="4"><strong>Name:</strong> {{ $data['employee']->emp_fullname }} </td>
        </tr>
        <thead>
            <tr>
                <th class="nowrap">No</th>
                <th class="nowrap">Date</th>
                <th class="nowrap">Emp No</th>
                <th class="nowrap">Department</th>
                <th class="nowrap">In Time</th>
                <th class="nowrap">Out Time</th>
                <th class="nowrap">No of Hours</th>
                <th class="nowrap">Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['attendance'] as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance['date'] }}</td>
                <td>{{ $attendance['empno'] }}</td>
                <td>{{ $attendance['Department'] }}</td>
                <td>{{ $attendance['in_time'] }}</td>
                <td>{{ $attendance['out_time'] }}</td>
                <td>{{ $attendance['duration'] }}</td>
                <td> </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="page-break"></div>
@endforeach

</body>
</html>
