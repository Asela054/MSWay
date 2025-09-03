<nav aria-label="breadcrumb" style="padding-top:8px;">
  <p class="navbar-brand d-none d-sm-block topbarcolor" style="font-weight: normal">
  
    @if(request()->route()->getName() == 'home')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home')}}" class="breadcrumb-link breadcumpfont">Dashboard</a></li>
    </ol>

    {{-- Organization breadcrump list  --}}




    @elseif(request()->route()->getName() == 'corporatedashboard')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization
          Dashboard</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'Company')

    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('Company')}}" class="breadcrumb-link breadcumpfont">Company</a></li>
    </ol>

    @elseif(request()->route()->getName() == 'Branch')

    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('Branch')}}" class="breadcrumb-link breadcumpfont">Branch</a></li>
    </ol>

    @elseif(request()->route()->getName() == 'Bank')

    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('Bank')}}" class="breadcrumb-link breadcumpfont">Bank</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'bank_branch_show')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('Bank')}}" class="breadcrumb-link breadcumpfont">Bank</a></li>
      <li class="breadcrumb-item"><a href="{{ route('bank_branch_show', ['id' => $id]) }}" class="breadcrumb-link breadcumpfont">Bank Branch - {{$bank->bank}} - {{$bank->code}}</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'DepartmentShow')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('Company')}}" class="breadcrumb-link breadcumpfont">Company</a></li>
      <li class="breadcrumb-item"><a href="{{ route('DepartmentShow', ['id' => $id]) }}" class="breadcrumb-link breadcumpfont">Department : {{$company->name}}</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'JobCategory')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{route('JobCategory')}}" class="breadcrumb-link breadcumpfont">Job Category</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'workCategoryList')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('workCategoryList')}}" class="breadcrumb-link breadcumpfont">Work Category</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'MonthWorkHour')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('corporatedashboard')}}" class="breadcrumb-link breadcumpfont">Organization</a></li>
      <li class="breadcrumb-item"><a href="{{ route('MonthWorkHour')}}" class="breadcrumb-link breadcumpfont">Monthly Work Hours</a></li>
    </ol>


    {{-- employee breadcrump list --}}



    @elseif(request()->route()->getName() == 'employeemanagementdashboard')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management
        Dashboard</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'Skill')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Master Data</a></li>
      <li class="breadcrumb-item"><a href="{{route('Skill')}}" class="breadcrumb-link breadcumpfont">Skill</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'JobTitle')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Master Data</a></li>
      <li class="breadcrumb-item"><a href="{{route('JobTitle')}}" class="breadcrumb-link breadcumpfont">Job Title</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'PayGrade')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Master Data</a></li>
      <li class="breadcrumb-item"><a href="{{route('PayGrade')}}" class="breadcrumb-link breadcumpfont">Pay Grade</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'JobCategory')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Master Data</a></li>
      <li class="breadcrumb-item"><a href="{{route('JobCategory')}}" class="breadcrumb-link breadcumpfont">Job Category</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'EmploymentStatus')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Master Data</a></li>
      <li class="breadcrumb-item"><a href="{{route('EmploymentStatus')}}" class="breadcrumb-link breadcumpfont">Job Employment Status</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'addEmployee')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('addEmployee')}}" class="breadcrumb-link breadcumpfont">Employee Details</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewEmployee')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{ route('viewEmployee', ['id' => $id]) }}" class="breadcrumb-link breadcumpfont">Personal Details</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewEmergencyContacts')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewEmergencyContacts', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Emergency Contact</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewDependents')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewDependents', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Dependent Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewImmigration')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewImmigration', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Immigration Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewSalaryDetails')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewSalaryDetails', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Salary Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewQualifications')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewQualifications', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Qualifications Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewPassport')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewPassport', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Passport Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'passportEdit')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('passportEdit', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Edit Passport Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewbankDetails')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewbankDetails', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Bank Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewEmployeeFiles')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewEmployeeFiles', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">File Detail</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'allowanceAmountList')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Allowance Amounts</a></li>
      <li class="breadcrumb-item"><a href="{{route('allowanceAmountList')}}" class="breadcrumb-link breadcumpfont">Allowance Amounts</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'emp_allowance')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Allowance Amounts</a></li>
      <li class="breadcrumb-item"><a href="{{route('emp_allowance')}}" class="breadcrumb-link breadcumpfont">Employee Allowance</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'allowance_approved')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Allowance Amounts</a></li>
      <li class="breadcrumb-item"><a href="{{route('allowance_approved')}}" class="breadcrumb-link breadcumpfont">Approved Allowance</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'viewEmployeeRequrement')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Employee Information</a></li>
      <li class="breadcrumb-item"><a href="{{route('viewEmployeeRequrement', ['id' => $id])}}" class="breadcrumb-link breadcumpfont">Requrement Details</a></li>
    </ol>
    @elseif(request()->route()->getName() == 'appoinementletter')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="{{route('appoinementletter')}}" class="breadcrumb-link breadcumpfont">Employee Appointment Letter</a></li>
    </ol>


