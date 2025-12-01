<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <style>
	@page {
	  font-size: 12px;
	  padding-left: 1px;
	  padding-right: 5px;
	}

	* {
	  font-family: Arial, sans-serif;
	}

	body {
	  align-items: center;
	  height: 100vh;
	}

.header {
    text-align: center;
    position: relative;
    padding-top: 15px;
}

.header h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.header .address {
    font-size: 14px;
    margin: 5px 0;
    font-weight: bold;
    color: #333;
}

.header .subtitle {
    font-size: 14px;
    margin: 5px 0;
    justify-content: center;
}

.header .subtitle .separator {
    font-weight: normal;
}

.header .date {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 14px;
    margin: 0;
    color: #333;
}


.custom-table {
    width: 100%;
    border-collapse: collapse;
    padding: 0 20px;
}

  /* Table header styling */
  .custom-table thead th {
    text-align: left;
    font-weight: bold;
    padding: 5px;
    color: #333;
  }

  /* Table body row styling */
  .custom-table tbody tr {
    border: none;
  }

  .custom-table tbody tr td {
    padding: 5px;
    font-size: 12px;
    color: #555;
  }

  /* Right-align numbers */
  .text-right {
    text-align: right;
  }

  /* Center-align numbers */
  .text-center {
    text-align: center;
  }

  /* Strong font weight for total rows */
  .strong {
    font-weight: bold;
  }
     
  </style>
