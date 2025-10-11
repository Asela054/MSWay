<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasAttendanceAccess = $user->can('attendance-sync') ||
                         $user->can('attendance-incomplete-data-list') ||
                         $user->can('attendance-list') ||
                         $user->can('attendance-create') ||
                         $user->can('attendance-edit') ||
                         $user->can('attendance-delete') ||
                         $user->can('attendance-approve') ||
                         $user->can('late-attendance-create') ||
                         $user->can('late-attendance-approve') ||
                         $user->can('late-attendance-list') ||
                         $user->can('attendance-incomplete-data-list') ||
                         $user->can('ot-approve') ||
                         $user->can('ot-list') ||
                         $user->can('finger-print-device-list') ||
                         $user->can('finger-print-user-list') ||
                         $user->can('attendance-device-clear');
  @endphp

  @if($hasAttendanceAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="attendantmaster">
      Attendance Information<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('finger-print-device-list'))
          <li><a class="dropdown-item" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a></li>
          @endif
          @if($user->can('finger-print-user-list'))
          <li><a class="dropdown-item" href="{{ route('FingerprintUser')}}">Fingerprint User</a></li>
          @endif
          @if($user->can('attendance-device-clear-list'))
          <li><a class="dropdown-item" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a></li>
          @endif

          @if($user->can('attendance-sync'))
            <li><a class="dropdown-item" href="{{ route('Attendance')}}">Attendance Sync</a></li>
          @endif
          @if($user->can('attendance-create'))
            <li><a class="dropdown-item" href="{{ route('AttendanceEdit')}}">Attendance</a></li>
          @endif
          {{-- @if($user->can('attendance-edit'))
            <li><a class="dropdown-item" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a></li>
          @endif --}}
          @if($user->can('late-attendance-create'))
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a></li>
          @endif
          @if($user->can('late-attendance-approve'))
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a></li>
          @endif
          @if($user->can('late-attendance-list'))
            <li><a class="dropdown-item" href="{{ route('late_attendances_all')}}">Late Attendances</a></li>
          @endif
          @if($user->can('attendance-incomplete-data-list'))
            <li><a class="dropdown-item" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a></li>
          @endif
          @if($user->can('ot-approve'))
            <li><a class="dropdown-item" href="{{ route('ot_approve')}}">OT Approve</a></li>
          @endif
          @if($user->can('ot-list'))
            <li><a class="dropdown-item" href="{{ route('ot_approved')}}">Approved OT</a></li>
          @endif
          @if($user->can('attendance-approve'))
            <li><a class="dropdown-item" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a></li>
          @endif
          @if($user->can('Lateminites-Approvel-list'))
          <li><a class="dropdown-item" href="{{ route('lateminitesapprovel')}}">Late Deduction Approval</a></li>
          @endif
          @if($user->can('MealAllowanceApprove-list'))
          <li><a class="dropdown-item" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a></li>
          @endif
          @if($user->can('Holiday-DeductionApprove-list'))
          <li><a class="dropdown-item" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a></li>
          @endif
           {{-- <li><a class="dropdown-item" href="{{ route('absentnopay')}}">Absent Noapy Apply</a></li> --}}
        </ul>
  </div>
  @endif

  @php
    $hasLeaveAccess = $user->can('leave-list') ||
                     $user->can('leave-type-list') ||
                     $user->can('leave-approve') ||
                     $user->can('holiday-list') ||
                     $user->can('IgnoreDay-list') ||
                     $user->can('Coverup-list') ||
                     $user->can('Holiday-Deduction-list');
  @endphp

  @if($hasLeaveAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="leavemaster">
        Leave Information <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('LeaveRequest-list'))
            <li><a class="dropdown-item" href="{{ route('leaverequest')}}">Leave Request</a></li>
          @endif
          @if($user->can('leave-list'))
            <li><a class="dropdown-item" href="{{ route('LeaveApply')}}">Leave Apply</a></li>
          @endif
          @if($user->can('leave-type-list'))
            <li><a class="dropdown-item" href="{{ route('LeaveType')}}">Leave Type</a></li>
          @endif
          @if($user->can('leave-approve'))
            <li><a class="dropdown-item" href="{{ route('LeaveApprovel')}}">Leave Approvals</a></li>
          @endif
          @if($user->can('holiday-list'))
            <li><a class="dropdown-item" href="{{ route('Holiday')}}">Holiday</a></li>
          @endif
          @if($user->can('IgnoreDay-list'))
            <li><a class="dropdown-item" href="{{ route('IgnoreDay')}}">Ignore Days</a></li>
          @endif
          @if($user->can('Coverup-list'))
            <li><a class="dropdown-item" href="{{ route('Coverup')}}">CoverUp Details</a></li>
          @endif
          @if($user->can('Holiday-Deduction-list'))
            <li><a class="dropdown-item" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a></li>
          @endif
        </ul>
  </div>
  @endif

  @php
    $hasJobAccess = auth()->user()->can('Job-Location-list') ||
                  auth()->user()->can('Job-Allocation-list') ||
                  auth()->user()->can('Job-Attendance-list') ||
                  auth()->user()->can('Job-Meal-list') ||
                  auth()->user()->can('Job-Meal-Approval') ||
                  auth()->user()->can('Job-Attendance-Approve-list')||
                  auth()->user()->can('Location-Allowance-Approve-list');
  @endphp

  @if($hasJobAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="jobmanegment">
      Location Wise Attendance <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
            @if(auth()->user()->can('Job-Allocation-list'))
            <li><a class="dropdown-item" href="{{ route('joballocation')}}">Allocation</a></li>
            @endif
            @if(auth()->user()->can('Job-Attendance-list'))
            <li><a class="dropdown-item" href="{{ route('jobattendance')}}">Location Attendance</a></li>
            @endif
            @if(auth()->user()->can('Job-Attendance-Approve-list'))
            <li><a class="dropdown-item" href="{{ route('jobattendanceapprove')}}">Location Attendance Approve</a></li>
            @endif
            @if(auth()->user()->can('Job-Attendance-Approve-list'))
           <li><a class="dropdown-item" href="{{ route('unauthorizejobattendanceapprove')}}">Unauthorized Location Attendance Approve</a></li>
            @endif
            <li><a class="dropdown-item" href="{{ route('locationallwanceapprove')}}">Location Allowance Approval</a></li>
            @if(auth()->user()->can('Job-Meal-list'))
            <li><a class="dropdown-item" href="{{ route('jobmealallowance')}}">Meal Allowance</a></li>
            @endif
            @if(auth()->user()->can('Job-Meal-Approval'))
            <li><a class="dropdown-item" href="{{ route('jobmealallowanceapp')}}">Meal Allowance Approval</a></li>
            @endif
        </ul>
  </div> 
  @endif
</div>