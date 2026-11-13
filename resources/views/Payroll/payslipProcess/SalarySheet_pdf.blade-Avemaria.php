<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        /* A5 portrait: 148mm x 210mm */
        @page {
            size: 148mm 210mm;
            margin: 8mm 8mm 8mm 8mm;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header .company {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .header .addr {
            font-size: 13px;
            margin: 0 0 10px 0;
        }
        .header .title {
            font-size: 13px;
            margin: 0;
        }

        table.slip {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }
        table.slip td {
            border: 1px solid #000;
            padding: 5px 7px;
            font-size: 13px;
            height: 18px;
        }
        table.slip td.label {
            width: 50%;
            text-align: left;
        }
        table.slip td.value {
            width: 50%;
            text-align: center;
        }
        .printed {
            font-size: 8px;
            margin-top: 4px;
            text-align: right;
        }
    </style>
</head>

<body>

    @php
        $check = 0;
        $fmt = function ($v) {
            return number_format((float)$v, 2, '.', ',');
        };
    @endphp

    @for ($slipcnt = 0; $slipcnt < count($emp_array); $slipcnt++)
        @if(isset($emp_array[$slipcnt]))
            @php
                $row = $emp_array[$slipcnt];

                $salary      = (float)$row['BASIC'] + (float)$row['BRA_I'] + (float)$row['add_bra2'];
                $publicHol   = (float)$row['OTHRS2'];
                $pbm         = (float)$row['ATTBONUS_W'];
                $salaryTotal = $salary + $publicHol + $pbm;
            @endphp

            <div class="header">
                @if (!empty($row['companylogo']))
                    <img src="{{ $row['companylogo'] }}" width="60" height="60" alt="">
                @endif
                <p class="company">{{ $company_name }}</p>
                <p class="addr">{{ $company_addr }}</p>
                <p class="title">Monthly Salary Slip</p>
            </div>

            <table class="slip">
                <tbody>
                    <tr>
                        <td class="label">AMH Salary Pay Sheet</td>
                        <td class="value">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="label">Month of</td>
                        <td class="value">{{ $paymonth_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Name</td>
                        <td class="value">{{ $row['emp_first_name'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">E.P.F No</td>
                        <td class="value">{{ $row['emp_epfno'] }}</td>
                    </tr>
                    <tr>
                        <td class="label">O.T Hours</td>
                        <td class="value">{{ $fmt($row['OTAMT1']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Salary</td>
                        <td class="value">{{ $fmt($salary) }}</td>
                    </tr>
                    <tr>
                        <td class="label">P.H</td>
                        <td class="value">{{ $fmt($publicHol) }}</td>
                    </tr>
                    <tr>
                        <td class="label">P.B.M</td>
                        <td class="value">{{ $fmt($pbm) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Salary Total</td>
                        <td class="value">{{ $fmt($salaryTotal) }}</td>
                    </tr>
                    <tr>
                        <td class="label">E.P.F 8%</td>
                        <td class="value">{{ $fmt($row['EPF8']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">O.T</td>
                        <td class="value">{{ $fmt($row['OTHRS1']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">N.P</td>
                        <td class="value">{{ $fmt($row['NOPAY']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Advance</td>
                        <td class="value">{{ $fmt($row['sal_adv']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Incentives</td>
                        <td class="value">{{ $fmt($row['INCNTV_EMP']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Night Duty Allowance</td>
                        <td class="value">{{ $fmt($row['INCNTV_DIR']) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Loan Deduction</td>
                        <td class="value">{{ $fmt($row['LOAN']) }}</td>
                    </tr>

                    {{-- Extra deductions: shown only when the employee has them, so no data is lost --}}
                    @if((float)$row['PAYE'] != 0)
                    <tr>
                        <td class="label">APIT</td>
                        <td class="value">{{ $fmt($row['PAYE']) }}</td>
                    </tr>
                    @endif
                    @if((float)$row['ded_fund_1'] != 0)
                    <tr>
                        <td class="label">Funeral Fund</td>
                        <td class="value">{{ $fmt($row['ded_fund_1']) }}</td>
                    </tr>
                    @endif
                    @if((float)$row['ded_IOU'] != 0)
                    <tr>
                        <td class="label">IOU</td>
                        <td class="value">{{ $fmt($row['ded_IOU']) }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td class="label"><b>Balance</b></td>
                        <td class="value"><b>{{ $fmt($row['NETSAL']) }}</b></td>
                    </tr>
                    <tr>
                        <td class="label">&nbsp;</td>
                        <td class="value">{{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="text-align:center;">Signature</td>
                        <td class="value">Date</td>
                    </tr>
                </tbody>
            </table>

            <div class="printed">Printed On : {{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y H:i:s') }}</div>

            @if($slipcnt < count($emp_array) - 1)
                <div style="page-break-after: always;"></div>
            @endif
        @endif
        @php $check++ @endphp
    @endfor

</body>

</html>