</head>
<body>
    <div class="header" >
        <h1>{{session("company_name")}}</h1>
        <p class="address">{{session("company_address")}}</p>
        <p class="subtitle">
            Section Wise Pay  Summary For the Month of {{$paymonth_name}}
        </p>
    </div><br><br>

    <table class="custom-table">
        <thead>
            <tr>
                <td colspan="2" class="text-left">{{$department_name}}</td>
                <td class="text-right">{{ \Carbon\Carbon::now('Asia/Colombo')->format('d/m/Y') }}</td>
            </tr>
          <tr>
            <th width="50%" style="text-align:center; border-top:1px solid;  border-bottom:1px solid;">Type</th>
            <th width="30%" style="text-align:center; border-top:1px solid;  border-bottom:1px solid;">Amount</th>
            <th width="20%" style="text-align:center; border-top:1px solid;  border-bottom:1px solid;">No. of Emp.</th>
          </tr>
        </thead>
        <tbody>
          <tr data-figid="BASIC">
            <td>Basic Salary</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['BASIC']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['BASIC']['cnt'] }}</td>
          </tr>
          <tr data-figid="NOPAY">
            <td>Nopay</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['NOPAY']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['NOPAY']['cnt'] }}</td>
          </tr>
          <tr data-figid="SAL_AFT_NOPAY">
            <td>Salary After Nopay</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['SAL_AFT_NOPAY']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['SAL_AFT_NOPAY']['cnt'] }}</td>
          </tr>
          <tr data-figid="OTHRS1">
            <td>Overtime</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['OTHRS1']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['OTHRS1']['cnt'] }}</td>
          </tr>
          <tr data-figid="OTHRS2">
            <td>Holiday</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['OTHRS2']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['OTHRS2']['cnt'] }}</td>
          </tr>
          <tr data-figid="TOTAL_WITH_OT">
            <td></td>
            <td data-cap="amt" style=" border-top:2px double;" class="text-right">{{ number_format($payment_detail['TOTAL_WITH_OT']['amt'], 2) }}</td>
            <td></td>
          </tr>
          <tr data-figid="ATTBONUS_W">
            <td>Reimburse Traveling</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['ATTBONUS_W']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['ATTBONUS_W']['cnt'] }}</td>
          </tr>
          <tr data-figid="INCNTV_EMP">
            <td>Incentive</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['INCNTV_EMP']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['INCNTV_EMP']['cnt'] }}</td>
          </tr>
          <tr data-figid="INCNTV_DIR">
            <td>Directors Incentive</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['INCNTV_DIR']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['INCNTV_DIR']['cnt'] }}</td>
          </tr>
          <tr data-figid="tot_earn" class="strong">
            <td>Total Earning</td>
            <td data-cap="amt" class="text-right"  style="border-top:1px double; border-bottom:2px double;">{{ number_format($payment_detail['tot_earn']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['tot_earn']['cnt'] }}</td>
          </tr>
          <tr data-figid="EPF8">
            <td>EPF Contribution 8%</td>
            <td data-cap="amt" class="text-right" style="border-top:2px double;">{{ number_format(abs($payment_detail['EPF8']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center" >{{ $payment_detail['EPF8']['cnt'] }}</td>
          </tr>
          <tr data-figid="sal_adv">
            <td>Salary Advance</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['sal_adv']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['sal_adv']['cnt'] }}</td>
          </tr>
          <tr data-figid="ded_fund_1">
            <td>Funeral Fund</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['ded_fund_1']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['ded_fund_1']['cnt'] }}</td>
          </tr>
          <tr data-figid="ded_IOU">
            <td>I.O.U</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['ded_IOU']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['ded_IOU']['cnt'] }}</td>
          </tr>
          <tr data-figid="PAYE">
            <td>PAYEE</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['PAYE']['amt']), 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['PAYE']['cnt'] }}</td>
          </tr>
          <tr data-figid="add_transport">
            <td>Traveling</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['add_transport']['amt']), 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['add_transport']['cnt'] }}</td>
        </tr>
        <tr data-figid="LOAN">
            <td>Loan</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['LOAN']['amt']), 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['LOAN']['cnt'] }}</td>
        </tr>
        <tr data-figid="">
            <td>Loan-2</td>
            <td data-cap="amt"></td>
            <td data-cap="cnt" style="text-align:center;"></td>
        </tr>
        <tr data-figid="tot_ded">
            <td><strong>Total Deduction</strong></td>
            <td data-cap="amt" class="text-right" style="border-top:1px double; border-bottom:2px double;">{{ number_format(abs($payment_detail['tot_ded']['amt']), 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['tot_ded']['cnt'] }}</td>
        </tr>
          <tr data-figid="bal_earn" class="strong">
            <td>Balance</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['bal_earn']['amt'], 2) }}</td>
            <td data-cap="cnt" class="text-center">{{ $payment_detail['bal_earn']['cnt'] }}</td>
          </tr>
        
        <tr data-figid="EPF12">
            <td>Contribution EPF 12%</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['EPF12']['amt'], 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['EPF12']['cnt'] }}</td>
        </tr>
        <tr data-figid="ETF3">
            <td>Contribution EPF 3%</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['ETF3']['amt'], 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['ETF3']['cnt'] }}</td>
        </tr>
        <tr data-figid="">
            <td><strong> Payment Summary</strong></td>
            <td data-cap="amt"></td>
            <td data-cap="cnt" style="text-align:center;"></td>
        </tr>
        <tr data-figid="TOTAL_BANK">
            <td>Bank</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['TOTAL_BANK']['amt'], 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['TOTAL_BANK']['cnt'] }}</td>
        </tr>
        <tr data-figid="TOTAL_CASH">
            <td>Cash</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['TOTAL_CASH']['amt'], 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['TOTAL_CASH']['cnt'] }}</td>
        </tr>          
        <tr data-figid="bal_earn">
            <td></td>
            <td data-cap="amt" style="text-align:right; border-top:1.5px solid; border-bottom:1.5px solid;" >{{ number_format($payment_detail['bal_earn']['amt'], 2) }}</td>
            <td data-cap="cnt" style="text-align:center;">{{ $payment_detail['bal_earn']['cnt'] }}</td>
        </tr>
        <tr data-figid="tot_earn">
            <td>Total Earning</td>
            <td></td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['tot_earn']['amt'], 2) }}</td>
        </tr>
        <tr data-figid="ATTBONUS_W">
            <td>Reimburse Traveling</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['ATTBONUS_W']['amt'], 2) }}</td>
            <td style="text-align:center;" >{{ $payment_detail['ATTBONUS_W']['cnt'] }}</td>
        </tr>
        <tr data-figid="INCNTV_EMP">
            <td>Incentive</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['INCNTV_EMP']['amt'], 2) }}</td>
            <td style="text-align:center;" >{{ $payment_detail['INCNTV_EMP']['cnt'] }}</td>
        </tr>
        <tr data-figid="INCNTV_DIR">
            <td>Directors Incentive</td>
            <td data-cap="amt" class="text-right">{{ number_format($payment_detail['INCNTV_DIR']['amt'], 2) }}</td>
            <td style="text-align:center;" >{{ $payment_detail['INCNTV_DIR']['cnt'] }}</td>
        </tr>
        <tr data-figid="PAYE">
            <td>PAYEE</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['PAYE']['amt']), 2) }}</td>
            <td style="text-align:center;" >{{ $payment_detail['PAYE']['cnt'] }}</td>
        </tr>
        <tr data-figid="sal_adv">
            <td>Salary Advance</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['sal_adv']['amt']), 2) }}</td>
            <td style="text-align:center;"> {{ $payment_detail['sal_adv']['cnt'] }}</td>
        </tr>
        <tr data-figid="TOTAL_ALLWITHEPF">
            <td>EPF Contribution 8%</td>
            <td data-cap="amt" class="text-right">{{ number_format(abs($payment_detail['TOTAL_ALLWITHEPF']['amt']), 2) }}</td>
            <td data-cap="cnt" style="text-align:right;">{{ $payment_detail['TOTAL_ALLWITHEPF']['cnt'] }}</td>
        </tr>
        <tr data-figid="PAYMENT_SUSPENSE">
            <td>Salary Payment Suspense</td>
            <td data-cap="cnt">{{ $payment_detail['PAYMENT_SUSPENSE']['cnt'] }}</td>
            <td data-cap="amt"  style="text-align:right; border-top:2px double;" >{{ number_format($payment_detail['PAYMENT_SUSPENSE']['amt'], 2) }}</td>
        </tr>
        </tbody>
      </table>
</body>
</html>