{{-- Attendance and leave breadcrump list --}}




@elseif(request()->route()->getName() == 'attendenceleavedashboard')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
</ol>
@elseif(request()->route()->getName() == 'Attendance')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('Attendance')}}" class="breadcrumb-link breadcumpfont">Attendance Sync </a></li>
</ol>
@elseif(request()->route()->getName() == 'AttendanceEdit')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('AttendanceEdit')}}" class="breadcrumb-link breadcumpfont">Attendance Add </a></li>
</ol>
@elseif(request()->route()->getName() == 'AttendanceEditBulk')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('AttendanceEditBulk')}}" class="breadcrumb-link breadcumpfont">Attendance Edit </a></li>
</ol>
@elseif(request()->route()->getName() == 'AttendanceApprovel')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('AttendanceApprovel')}}" class="breadcrumb-link breadcumpfont">Attendance Approval </a></li>
</ol>
@elseif(request()->route()->getName() == 'Attendentdetails')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('Attendentdetails',['id' => $employee[0]->emp_id, 'date' => $month])}}" class="breadcrumb-link breadcumpfont">Attendance Monthly Summary </a></li>
</ol>
@elseif(request()->route()->getName() == 'late_attendance_by_time')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('late_attendance_by_time')}}" class="breadcrumb-link breadcumpfont">Late Attendance Mark </a></li>
</ol>
@elseif(request()->route()->getName() == 'late_attendance_by_time_approve')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('late_attendance_by_time_approve')}}" class="breadcrumb-link breadcumpfont">Late Attendance Approve</a></li>
</ol>
@elseif(request()->route()->getName() == 'late_attendances_all')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('late_attendances_all')}}" class="breadcrumb-link breadcumpfont">Late Attendances</a></li>
</ol>
@elseif(request()->route()->getName() == 'incomplete_attendances')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('incomplete_attendances')}}" class="breadcrumb-link breadcumpfont">Incomplete Attendances</a></li>
</ol>
@elseif(request()->route()->getName() == 'ot_approve')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('ot_approve')}}" class="breadcrumb-link breadcumpfont">OT Approve</a></li>
</ol>
@elseif(request()->route()->getName() == 'ot_approved')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('ot_approved')}}" class="breadcrumb-link breadcumpfont">Approved OT</a></li>
</ol>
@elseif(request()->route()->getName() == 'FingerprintDevice')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('FingerprintDevice')}}" class="breadcrumb-link breadcumpfont">Fingerprint Device</a></li>
</ol>
@elseif(request()->route()->getName() == 'FingerprintUser')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Attendance</a></li>
  <li class="breadcrumb-item"><a href="{{route('FingerprintUser')}}" class="breadcrumb-link breadcumpfont">Fingerprint User</a></li>
</ol>

@elseif(request()->route()->getName() == 'LeaveType')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Leave</a></li>
  <li class="breadcrumb-item"><a href="{{route('LeaveType')}}" class="breadcrumb-link breadcumpfont">Leave Type</a></li>
</ol>
@elseif(request()->route()->getName() == 'Holiday')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Leave</a></li>
  <li class="breadcrumb-item"><a href="{{route('Holiday')}}" class="breadcrumb-link breadcumpfont">Holiday</a></li>
</ol>
@elseif(request()->route()->getName() == 'HolidayCalendar')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Leave</a></li>
  <li class="breadcrumb-item"><a href="{{route('HolidayCalendar')}}" class="breadcrumb-link breadcumpfont">Holiday Calendar</a></li>
