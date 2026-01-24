<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advance Slips</title>
  <style>
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 8px;
        margin: 0;
        padding: 0;
    }

    /* Each page container fits 2 payslips side by side */
    .page {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: nowrap;
        width: 100%;
        height: 50%;
        page-break-after: always;
    }

    .payslip {
        width: 48%;
        border: 1px solid #000;
        padding: 5mm;
        box-sizing: border-box;
        height: 8cm; /* Increased height to look proportional */
        page-break-inside: avoid;
    }

    .title {
        font-weight: bold;
        text-align: center;
        font-size: 10px;
        margin: 3px 0;
    }

    .divider {
        border-bottom: 1px dashed #000;
        margin: 2px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3px;
    }

    td {
        padding: 1px 0;
        vertical-align: top;
    }

    .employeecontent {
        text-align: left;
    }

    .amount {
        text-align: right;
    }

    .total {
        font-weight: bold;
    }

    .net-payable {
        font-weight: bold;
        font-size: 10px;
        padding-top: 8px;
    }

    /* Optional: a dotted vertical line between slips */
    .cut-line {
        border-left: 1px dashed #888;
        height: auto;
        margin: 0 2mm;
    }
  </style>
</head>
<body>
  @foreach(collect($employee_list)->chunk(2) as $pair)
    <div class="page">
      @foreach($pair as $row)
        <div class="payslip">
          <table>
            <tr><td colspan="2" class="title">Advance Slip</td></tr>
            <tr><td colspan="2" class="title">For the Month of {{$paymonth_name}}</td></tr>
            <tr><td colspan="2"><div class="divider"></div></td></tr>

            <tr>
              <td style="width:35%; padding-top:5px;">Employee No.</td>
              <td class="employeecontent" style="padding-top:5px;">:&nbsp;{{ $row['emp_epfno'] }}</td>
            </tr>
            <tr><td>Name</td><td class="employeecontent">:&nbsp;{{ $row['emp_name'] }}</td></tr>
            <tr><td>Department</td><td class="employeecontent">:&nbsp;{{ $row['emp_department'] }}</td></tr>
            <tr><td>Designation</td><td class="employeecontent">:&nbsp;{{ $row['emp_designation'] }}</td></tr>
            <tr><td colspan="2" style="padding-bottom:5px;"><div class="divider"></div></td></tr>
          </table>

          <table>
             @php 
                $tot_add = 0;
                $tot_ded = 0;
                $i=$row['id'];
                @endphp
                  @if(isset($emp_pay_add_details[$i]))
                @foreach ($emp_pay_add_details[$i] as $add_row)

            <tr>
              <td width="75%">{{ $add_row['payment_desc'] }}</td>
              <td width="25%" class="amount">{{ number_format((float)$add_row['entitle_amount'], 2, '.', '') }}</td>
            </tr>
            	@php $tot_add += $add_row['entitle_amount']; @endphp
                @endforeach
                @endif

            <tr><td colspan="2"><div class="divider"></div></td></tr>

            <tr><td class="total">Total Allowance</td><td class="amount total">{{ number_format((float)$tot_add, 2, '.', ',') }}</td></tr>

             @if(isset($emp_pay_ded_details[$i]))
                @foreach ($emp_pay_ded_details[$i] as $ded_row)

            <tr>
              <td width="75%">{{ $ded_row['payment_desc'] }}</td>
              <td width="25%" class="amount">({{ number_format((float)abs($ded_row['entitle_amount']), 2, '.', '') }})</td>
            </tr>
            @php $tot_ded += $ded_row['entitle_amount']; @endphp
                @endforeach
                @endif

            <tr><td colspan="2"><div class="divider"></div></td></tr>

            <tr><td class="total">Total Deduction</td><td class="amount total">({{ number_format((float)abs($tot_ded), 2, '.', ',') }})</td></tr>

            <tr><td colspan="2"><div class="divider"></div></td></tr>

            <tr><td class="net-payable">Net Amt. Payable</td><td class="amount net-payable">{{ number_format((float)$row['amount'], 2, '.', ',') }}</td></tr>
          </table>
        </div>

        @if (!$loop->last && $loop->count == 2)
          <div class="cut-line"></div>
        @endif
      @endforeach
    </div>
  @endforeach
</body>
</html>
