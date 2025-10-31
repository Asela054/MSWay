<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="employeereportmaster">
        Atte. & Leave Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            <li><a class="dropdown-item" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a></li>
            <li><a class="dropdown-item" href="{{ route('LateAttendance')}}">Late Attendance</a></li>
            <li><a class="dropdown-item" href="{{ route('leaveReport')}}">Leave Report</a></li>
            <li><a class="dropdown-item" href="{{ route('LeaveBalance')}}">Leave Balance</a></li>
            <li><a class="dropdown-item" href="{{ route('ot_report')}}">O.T. Report</a></li>
            <li><a class="dropdown-item" href="{{ route('no_pay_report')}}">No Pay Report</a></li>
            <li><a class="dropdown-item" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a></li>
        </ul>
  </div>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="employeedetailsreport">
        Employee Details Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('EmpoloyeeReport')}}">Employees Report</a></li>
          <li><a class="dropdown-item" href="{{ route('empBankReport')}}">Employee Banks</a></li>
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a></li>
          <li><a class="dropdown-item" href="{{ route('employee_recirument_report') }}">Employee Recruitment Report</a></li>
          <li><a class="dropdown-item" href="{{ route('employeeattendancereport') }}">Employee Time In-Out Report</a></li>
          <li><a class="dropdown-item" href="{{ route('employeeotreport') }}">Employee Ot Report</a></li>
        </ul>
  </div>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="departmentvisereport">
      Department Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('departmentwise_attendancereport') }}">Department-Wise Attendance Report</a></li>
          <li><a class="dropdown-item" href="{{ route('departmentwise_otreport')}}"> Department-Wise O.T. Report</a></li>
          <li><a class="dropdown-item" href="{{ route('departmentwise_leavereport')}}">Department-Wise Leave Report</a></li>
          <li><a class="dropdown-item" href="{{ route('joballocationreport')}}">Job Allocation Report</a></li>
        </ul>
  </div>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="compliancereport">
      Audit Reports<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('auditattendancereport') }}">Attendance Time In-Out Report</a></li>
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('auditpayregister') }}">Audit Pay Report</a></li>
          <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('AuditReportSalarySheet') }}">Audit Salary Sheet</a></li>
        </ul>
  </div>
</div>