</ol>
@elseif(request()->route()->getName() == 'LeaveApply')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Leave</a></li>
  <li class="breadcrumb-item"><a href="{{route('Holiday')}}" class="breadcrumb-link breadcumpfont">Leave Apply</a></li>
</ol>
@elseif(request()->route()->getName() == 'LeaveApprovel')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link breadcumpfont">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link breadcumpfont">Leave</a></li>
  <li class="breadcrumb-item"><a href="{{route('LeaveApprovel')}}" class="breadcrumb-link breadcumpfont">Leave Approvals</a></li>
</ol>
@elseif(request()->route()->getName() == 'clientattendanceapprovel')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="{{ route('clientattendanceapprovel')}}" class="breadcrumb-link">Client Attendance Approvel</a></li>
</ol>
@elseif(request()->route()->getName() == 'attendancefinalapprove')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('attendenceleavedashboard')}}" class="breadcrumb-link">Attendance & Leave</a></li>
  <li class="breadcrumb-item"><a href="{{ route('attendancefinalapprove')}}" class="breadcrumb-link">Attendance Final Approvel</a></li>
</ol>

{{-- Shift breadcrump list --}}
@elseif(request()->route()->getName() == 'shiftmanagementdashboard')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('shiftmanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Shift Management</a></li>
</ol>
@elseif(request()->route()->getName() == 'Shift')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('shiftmanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Shift Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('Shift')}}" class="breadcrumb-link breadcumpfont">Employee Shifts</a></li>
</ol>
@elseif(request()->route()->getName() == 'ShiftType')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('shiftmanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Shift Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ShiftType')}}" class="breadcrumb-link breadcumpfont">Work Shifts</a></li>
</ol>
@elseif(request()->route()->getName() == 'AdditionalShift.index')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('shiftmanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Shift Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('AdditionalShift.index')}}" class="breadcrumb-link breadcumpfont">Additional Shifts</a></li>
</ol>



{{-- Reports breadcrump list --}}


@elseif(request()->route()->getName() == 'reportdashboard')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmpoloyeeReport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('EmpoloyeeReport')}}" class="breadcrumb-link breadcumpfont">Employees Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'attendetreportbyemployee')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('attendetreportbyemployee')}}" class="breadcrumb-link breadcumpfont">Attendance Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'LateAttendance')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('LateAttendance')}}" class="breadcrumb-link breadcumpfont">Late Attendance</a></li>
</ol>
@elseif(request()->route()->getName() == 'leaveReport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('leaveReport')}}" class="breadcrumb-link breadcumpfont">Leave Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'empBankReport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('empBankReport')}}" class="breadcrumb-link breadcumpfont">Employee Banks</a></li>
</ol>
@elseif(request()->route()->getName() == 'LeaveBalance')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('LeaveBalance')}}" class="breadcrumb-link breadcumpfont">Leave Balance</a></li>
</ol>
@elseif(request()->route()->getName() == 'ot_report')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('ot_report')}}" class="breadcrumb-link breadcumpfont">O.T.Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'no_pay_report')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('no_pay_report')}}" class="breadcrumb-link breadcumpfont">No Pay Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'employee_resign_report')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('employee_resign_report')}}" class="breadcrumb-link breadcumpfont">Employees Resignation Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'employee_absent_report')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('employee_absent_report')}}" class="breadcrumb-link breadcumpfont">Employees Absent Report</a></li>
</ol>


@elseif(request()->route()->getName() == 'employee_recirument_report')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('employee_recirument_report')}}" class="breadcrumb-link breadcumpfont">Employees Recruitment Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'departmentwise_otreport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('departmentwise_otreport')}}" class="breadcrumb-link breadcumpfont">Department-Wise O.T Report</a></li>
</ol>

@elseif(request()->route()->getName() == 'departmentwise_leavereport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('departmentwise_leavereport')}}" class="breadcrumb-link breadcumpfont">Department-Wise Leave Report</a></li>
</ol>

@elseif(request()->route()->getName() == 'departmentwise_attendancereport')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('reportdashboard')}}" class="breadcrumb-link breadcumpfont">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{route('departmentwise_attendancereport')}}" class="breadcrumb-link breadcumpfont">Department-Wise Attendance Report</a></li>
</ol>

{{-- Payroll breadcrump list --}}



