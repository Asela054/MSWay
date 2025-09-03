<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasAttendanceReports = $user->can('attendance-report') ||
                          $user->can('late-attendance-report') ||
                          $user->can('leave-report') ||
                          $user->can('leave-balance-report') ||
                          $user->can('ot-report') ||
                          $user->can('no-pay-report');
  @endphp

  @if($hasAttendanceReports)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="employeereportmaster">
        Atte. & Leave Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @if($user->can('attendance-report'))
            <li><a class="dropdown-item" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a></li>
            @endif
            @if($user->can('late-attendance-report'))
            <li><a class="dropdown-item" href="{{ route('LateAttendance')}}">Late Attendance</a></li>
            @endif
            @if($user->can('leave-report'))
            <li><a class="dropdown-item" href="{{ route('leaveReport')}}">Leave Report</a></li>
            @endif
            @if($user->can('leave-balance-report'))
            <li><a class="dropdown-item" href="{{ route('LeaveBalance')}}">Leave Balance</a></li>
            @endif
            @if($user->can('ot-report'))
            <li><a class="dropdown-item" href="{{ route('ot_report')}}">O.T. Report</a></li>
            @endif
            @if($user->can('no-pay-report'))
            <li><a class="dropdown-item" href="{{ route('no_pay_report')}}">No Pay Report</a></li>
            @endif
            @if($user->can('employee-absent-report'))
            <li><a class="dropdown-item" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a></li>
            @endif
        </ul>
  </div>
  @endif

  @php
    $hasEmployeeDetails = $user->can('employee-report') || $user->can('employee-bank-report');
  @endphp

  @if($hasEmployeeDetails)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="employeedetailsreport">
        Employee Details Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('employee-report'))
          <li><a class="dropdown-item" href="{{ route('EmpoloyeeReport')}}">Employees Report</a></li>
          @endif
          @if($user->can('employee-bank-report'))
          <li><a class="dropdown-item" href="{{ route('empBankReport')}}">Employee Banks</a></li>
          @endif
          @if($user->can('employee-resign-report'))
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a></li>
          @endif
          @if($user->can('employee-recruitment-report'))
          <li><a class="dropdown-item" href="{{ route('employee_recirument_report') }}">Employee Recruitment Report</a></li>
          @endif
          @if($user->can('employee-time-in-out-report'))
          <li><a class="dropdown-item" href="{{ route('employeeattendancereport') }}">Employee Time In-Out Report</a></li>
          @endif
          @if($user->can('employee-actual-ot-report'))
          <li><a class="dropdown-item" href="{{ route('employeeotreport') }}">Employee Ot Report</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasDeptWiseReports = $user->can('department-wise-ot-report') || 
                         $user->can('department-wise-leave-report') || 
                         $user->can('department-wise-attendance-report');
  @endphp

  @if($hasDeptWiseReports)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="departmentvisereport">
      Department Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('department-wise-attendance-report'))
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('departmentwise_attendancereport') }}">Department-Wise Attendance Report</a></li>
          @endif
          @if($user->can('department-wise-ot-report'))
          <li><a class="dropdown-item" href="{{ route('departmentwise_otreport')}}"> Department-Wise O.T. Report</a></li>
          @endif
          @if($user->can('department-wise-leave-report'))
          <li><a class="dropdown-item" href="{{ route('departmentwise_leavereport')}}">Department-Wise Leave Report</a></li>
          @endif
          @if($user->can('department-wise-leave-report'))
          <li><a class="dropdown-item" href="{{ route('joballocationreport')}}">Job Allocation Report</a></li>
          @endif
        </ul>
  </div>
  @endif

  @if($user->can('attendance-audit-report'))
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="compliancereport">
      Audit Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('auditattendancereport') }}">Attendance Time In-Out Report</a></li>
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('auditpayregister') }}">Audit Pay Report</a></li>
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('AuditReportSalarySheet') }}">Audit Salary Sheet</a></li>
        </ul>
  </div>
  @endif
</div>