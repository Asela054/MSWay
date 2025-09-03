
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('attendance-sync')
  || auth()->user()->can('attendance-incomplete-data-list')
  || auth()->user()->can('attendance-list')
  || auth()->user()->can('attendance-create')
  || auth()->user()->can('attendance-edit')
  || auth()->user()->can('attendance-delete')
  || auth()->user()->can('attendance-approve')
  || auth()->user()->can('late-attendance-create')
  || auth()->user()->can('late-attendance-approve')
  || auth()->user()->can('late-attendance-list')
  || auth()->user()->can('attendance-incomplete-data-list')
  || auth()->user()->can('ot-approve')
  || auth()->user()->can('ot-list')
  || auth()->user()->can('finger-print-device-list')
  || auth()->user()->can('finger-print-user-list')
  || auth()->user()->can('attendance-device-clear')
  )
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="attendantmaster">
      Attendance Information<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('finger-print-device-list')
          <li><a class="dropdown-item" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a></li>
          @endcan
          @can('finger-print-user-list')
            <li><a class="dropdown-item" href="{{ route('FingerprintUser')}}">Fingerprint User</a></li>
          @endcan
          @can('attendance-device-clear-list')
            <li><a class="dropdown-item" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a></li>
          @endcan

          @can('attendance-sync')
            <li><a class="dropdown-item" href="{{ route('Attendance')}}">Attendance Sync</a></li>
          @endcan
          @can('attendance-create')
            <li><a class="dropdown-item" href="{{ route('AttendanceEdit')}}">Attendance Add</a></li>
          @endcan
          @can('attendance-edit')
            <li><a class="dropdown-item" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a></li>
          @endcan
          @can('late-attendance-create')
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a></li>
          @endcan
          @can('late-attendance-approve')
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a></li>
          @endcan
          @can('late-attendance-list')
            <li><a class="dropdown-item" href="{{ route('late_attendances_all')}}">Late Attendances</a></li>
          @endcan
          @can('attendance-incomplete-data-list')
            <li><a class="dropdown-item" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a></li>
          @endcan
          @can('ot-approve')
            <li><a class="dropdown-item" href="{{ route('ot_approve')}}">OT Approve</a></li>
          @endcan
          @can('ot-list')
            <li><a class="dropdown-item" href="{{ route('ot_approved')}}">Approved OT</a></li>
          @endcan
          @can('attendance-approve')
            <li><a class="dropdown-item" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a></li>
          @endcan
          @can('MealAllowanceApprove-list')
          <li><a class="dropdown-item" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a></li>
          @endcan
          @can('Holiday-DeductionApprove-list')
          <li><a class="dropdown-item" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a></li>
          @endcan
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('leave-list')
  || auth()->user()->can('leave-type-list')
  || auth()->user()->can('leave-approve')
  || auth()->user()->can('holiday-list')
  || auth()->user()->can('IgnoreDay-list')
  || auth()->user()->can('Coverup-list')
  || auth()->user()->can('Holiday-Deduction-list')
  )
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="leavemaster">
        Leave Information <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('leave-list')
            <li><a class="dropdown-item" href="{{ route('LeaveApply')}}">Leave Apply</a></li>
          @endcan
          @can('leave-type-list')
            <li><a class="dropdown-item" href="{{ route('LeaveType')}}">Leave Type</a></li>
          @endcan
          @can('leave-approve')
            <li><a class="dropdown-item" href="{{ route('LeaveApprovel')}}">Leave Approvals</a></li>
          @endcan
          @can('holiday-list')
            <li><a class="dropdown-item" href="{{ route('Holiday')}}">Holiday</a></li>
          @endcan
          {{-- @can('holiday-list')
            <li><a class="dropdown-item" href="{{ route('HolidayCalendar')}}">Holiday Calendar</a></li>
          @endcan --}}
          @can('IgnoreDay-list')
            <li><a class="dropdown-item" href="{{ route('IgnoreDay')}}">Ignore Days</a></li>
          @endcan
          @can('Coverup-list')
            <li><a class="dropdown-item" href="{{ route('Coverup')}}">CoverUp Details</a></li>
          @endcan
          @can('Holiday-Deduction-list')
            <li><a class="dropdown-item" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a></li>
          @endcan
        </ul>
  </div>
  @endif


  @if(auth()->user()->can('Job-Location-list')
  || auth()->user()->can('Job-Allocation-list')
  || auth()->user()->can('Job-Attendance-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="jobmanegment">
      Job Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('Job-Location-list')
            <li><a class="dropdown-item" href="{{ route('joblocations')}}">Job Location</a></li>
            @endcan
            <!-- @can('Job-Allocation-list')
            <li><a class="dropdown-item" href="{{ route('joballocation')}}">Job Allocation</a></li>
            @endcan -->
            @can('Job-Attendance-list')
            <li><a class="dropdown-item" href="{{ route('jobattendance')}}">Attendance</a></li>
            @endcan
        </ul>
  </div>
  @endif
  
    </div>