@elseif(request()->route()->getName() == 'payrolldashboard')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
</ol>
@elseif(request()->route()->getName() == 'employeepayment')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="{{ route('employeepayment')}}" class="breadcrumb-link"> Security Payment</a></li>
</ol>
@elseif(request()->route()->getName() == 'RemunerationList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('RemunerationList')}}" class="breadcrumb-link">Facilities</a></li>
</ol>
@elseif(request()->route()->getName() == 'PayrollProfileList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('PayrollProfileList')}}" class="breadcrumb-link">Payroll Profile</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeeLoanList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeLoanList')}}" class="breadcrumb-link">Loans</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeeLoanAdmin')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeLoanAdmin')}}" class="breadcrumb-link">Loans Approval</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeeLoanInstallmentList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeLoanInstallmentList')}}" class="breadcrumb-link">Loan Settlement</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeeTermPaymentList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeTermPaymentList')}}" class="breadcrumb-link">Salary Additions</a></li>
</ol>
@elseif(request()->route()->getName() == 'OtherFacilityPaymentList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('OtherFacilityPaymentList')}}" class="breadcrumb-link">Other Facilities</a></li>
</ol>
@elseif(request()->route()->getName() == 'SalaryIncrementList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('SalaryIncrementList')}}" class="breadcrumb-link">Salary Increments</a></li>
</ol>
@elseif(request()->route()->getName() == 'SalaryProcessSchedule')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('SalaryProcessSchedule')}}" class="breadcrumb-link">Salary Schedule</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeeWorkSummary')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeWorkSummary')}}" class="breadcrumb-link">Work Summary</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmployeePayslipList')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeePayslipList')}}" class="breadcrumb-link">Salary Preperation</a></li>
</ol>
@elseif(request()->route()->getName() == 'PayslipRegistry')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('PayslipRegistry')}}" class="breadcrumb-link">Payslip List</a></li>
</ol>

@elseif(request()->route()->getName() == 'shiftsalarypreparation')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('shiftsalarypreparation')}}" class="breadcrumb-link">Shift Salary Preperation</a></li>
</ol>

   {{-- Payroll Reports lists  --}}


@elseif(request()->route()->getName() == 'ReportPayRegister')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportPayRegister')}}" class="breadcrumb-link">Pay Register</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportEmpOvertime')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportEmpOvertime')}}" class="breadcrumb-link">OT Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportEpfEtf')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportEpfEtf')}}" class="breadcrumb-link">EPF and ETF</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportSalarySheet')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportSalarySheet')}}" class="breadcrumb-link">Salary Sheet</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportSalarySheetBankSlip')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportSalarySheetBankSlip')}}" class="breadcrumb-link">Salary Sheet - Bank Slip</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportHeldSalaries')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportHeldSalaries')}}" class="breadcrumb-link">Salary Sheet - Held Payments</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportSixMonth')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportSixMonth')}}" class="breadcrumb-link">Six Month Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportAddition')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportAddition')}}" class="breadcrumb-link">Additions Report</a></li>
</ol>
@elseif(request()->route()->getName() == 'LocationPaySummary')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('LocationPaySummary')}}" class="breadcrumb-link">Pay Summary</a></li>
</ol>
@elseif(request()->route()->getName() == 'LocationMasterSummary')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('LocationMasterSummary')}}" class="breadcrumb-link">Master Summary</a></li>
</ol>
{{-- @elseif(request()->route()->getName() == 'EmployeeGratuity')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmployeeGratuity')}}" class="breadcrumb-link">Employee Gratuity</a></li>
</ol> --}}
@elseif(request()->route()->getName() == 'GratuityProvision')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('GratuityProvision')}}" class="breadcrumb-link">Gratuity Provision</a></li>
</ol>
@elseif(request()->route()->getName() == 'SalaryReconciliation')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Reports</a></li>
  <li class="breadcrumb-item"><a href="{{ route('SalaryReconciliation')}}" class="breadcrumb-link">Salary Reconciliation</a></li>
</ol>
@elseif(request()->route()->getName() == 'rankpayrollprofile')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('rankpayrollprofile')}}" class="breadcrumb-link">Rank Payroll Profile</a></li>
</ol>
@elseif(request()->route()->getName() == 'shiftsalarypreparation')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Policy Management</a></li>
  <li class="breadcrumb-item"><a href="{{ route('shiftsalarypreparation')}}" class="breadcrumb-link">Shift Salary Preperation</a></li>
