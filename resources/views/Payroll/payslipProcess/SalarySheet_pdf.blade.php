<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        @page {
            size: 220mm 140mm;
            margin: 5mm 5mm 5mm 5mm;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            /* border: 1px solid #000;
            border-radius: 5px; */
            overflow: hidden;
        }

        th,
        td {
            padding: 4px;
        }

        .bodytd {
            border: 1px solid;
        }

        .table-wrapper {
            border: 1px solid #000;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    @php 
        $check=0;
        
        // Define Language Mapping
        $labels = [
            1 => [ // English
                'pay_advice' => 'Pay Advice for the Month of',
                'name' => 'NAME',
                'nic' => 'NIC NO',
                'epf_no' => 'EPF NO',
                'basic' => 'BASIC SALARY',
                'nopay' => 'NO PAY',
                'net_basic' => 'NET BASIC',
                'receivables' => 'RECEIVABLES',
                'gross_pay' => 'GROSS PAY',
                'total_ded' => 'TOTAL DEDUCTIONS',
                'overtime' => 'OVERTIME',
                'holiday' => 'HOLIDAY',
                'reimb' => 'REIMB.TRAV.',
                'incentive' => 'INCENTIVE',
                'dir_incentive' => 'DIR. INCENTIVE',
                'deduction' => 'DEDUCTION',
                'net_salary' => 'NET SALARY',
                'total' => 'TOTAL',
                'bank_msg' => 'YOUR NET SALARY AS ABOVE IS SENT TO',
                'acc_no' => 'ACCOUNT NO',
                'attendance' => 'ATTENDANCE SUMMARY',
                'working' => 'WORKING',
                'nopay_days' => 'NO PAY DAYS',
                'late' => 'LATE ATTENDANCE H/M',
                'employer' => 'EMPLOYER',
                'signature' => "EMPLOYEE'S SIGNATURE"
            ],
            2 => [ // Sinhala
                'pay_advice' => 'වැටුප් විස්තරය - මාසය:',
                'name' => 'නම',
                'nic' => 'ජා.හැ.අංකය',
                'epf_no' => 'අර්ථසාධක අංකය',
                'basic' => 'මූලික වැටුප',
                'nopay' => 'නිවාඩු අඩුකිරීම්',
                'net_basic' => 'ශුද්ධ මූලික වැටුප',
                'receivables' => 'ලැබිය යුතු දීමනා',
                'gross_pay' => 'මුළු දළ වැටුප',
                'total_ded' => 'මුළු අඩුකිරීම්',
                'overtime' => 'අතිකාල දීමනා',
                'holiday' => 'නිවාඩු දින දීමනා',
                'reimb' => 'ගමන් වියදම්',
                'incentive' => 'දිරි දීමනා',
                'dir_incentive' => 'අධ්‍යක්ෂ දිරි දීමනා',
                'deduction' => 'අඩුකිරීම්',
                'net_salary' => 'ශුද්ධ වැටුප',
                'total' => 'එකතුව',
                'bank_msg' => 'ඔබගේ ශුද්ධ වැටුප පහත ගිණුමට බැර කර ඇත',
                'acc_no' => 'ගිණුම් අංකය',
                'attendance' => 'පැමිණීමේ සාරාංශය',
                'working' => 'වැඩ කළ දින',
                'nopay_days' => 'නොපැමිණි දින',
                'late' => 'ප්‍රමාද පැමිණීම්',
                'employer' => 'සේව්‍යයා',
                'signature' => 'සේවකයාගේ අත්සන'
            ],
            3 => [ // Tamil
                'pay_advice' => 'சம்பள விபரம் - மாதம்:',
                'name' => 'பெயர்',
                'nic' => 'தேசிய அடையாள அட்டை',
                'epf_no' => 'ஈ.பி.எப் இலக்கம்',
                'basic' => 'அடிப்படைச் சம்பளம்',
                'nopay' => 'சம்பளமற்ற விடுமுறை',
                'net_basic' => 'தேறிய அடிப்படைச் சம்பளம்',
                'receivables' => 'பெறத்தக்கவை',
                'gross_pay' => 'மொத்தச் சம்பளம்',
                'total_ded' => 'மொத்தக் கழிவுகள்',
                'overtime' => 'மேலதிக நேரம்',
                'holiday' => 'விடுமுறை கொடுப்பனவு',
                'reimb' => 'பயணக் கொடுப்பனவு',
                'incentive' => 'ஊக்கத்தொகை',
                'dir_incentive' => 'நிர்வாக ஊக்கத்தொகை',
                'deduction' => 'கழிவுகள்',
                'net_salary' => 'தேறிய சம்பளம்',
                'total' => 'மொத்தம்',
                'bank_msg' => 'உங்களது தேறிய சம்பளம் வங்கிக்கு அனுப்பப்பட்டுள்ளது',
                'acc_no' => 'கணக்கு இலக்கம்',
                'attendance' => 'வருகை விபரம்',
                'working' => 'வேலை நாட்கள்',
                'nopay_days' => 'சம்பளமற்ற நாட்கள்',
                'late' => 'தாமதமான வருகை',
                'employer' => 'முதலாளி',
                'signature' => 'ஊழியர் கையொப்பம்'
            ]
        ];

        // Corrected variable logic
        $lang = $labels[$paysheet_language] ?? $labels[1];
    @endphp

    @for ($slipcnt=0; $slipcnt < count($emp_array); $slipcnt++) 
        @if(isset($emp_array[$slipcnt])) 
            @php
                $row = $emp_array[$slipcnt];
                $netbasicValue = ($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']) - $row['NOPAY']; 
                $totalearnValue = $row['OTHRS1'] + $row['OTHRS2'] + $row['ATTBONUS_W'] + $row['INCNTV_EMP'] + $row['INCNTV_DIR']; 
                $netbasic = number_format((float)$netbasicValue, 2, '.' , ',' );
                $totalearn = number_format((float)$totalearnValue, 2, '.' , ',' ); 
                $grosspay = number_format((float)($netbasicValue + $totalearnValue), 2, '.' , ',' ); 
            @endphp 

            @php
                $fontcss = "";
                if($paysheet_language == 2) { // Sinhala
                    $fontcss = "sinhala-text";
                } else if($paysheet_language == 3) { // Tamil
                    $fontcss = "";
                }
            @endphp

            <div class="table-wrapper">
                <table id="maintable">
                    <tbody>
                        <tr>
                            <td colspan="3"><strong style="font-size: 14px;">{{ $company_name }}</strong></td>
                            <td colspan="3" style="text-align:right;" class="{{ $fontcss }}"><strong>{{ $lang['pay_advice'] }} {{$paymonth_name}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border-top: none; border-right:none;" colspan="3"><b>{{ $company_addr }}</b></td>
                            <td style="border-top: none; border-right:none;text-align:right;" colspan="3"><b></b></td>
                        </tr>
                        <tr>
                            <td class="bodytd" colspan="2" style="border-left: none;border-right: none;  border-bottom:none;">
                                <table class="innertables" style="border: none;">
                                    <tr>
                                        <td style="border:none;" class="{{ $fontcss }}">{{ $lang['name'] }}</td>
                                        <td style="border-right:none;">: &nbsp; {{ $row['emp_first_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['nic'] }}</td>
                                        <td>: &nbsp;{{ $row['emp_national_id'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['epf_no'] }}</td>
                                        <td>: &nbsp;{{ $row['emp_epfno'] }}</td>
                                    </tr>
                                </table>
                                <table class="innertables" style="border: none;">
                                    <tr>
                                        <td style=" border-top: 1px solid black;" class="{{ $fontcss }}">{{ $lang['basic'] }} </td>
                                        <td style="text-align:right; border-top: 1px solid black;">
                                            {{ number_format((float)($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']), 2, '.', ',') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['nopay'] }} </td>
                                        <td style="text-align:right;">{{ number_format((float)$row['NOPAY'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['net_basic'] }}</td>
                                        <td style="text-align:right;  border-top: 1px solid black;">{{ $netbasic }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['receivables'] }}</td>
                                        <td style="text-align:right;">{{ $totalearn }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['gross_pay'] }}</td>
                                        <td style="text-align:right;  border-top: 1px solid black;">{{ $grosspay }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['total_ded'] }}</td>
                                        <td style="text-align:right; border-bottom: 1px solid black;">
                                            {{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="bodytd" width="30%" colspan="2" style="vertical-align: top; border-right: none; border-bottom:none; ">
                                <table class="innertables" style="border: none;  width: 100%;">
                                    <tr>
                                        <td colspan="4" style="text-align: center; border-bottom: 1px solid black; border-left: none;" class="{{ $fontcss }}">
                                            <b>{{ $lang['receivables'] }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['overtime'] }}</td>
                                        <td style="text-align: center;">{{ number_format((float)$row['OTAMT1'], 2, '.', ',') }}</td>
                                        <td style="text-align: center;">
                                            {{ (float)$row['OTHRS1'] != 0 ? number_format((float)$row['OTHRS1'] / (float)$row['OTAMT1'], 2, '.', ',') : '00.00' }}
                                        </td>
                                        <td style="text-align: right;">{{ number_format((float)$row['OTHRS1'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['holiday'] }}</td>
                                        <td style="text-align: center;">{{ number_format((float)$row['OTAMT2'], 2, '.', ',') }}</td>
                                        <td style="text-align: center;">
                                            {{ (float)$row['OTHRS2'] != 0 ? number_format((float)$row['OTHRS2'] / (float)$row['OTAMT2'], 2, '.', ',') : '00.00' }}
                                        </td>
                                        <td style="text-align: right;">{{ number_format((float)$row['OTHRS2'], 2, '.', ',') }}</td>
                                    </tr>
                                    @if((float)$row['ATTBONUS_W'] != 0)
                                    <tr>
                                        <td colspan="2" class="{{ $fontcss }}">{{ $lang['reimb'] }}</td>
                                        <td colspan="2" style="text-align: right;">{{ number_format((float)$row['ATTBONUS_W'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif

                                    @if((float)$row['INCNTV_EMP'] != 0)
                                    <tr>
                                        <td colspan="2" class="{{ $fontcss }}">{{ $lang['incentive'] }}</td>
                                        <td colspan="2" style="text-align: right;">{{ number_format((float)$row['INCNTV_EMP'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif

                                    @if((float)$row['INCNTV_DIR'] != 0)
                                    <tr>
                                        <td colspan="2" class="{{ $fontcss }}">{{ $lang['dir_incentive'] }}</td>
                                        <td colspan="2" style="text-align: right;">{{ number_format((float)$row['INCNTV_DIR'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </td>
                            <td class="bodytd" width="30%" colspan="2" style="vertical-align: top; border-bottom:none; border-right: none; ">
                                <table class="innertables" style="border: none;  width: 100%;">
                                    <tr>
                                        <td colspan="2" style="text-align: center; border-bottom: 1px solid black; border-left: none;" class="{{ $fontcss }}">
                                            <b>{{ $lang['deduction'] }}</b></td>
                                    </tr>
                                    <tr>
                                        <td> EPF 8%</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</td>
                                    </tr>

                                    @if((float)$row['ded_fund_1'] != 0)
                                    <tr>
                                        <td> FUNERAL FUND</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['ded_fund_1'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif

                                    @if((float)$row['LOAN'] != 0)
                                    <tr>
                                        <td>LOAN</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['LOAN'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                    @if((float)$row['PAYE'] != 0)
                                    <tr>
                                        <td>APIT</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['PAYE'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                    @if((float)$row['sal_adv'] != 0)
                                    <tr>
                                        <td>ADVANCE</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['sal_adv'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                    @if((float)$row['ded_IOU'] != 0)
                                    <tr>
                                        <td>IOU</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['ded_IOU'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td class="bodytd {{ $fontcss }}" style=" border-left:none; border-right:none;border-bottom:none;border-top:none;">{{ $lang['net_salary'] }}</td>
                            <td class="bodytd" style="border-left:none; border-bottom:none;border-top:none;  border-right:none; text-align: right;">
                                &nbsp;{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</td>
                            <td class="bodytd {{ $fontcss }}" style="border-right:none;border-bottom:none; border-right:none; border-top:none;">{{ $lang['total'] }}</td>
                            <td class="bodytd" style="border-left:none; border-bottom:none; border-right:none;  text-align: right;">
                                &nbsp;{{ $totalearn }}</td>
                            <td class="bodytd {{ $fontcss }}" style="text-align: left; border-right:none; border-bottom:none; border-top:none;">{{ $lang['total'] }}</td>
                            <td class="bodytd" style="text-align: right; border-left:none; border-bottom:none; border-right:none;">
                                {{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="bodytd {{ $fontcss }}" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; text-align:center;">{{ $lang['bank_msg'] }}</td>
                            <td class="bodytd {{ $fontcss }}" colspan="2" style="text-align:center;  border-right:none;"><b>{{ $lang['attendance'] }}</b></td>
                            <td class="bodytd {{ $fontcss }}" colspan="2" style="text-align:center;  border-right:none;"><b>{{ $lang['employer'] }}</b></td>
                        </tr>

                        <tr>
                            <td class="bodytd" colspan="2" style="vertical-align: top; border-left:none; border-right:none;border-bottom:none; border-top:none; text-align:center;">
                                <table class="innertables" style="border: none;">
                                    <tr>
                                        <td style="text-align:center;">{{ $row['bank_name'] }} - {{ $row['bank_branch'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;" class="{{ $fontcss }}">{{ $lang['acc_no'] }} - {{ $row['bank_accno'] }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2" class="bodytd" style=" vertical-align: top; border-right:none;border-bottom:none; border-right:none; border-top:none;  ">
                                <table class="innertables" style="border: none;">
                                    <tr>
                                        <td colspan="2" class="{{ $fontcss }}">{{ $lang['working'] }}</td>
                                        <td style="text-align: right;">{{ number_format((float)$row['work_week_days'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="{{ $fontcss }}">{{ $lang['nopay_days'] }}</td>
                                        <td style="text-align: center;">
                                            {{ (float)$row['NOPAY'] != 0 ? number_format((float)$row['NOPAY'] / (float)$row['NOPAYCNT'], 2, '.', ',') : '00.00' }}
                                        </td>
                                        <td style="text-align: right; ">{{ number_format((float)$row['NOPAYCNT'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="{{ $fontcss }}">{{ $lang['late'] }}</td>
                                        <td style="text-align: right;">00.00</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2" class="bodytd" style=" vertical-align: top; text-align: left; border-right:none; border-bottom:none; border-top:none;">
                                <table class="innertables" style="border: none;">
                                    <tr>
                                        <td>EPF 12% </td>
                                        <td style="text-align: right;">{{ number_format((float)$row['EPF12'], 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td>ETF 3% </td>
                                        <td style="text-align: right;">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td class="bodytd" colspan="4" style=" border-left:none; border-right:none;border-bottom:none; padding-top:5px; font-size:13px; text-align:left; vertical-align:top;">&nbsp;</td>
                            <td class="bodytd {{ $fontcss }}" colspan="2" style="text-align:center;border-bottom:none; border-top:none; border-right:none; padding-top:15px;">
                                .......................................... <br>{{ $lang['signature'] }}<br />
                                <span style="font-size:8px;">Printed On : {{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y H:i:s') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($slipcnt < count($emp_array) - 1)
                <div style="page-break-after: always;"></div>
            @endif
        @endif
        @php $check++ @endphp
    @endfor

</body>

</html>