<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasPolicyAccess = $user->can('Facilities-list') || 
                     $user->can('Payrollprofile-list') || 
                     $user->can('Loans-list') || 
                     $user->can('Loans-Settlement-list') || 
                     $user->can('Salaryaddition-list') || 
                     $user->can('Other-facilities-list') || 
                     $user->can('Salary-increment-list') || 
                     $user->can('Work-summary-list') || 
                     $user->can('Salary-preperation-list') || 
                     $user->can('Salary-schedule-list') || 
                     $user->can('Paysliplist-list');
  @endphp

  @if($hasPolicyAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="policymanagement">
       Policy Management<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @if($user->can('Facilities-list'))
            <li><a class="dropdown-item" href="{{ url('RemunerationList') }}" id="facilities">Facilities</a></li>
            @endif
            @if($user->can('Payrollprofile-list'))
            <li><a class="dropdown-item" href="{{ url('PayrollProfileList') }}" id="payrollprofile">Payroll Profile</a></li>
            @endif
            @if($user->can('Loans-list'))
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanList') }}" id="loans">Loans</a></li>
            @endif
            @if($user->can('Loan Approve'))
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanAdmin') }}">Loan Approval</a></li>
            @endif
            @if($user->can('Loans-Settlement-list'))
            <li><a class="dropdown-item" href="{{ url('EmployeeLoanInstallmentList') }}" id="loanSettlement">Loan Settlement</a></li>
            @endif
            @if($user->can('Salaryaddition-list'))
            <li><a class="dropdown-item" href="{{ url('EmployeeTermPaymentList') }}" id="SalaryAdditions">Salary Additions / Deduction</a></li>
            @endif
            @if($user->can('Other-facilities-list'))
            <li><a class="dropdown-item" href="{{ url('OtherFacilityPaymentList') }}" id="OtherFacilities">Other Facilities</a></li>
            @endif
            @if($user->can('Salary-increment-list'))
            <li><a class="dropdown-item" href="{{ url('SalaryIncrementList') }}" id="SalaryIncrements">Salary Increments</a></li>
            @endif
            @if($user->can('Work-summary-list'))
            <li><a class="dropdown-item" href="{{ url('SalaryProcessSchedule') }}" id="SalaryIncrements">Salary Schedule</a></li>
            @endif
            @if($user->can('Salary-preperation-list'))
            <li><a class="dropdown-item" href="{{ url('EmployeeWorkSummary') }}" id="Worksummary">Work Summary</a></li>
            @endif
            @if($user->can('Salary-schedule-list'))
            <li><a class="dropdown-item" href="{{ url('EmployeePayslipList') }}" id="SalaryPreperation">Salary Preperation</a></li>
            @endif
            @if($user->can('Paysliplist-list'))
            <li><a class="dropdown-item" href="{{ url('PayslipRegistry') }}" id="PayslipList">Payslip List</a></li>
            @endif
        </ul>
  </div>
  @endif

  @php
    $hasReportAccess = $user->can('Pay-Register-Report') || 
                     $user->can('OT-Report') || 
                     $user->can('EPF-ETF-Report') || 
                     $user->can('Salary-Sheet-report') || 
                     $user->can('Salary-sheet-bankslip-report') || 
                     $user->can('Salary-sheet-heldpayment-report') || 
                     $user->can('Sixmonths-report') || 
                     $user->can('Addition-report');
  @endphp

  @if($hasReportAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollreport">
      Reports <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @if($user->can('Pay-Register-Report'))
             <li><a class="dropdown-item" href="{{ url('ReportPayRegister') }}" id="payregister">Pay Register</a></li>
            @endif
            @if($user->can('OT-Report'))
             <li><a class="dropdown-item" href="{{ url('ReportEmpOvertime') }}" id="otreport">OT Report</a></li>
            @endif
            @if($user->can('EPF-ETF-Report'))
             <li><a class="dropdown-item" href="{{ url('ReportEpfEtf') }}" id="epfetf">EPF and ETF</a></li>
            @endif
            @if($user->can('Salary-Sheet-report'))
             <li><a class="dropdown-item" href="{{ url('ReportSalarySheet') }}" id="salarysheet">Salary Sheet</a></li>
            @endif
            @if($user->can('Salary-sheet-bankslip-report'))
             <li><a class="dropdown-item" href="{{ url('ReportSalarySheetBankSlip') }}" id="salarysheetbank">Salary Sheet - Bank Slip</a></li>
            @endif
            @if($user->can('Salary-sheet-heldpayment-report'))
             <li><a class="dropdown-item" href="{{ url('ReportHeldSalaries') }}" id="salaryheld">Salary Sheet - Held Payments</a></li>
            @endif
            @if($user->can('Sixmonths-report'))
             <li><a class="dropdown-item" href="{{ url('ReportSixMonth') }}" id="sixmonth">Six Month Report</a></li>
            @endif
            @if($user->can('Addition-report'))
             <li><a class="dropdown-item" href="{{ url('ReportAddition') }}" id="additionreport">Additions Report</a></li>
            @endif
        </ul>
  </div>
  @endif

  @php
    $hasStatementAccess = $user->can('Employee-Salary-Payment-Statement') || 
                        $user->can('Employee-Incentive-Statement') || 
                        $user->can('Bank-Advice-Statement') || 
                        $user->can('Pay-Summary-Statement') || 
                        $user->can('Employee-Salary-Journal-Statement') || 
                        $user->can('EPF-ETF-Journal-Statement');
  @endphp

  @if($hasStatementAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollststement">
      Statements <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('Employee-Salary-Payment-Statement'))
           <li><a class="dropdown-item" href="{{ url('EmpSalaryPayVoucher') }}">Employee Salary (Payment Voucher)</a></li>
          @endif
          @if($user->can('Employee-Incentive-Statement'))
           <li><a class="dropdown-item" href="{{ url('EmpIncentivePayVoucher') }}">Employee Incentive (Payment Voucher)</a></li>
          @endif
          @if($user->can('Bank-Advice-Statement'))
           <li><a class="dropdown-item" href="{{ url('ReportBankAdvice') }}">Bank Advice</a></li>
          @endif
          @if($user->can('Pay-Summary-Statement'))
           <li><a class="dropdown-item" href="{{ url('ReportPaySummary') }}">Pay Summary</a></li>
          @endif
          @if($user->can('Employee-Salary-Journal-Statement'))
           <li><a class="dropdown-item" href="{{ url('EmpSalaryJournalVoucher') }}">Employee Salary (Journal Voucher)</a></li>
          @endif
          @if($user->can('EPF-ETF-Journal-Statement'))
           <li><a class="dropdown-item" href="{{ url('EmpEpfEtfJournalVoucher') }}">EPF and ETF (Journal Voucher)</a></li>
          @endif
        </ul>
  </div>
  @endif
</div>