</ol>
  
{{-- payroll statement  --}}

@elseif(request()->route()->getName() == 'EmpSalaryPayVoucher')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmpSalaryPayVoucher')}}" class="breadcrumb-link">Employee Salary (Payment Voucher)</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmpIncentivePayVoucher')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmpIncentivePayVoucher')}}" class="breadcrumb-link">Employee Incentive (Payment Voucher)</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportBankAdvice')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportBankAdvice')}}" class="breadcrumb-link">Bank Advice</a></li>
</ol>
@elseif(request()->route()->getName() == 'ReportPaySummary')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('ReportPaySummary')}}" class="breadcrumb-link">Pay Summary</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmpSalaryJournalVoucher')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmpSalaryJournalVoucher')}}" class="breadcrumb-link">Employee Salary (Journal Voucher)</a></li>
</ol>
@elseif(request()->route()->getName() == 'EmpEpfEtfJournalVoucher')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('payrolldashboard')}}" class="breadcrumb-link"> Pay Roll</a></li>
  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link"> Statements</a></li>
  <li class="breadcrumb-item"><a href="{{ route('EmpEpfEtfJournalVoucher')}}" class="breadcrumb-link">EPF and ETF (Journal Voucher)</a></li>
</ol>

{{-- Administration breadcrump list --}}




@elseif(request()->route()->getName() == 'administratordashboard')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
</ol>
@elseif(request()->route()->getName() == 'users.index')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.index')}}" class="breadcrumb-link breadcumpfont"> Users</a></li>
</ol>
@elseif(request()->route()->getName() == 'users.create')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.index')}}" class="breadcrumb-link breadcumpfont"> Users</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.create')}}" class="breadcrumb-link breadcumpfont"> User Create</a></li>
</ol>
@elseif(request()->route()->getName() == 'users.show')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.index')}}" class="breadcrumb-link breadcumpfont"> Users</a></li>
  <li class="breadcrumb-item"><a href="" class="breadcrumb-link breadcumpfont"> User View</a></li>
</ol>
@elseif(request()->route()->getName() == 'users.edit')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.index')}}" class="breadcrumb-link breadcumpfont"> Users</a></li>
  <li class="breadcrumb-item"><a href="" class="breadcrumb-link breadcumpfont"> User Edit</a></li>
</ol>

@elseif(request()->route()->getName() == 'roles.index')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('roles.index')}}" class="breadcrumb-link breadcumpfont"> Roles</a></li>
</ol>
@elseif(request()->route()->getName() == 'roles.create')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('roles.index')}}" class="breadcrumb-link breadcumpfont"> Roles</a></li>
  <li class="breadcrumb-item"><a href="{{ route('roles.create')}}" class="breadcrumb-link breadcumpfont"> Role Create</a></li>
</ol>
@elseif(request()->route()->getName() == 'roles.show')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('roles.index')}}" class="breadcrumb-link breadcumpfont"> Roles</a></li>
  <li class="breadcrumb-item"><a href="" class="breadcrumb-link breadcumpfont"> Role View</a></li>
</ol>
@elseif(request()->route()->getName() == 'roles.edit')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('administratordashboard')}}" class="breadcrumb-link breadcumpfont"> Administrator</a></li>
  <li class="breadcrumb-item"><a href="{{ route('roles.index')}}" class="breadcrumb-link breadcumpfont"> Roles</a></li>
  <li class="breadcrumb-item"><a href="" class="breadcrumb-link breadcumpfont"> Role Edit</a></li>
</ol>
@elseif(request()->route()->getName() == 'trainingmaster')
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('employeemanagementdashboard')}}" class="breadcrumb-link breadcumpfont">Employee Management</a></li>
      <li class="breadcrumb-item"><a href="{{route('trainingmaster')}}" class="breadcrumb-link breadcumpfont">Employee Traning</a></li>
    </ol>
@elseif(request()->route()->getName() == 'useraccountsummery')
<ol class="breadcrumb custom-breadcrumb">
  <li class="breadcrumb-item"><a href="{{ route('useraccountsummery')}}" class="breadcrumb-link breadcumpfont"> User Account Summery</a></li>
</ol>
    @endif
  </p>
</nav>