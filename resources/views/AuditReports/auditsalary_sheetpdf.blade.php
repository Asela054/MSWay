<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <style>
	@page { 
        size: 220mm 140mm;
        margin: 5mm 5mm 5mm 5mm;
        font-family: Arial, sans-serif;
        font-size:13px;
	    }

     table{
        width: 100%;
        border-collapse: separate;
        border-spacing: 0; 
        border: 1px solid #000;
        border-radius: 5px;
        overflow: hidden;
       
     }
     th, td {
        padding: 4px;
    }
    .bodytd{
        border: 1px solid;
    }
   
  </style>
</head>
<body>

    @php $check=0 @endphp
    
        @for ($slipcnt=0;$slipcnt<count($emp_array);$slipcnt++)
  
            @if(isset($emp_array[$slipcnt]))
            
            @php $row=$emp_array[$slipcnt] @endphp
            
            @php 
                     $netbasicValue = ($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']) - $row['NOPAY'];
                     $totalearnValue = $row['OTHRS1'] + $row['OTHRS2'] + $row['ATTBONUS_W'] + $row['INCNTV_EMP'] + $row['INCNTV_DIR'];
   
                    $netbasic = number_format((float)$netbasicValue, 2, '.', ',');
                    $totalearn = number_format((float)$totalearnValue, 2, '.', ',');
                    
                    $grosspay = number_format((float)($netbasicValue + $totalearnValue), 2, '.', ',');
             @endphp

              <table id="maintable">
                  <tbody>
                    <tr>
                      <td colspan="3"><strong style="font-size: 14px;">{{ $company_name }}</strong></td>
                      <td colspan="3" style="text-align:right;"><strong>Pay Advice for the Month of {{$paymonth_name}}</strong></td>
                  </tr>
                  <tr >
                      <td  style="border-top: none; border-right:none;" colspan="3" ><b>{{ $company_addr }}</b></td>
                      <td  style="border-top: none; border-right:none;text-align:right;" colspan="3" ><b></b></td>
                  </tr>
                  <tr>
                    <td class="bodytd" colspan="2" style="border-left: none;border-right: none;  border-bottom:none;">
                        <table class="innertables" style="border: none;">
                            <tr>
                                <td style="border:none;">NAME</td>
                                <td style="border-right:none;">: &nbsp; {{ $row['emp_first_name'] }}</td>
                            </tr>
                            <tr>
                                <td>NIC NO</td>
                                <td>: &nbsp;{{ $row['emp_national_id'] }}</td>
                            </tr>
                            <tr>
                                <td>EPF NO </td>
                                <td>: &nbsp;{{ $row['emp_epfno'] }}</td>
                            </tr>
                        </table>
                        <table class="innertables" style="border: none;">
                            <tr>
                                <td style=" border-top: 1px solid black;">BASIC SALARY </td>
                                <td style="text-align:right; border-top: 1px solid black;" >{{ number_format((float)($row['BASIC'] + $row['BRA_I'] + $row['add_bra2']), 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td>NO PAY </td>
                                <td style="text-align:right;">{{ number_format((float)$row['NOPAY'], 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td>NET BASIC</td>
                                <td style="text-align:right;  border-top: 1px solid black;">{{ $netbasic }}</td>
                            </tr>
                            <tr>
                                <td>RECEIVABLES</td>
                                <td style="text-align:right;">{{ $totalearn }}</td>
                            </tr>
                            <tr>
                                <td>GROSS PAY</td>
                                <!--td style="text-align:right;  border-top: 1px solid black;">{{ $grosspay }}</td-->
                                <td style="text-align:right;  border-top: 1px solid black;">{{ number_format((float)$row['tot_earn'], 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td>TOTAL DEDUCTIONS</td>
                                <td style="text-align:right; border-bottom: 1px solid black;">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="bodytd" width="30%"colspan="2" style="vertical-align: top; border-right: none; border-bottom:none; ">
                        <table class="innertables" style="border: none;  width: 100%;">
                            <tr>
                                <td colspan="4" style="text-align: center; border-bottom: 1px solid black; border-left: none;">
                                    <b>RECEIVABLES</b>
                                </td>
                            </tr>
                            <tr>
                                <td>OVERTIME</td>
                                <td style="text-align: center;">{{ number_format((float)$row['OTAMT1'], 2, '.', ',') }}</td>
                                <td style="text-align: center;">{{ (float)$row['OTHRS1'] != 0 ? number_format((float)$row['OTHRS1'] / (float)$row['OTAMT1'], 2, '.', ',') : '00.00' }}</td>
                                <td  style="text-align: right;">{{ number_format((float)$row['OTHRS1'], 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td> HOLIDAY </td>
                                <td style="text-align: center;">{{ number_format((float)$row['OTAMT2'], 2, '.', ',') }}</td>
                                <td style="text-align: center;">{{ (float)$row['OTHRS2'] != 0 ? number_format((float)$row['OTHRS2'] / (float)$row['OTAMT2'], 2, '.', ',') : '00.00' }}</td>
                                <td  style="text-align: right;">{{ number_format((float)$row['OTHRS2'], 2, '.', ',') }}</td>
                            </tr>
                            @if((float)$row['ATTBONUS_W'] != 0)
                            <tr>
                                <td colspan="2">Attendance Allow.</td>
                                <td colspan="2" style="text-align: right;">{{ number_format((float)$row['ATTBONUS_W'], 2, '.', ',') }}</td>
                            </tr>
                            @endif

                            @if((float)$row['INCNTV_EMP'] != 0)
                            @endif
                            <tr>
                                <td colspan="2">PROD. INCENTIVE</td>
                                <td colspan="2" style="text-align: right;">{{ number_format((float)$row['INCNTV_EMP'], 2, '.', ',') }}</td>
                            </tr>
                            

                            @if((float)$row['INCNTV_DIR'] != 0)
                            <tr>
                                <td colspan="2">Other Addition</td>
                                <td colspan="2" style="text-align: right;">{{ number_format((float)$row['INCNTV_DIR'], 2, '.', ',') }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                    <td class="bodytd" width="30%"colspan="2" style="vertical-align: top; border-bottom:none; border-right: none; ">
                        <table class="innertables"  style="border: none;  width: 100%;">
                            <tr>
                                <td colspan="2" style="text-align: center; border-bottom: 1px solid black; border-left: none;"><b>DEDUCTION</b></td>
                            </tr>
                            <tr>
                                <td> EPF 8%</td>
                                <td style="text-align: right;">{{ number_format((float)$row['EPF8'], 2, '.', ',') }}</td>
                            </tr>

                            @if((float)$row['ded_fund_1'] != 0)
                            @endif
                            <tr>
                                <td>Bank Charges</td>
                                <td style="text-align: right;">{{ number_format((float)$row['ded_fund_1'], 2, '.', ',') }}</td>
                            </tr>
                            

                            @if((float)$row['LOAN'] != 0)
                            @endif
                            <tr>
                                <td>LOAN</td>
                                <td style="text-align: right;">{{ number_format((float)$row['LOAN'], 2, '.', ',') }}</td>
                            </tr>
                            
                            
                            @if((float)$row['PAYES'] != 0)
                            <tr>
                                <td>APIT</td>
                                <td style="text-align: right;">{{ number_format((float)$row['PAYES'], 2, '.', ',') }}</td>
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
                                <td>Late Deduct.</td>
                                <td style="text-align: right;">{{ number_format((float)$row['ded_IOU'], 2, '.', ',') }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>

                      <tr>
                          <td class="bodytd" style=" border-left:none; border-right:none;border-bottom:none;border-top:none;">NET SALARY</td>
                          <td class="bodytd" style="border-left:none; border-bottom:none;border-top:none;  border-right:none; text-align: right;"> &nbsp;{{ number_format((float)$row['NETSAL'], 2, '.', ',') }}</td>
                          <td class="bodytd" style="border-right:none;border-bottom:none; border-right:none; border-top:none;">TOTAL</td>
                          <td class="bodytd" style="border-left:none; border-bottom:none; border-right:none;  text-align: right;">&nbsp;{{ $totalearn }}</td>
                          <td class="bodytd" style="text-align: left; border-right:none; border-bottom:none; border-top:none;">TOTAL</td>
                          <td class="bodytd" style="text-align: right; border-left:none; border-bottom:none; border-right:none;">{{ number_format((float)$row['tot_ded'], 2, '.', ',') }}</td>
                      </tr>
                      <tr>
                          <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; text-align:center;">YOUR NET SALARY AS ABOVE IS SEND TO</td>
                          <td class="bodytd" colspan="2" style="text-align:center;  border-right:none;"><b>ATTENDANCE SUMMARY</b></td>
                          <td class="bodytd" colspan="2" style="text-align:center;  border-right:none;"><b>EMPLOYER</b></td>
                      </tr>

                      <tr>
                        <td class="bodytd" colspan="2" style="vertical-align: top; border-left:none; border-right:none;border-bottom:none; border-top:none; text-align:center;">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td style="text-align:center;">{{ $row['bank_name'] }} - {{ $row['bank_branch'] }}  </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">ACCOUNT NO - {{ $row['bank_accno'] }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                        <td  colspan="2" class="bodytd" style=" vertical-align: top; border-right:none;border-bottom:none; border-right:none; border-top:none;  ">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td colspan="2">WORKING</td>
                                    <td style="text-align: right;">{{ number_format((float)$row['work_week_days'], 2, '.', ',') }}</td>
                                 </tr>
                                 <tr>
                                    <td>NO PAY DAYS</td>
                                    <td style="text-align: center;">{{ (float)$row['NOPAY'] != 0 ? number_format((float)$row['NOPAY'] / (float)$row['NOPAYCNT'], 2, '.', ',') : '00.00' }}</td>
                                    <td style="text-align: right; ">{{ number_format((float)$row['NOPAYCNT'], 2, '.', ',') }}</td>
                                 </tr>
                                 <tr>
                                    <td colspan="2">LATE ATTENDANCE H/M</td>
                                    <td style="text-align: right;">00.00</td>
                                 </tr>
                                
                            </table>
                        </td>
                        <td colspan="2" class="bodytd"
                            style=" vertical-align: top; text-align: left; border-right:none; border-bottom:none; border-top:none;">
                            <table class="innertables" style="border: none;">
                                <tr>
                                    <td>EPF 12% </td>
                                    <td style="text-align: right;">
                                        {{ number_format((float)$row['EPF12'], 2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <td>ETF 3% </td>
                                    <td style="text-align: right;">{{ number_format((float)$row['ETF3'], 2, '.', ',') }}
                                    </td>
                                </tr>

                            </table>
                        </td>
                      </tr>

                      <tr>
                        <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; "></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none;  border-right:none;"><b></b></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none; border-top:none; border-right:none;"></td>
                    </tr>
                      <tr>
                        <td class="bodytd" colspan="2" style=" border-left:none; border-right:none;border-bottom:none; border-top:none; padding-top:15px; font-size:11px; text-align:left; vertical-align:bottom;">Printed On :  {{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y H:i:s') }}</td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none;  border-top:none; border-right:none; padding-top:15px;"><b></b></td>
                        <td class="bodytd" colspan="2" style="text-align:center;border-bottom:none; border-top:none; border-right:none; padding-top:15px;">
                          .......................................... <br>EMPLOYEE'S SIGNATURE</td>
                    </tr>
                  </tbody>
              </table>
            @endif
            
            @php $check++ @endphp
        	
        @endfor
      
  </body>
</html>