<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="policymanagement">
      Policy Management<span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
      <li><a class="dropdown-item" href="{{ url('RemunerationList') }}" id="facilities">Facilities</a></li>
      <li><a class="dropdown-item" href="{{ url('PayrollProfileList') }}" id="payrollprofile">Payroll Profile</a></li>
      <li><a class="dropdown-item" href="{{ url('EmployeeLoanList') }}" id="loans">Loans</a></li>
      <li><a class="dropdown-item" href="{{ url('EmployeeLoanAdmin') }}">Loan Approval</a></li>
      <li><a class="dropdown-item" href="{{ url('EmployeeLoanInstallmentList') }}" id="loanSettlement">Loan
          Settlement</a></li>
      <li><a class="dropdown-item" href="{{ url('EmployeeTermPaymentList') }}" id="SalaryAdditions">Salary Additions /
          Deduction</a></li>
      <li><a href="{{ url('EmployeeExtraPaymentList') }}" id="SalaryInAdvance" class="dropdown-item">Advance Payments</a></li>
      <li><a class="dropdown-item" href="{{ url('OtherFacilityPaymentList') }}" id="OtherFacilities">Other
          Facilities</a></li>
      <li><a class="dropdown-item" href="{{ url('SalaryIncrementList') }}" id="SalaryIncrements">Salary Increments</a>
      </li>
      <li><a class="dropdown-item" href="{{ url('SalaryProcessSchedule') }}" id="SalaryIncrements">Salary Schedule</a>
      </li>
      <li><a class="dropdown-item" href="{{ url('EmployeeWorkSummary') }}" id="Worksummary">Work Summary</a></li>
      <li><a class="dropdown-item" href="{{ url('EmployeePayslipList') }}" id="SalaryPreperation">Salary Preperation</a>
      </li>
      <li><a class="dropdown-item" href="{{ url('PayslipRegistry') }}" id="PayslipList">Payslip List</a></li>
    </ul>
  </div>
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollreport">
      Reports <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
      <li><a class="dropdown-item" href="{{ url('ReportPayRegister') }}" id="payregister">Pay Register</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportEmpOvertime') }}" id="otreport">OT Report</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportEpfEtf') }}" id="epfetf">EPF & ETF Report</a></li>
      <li><a href="{{ url('ReportExtraPayRegister') }}" id="employeeAdvancePayRegister" class="dropdown-item">Advance Payment Register</a></li>
      <li><a href="{{ url('ReportExtraPaymentList') }}" id="employeeAdvancePaySlips" class="dropdown-item">Advance/Bonus Sheet</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportSalarySheet') }}" id="salarysheet">Salary Sheet</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportSalarySheetBankSlip') }}" id="salarysheetbank">Salary Sheet -
          Bank Slip</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportHeldSalaries') }}" id="salaryheld">Salary Sheet - Held
          Payments</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportSixMonth') }}" id="sixmonth">Six Month Report</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportAddition') }}" id="additionreport">Additions Report</a></li>
    </ul>
  </div>
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="payrollststement">
      Statements <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
      <li><a class="dropdown-item" href="{{ url('EmpSalaryPayVoucher') }}">Employee Salary (Payment Voucher)</a></li>
      <li><a class="dropdown-item" href="{{ url('EmpIncentivePayVoucher') }}">Employee Incentive (Payment Voucher)</a>
      </li>
      <li><a class="dropdown-item" href="{{ url('ReportBankAdvice') }}">Bank Advice</a></li>
      <li><a class="dropdown-item" href="{{ url('ReportPaySummary') }}">Pay Summary</a></li>
      <li><a class="dropdown-item" href="{{ url('EmpSalaryJournalVoucher') }}">Employee Salary (Journal Voucher)</a>
      </li>
      <li><a class="dropdown-item" href="{{ url('EmpEpfEtfJournalVoucher') }}">EPF and ETF (Journal Voucher)</a></li>
    </ul>
  </div>
</div>