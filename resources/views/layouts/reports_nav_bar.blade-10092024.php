
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('employee-report')
      || auth()->user()->can('attendance-report')
      || auth()->user()->can('late-attendance-report')
      || auth()->user()->can('leave-report')
      || auth()->user()->can('employee-bank-report')
      || auth()->user()->can('leave-balance-report')
      || auth()->user()->can('ot-report')
      || auth()->user()->can('no-pay-report'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="employeereportmaster">
        Employee Report<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('employee-report')
            <li><a class="dropdown-item" href="{{ route('EmpoloyeeReport')}}">Employees Report</a></li>
            @endcan
            @can('attendance-report')
            <li><a class="dropdown-item" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a></li>
            @endcan
            @can('late-attendance-report')
            <li><a class="dropdown-item" href="{{ route('LateAttendance')}}">Late Attendance</a></li>
            @endcan
            @can('leave-report')
            <li><a class="dropdown-item" href="{{ route('leaveReport')}}">Leave Report</a></li>
            @endcan
            @can('employee-bank-report')
            <li><a class="dropdown-item" href="{{ route('empBankReport')}}">Employee Banks</a></li>
            @endcan
            @can('leave-balance-report')
            <li><a class="dropdown-item" href="{{ route('LeaveBalance')}}">Leave Balance</a></li>
            @endcan
            @can('ot-report')
            <li><a class="dropdown-item" href="{{ route('ot_report')}}">O.T. Report</a></li>
            @endcan
            @can('no-pay-report')
            <li><a class="dropdown-item" href="{{ route('no_pay_report')}}">No Pay Report</a></li>
            @endcan
            @can('employee-resign-report')
            <li><a class="dropdown-item" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a></li>
            @endcan
            @can('employee-absent-report')
            <li><a class="dropdown-item" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a></li>
            @endcan
        </ul>
  </div>
  @endif

</div>


