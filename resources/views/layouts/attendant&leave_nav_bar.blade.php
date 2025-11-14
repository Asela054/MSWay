<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
 


  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="attendantmaster">
      Attendance Information<span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
         
          <li><a class="dropdown-item" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a></li>
          
          <li><a class="dropdown-item" href="{{ route('FingerprintUser')}}">Fingerprint User</a></li>
        
          {{-- <li><a class="dropdown-item" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a></li> --}}
         
            <li><a class="dropdown-item" href="{{ route('Attendance')}}">Attendance Sync</a></li>
         
            <li><a class="dropdown-item" href="{{ route('AttendanceEdit')}}">Attendance Add & Edit</a></li>
        
          {{-- @if($user->can('attendance-edit'))
            <li><a class="dropdown-item" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a></li>
          @endif --}}
          
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a></li>
         
            <li><a class="dropdown-item" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a></li>
         
            <li><a class="dropdown-item" href="{{ route('late_attendances_all')}}">Late Attendances</a></li>
         
            <li><a class="dropdown-item" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a></li>
         
            <li><a class="dropdown-item" href="{{ route('absentnopay')}}">Absent Noapy Apply</a></li> 

            <li><a class="dropdown-item" href="{{ route('ot_approve')}}">OT Approve</a></li>
        
            <li><a class="dropdown-item" href="{{ route('ot_approved')}}">Approved OT</a></li>
       
            <li><a class="dropdown-item" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a></li>
          
          <li><a class="dropdown-item" href="{{ route('lateminitesapprovel')}}">Late Deduction Approval</a></li>
          
          <li><a class="dropdown-item" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a></li>
          
          <li><a class="dropdown-item" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a></li>
          
          
        </ul>
  </div>
 

 
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="leavemaster">
        Leave Information <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
        
            <li><a class="dropdown-item" href="{{ route('leaverequest')}}">Leave Request</a></li>
          
            <li><a class="dropdown-item" href="{{ route('LeaveApply')}}">Leave Apply</a></li>
        
            <li><a class="dropdown-item" href="{{ route('LeaveType')}}">Leave Type</a></li>
         
            <li><a class="dropdown-item" href="{{ route('LeaveApprovel')}}">Leave Approvals</a></li>
         
            <li><a class="dropdown-item" href="{{ route('Holiday')}}">Holiday</a></li>
         
            <li><a class="dropdown-item" href="{{ route('IgnoreDay')}}">Ignore Days</a></li>
         
            <li><a class="dropdown-item" href="{{ route('Coverup')}}">CoverUp Details</a></li>
          
            <li><a class="dropdown-item" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a></li>
         
        </ul>
  </div>


 
  
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="jobmanegment">
      Location Wise Attendance <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
           
            <li><a class="dropdown-item" href="{{ route('joballocation')}}">Allocation</a></li>
          
            <li><a class="dropdown-item" href="{{ route('jobattendance')}}">Location Attendance</a></li>
           
            <li><a class="dropdown-item" href="{{ route('jobattendanceapprove')}}">Location Attendance Approve</a></li>
           
           <li><a class="dropdown-item" href="{{ route('unauthorizejobattendanceapprove')}}">Unauthorized Location Attendance Approve</a></li>
           
            <li><a class="dropdown-item" href="{{ route('locationallwanceapprove')}}">Location Allowance Approval</a></li>
           
            {{-- <li><a class="dropdown-item" href="{{ route('jobmealallowance')}}">Meal Allowance</a></li>
           
            <li><a class="dropdown-item" href="{{ route('jobmealallowanceapp')}}">Meal Allowance Approval</a></li> --}}
          
        </ul>
  </div> 

</div>