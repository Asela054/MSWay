@php
    $user = auth()->user();
    $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

    $hasOrganizationPermissions = in_array('Access-Organization', $userPermissions);
    $hasEmployeeManagementPermissions = in_array('Access-Employee-Management', $userPermissions);
    $hasAttendanceLeavePermissions = in_array('Access-Attendance_Leave', $userPermissions);
    $hasShiftManagementPermissions = in_array('Access-Shift_Management', $userPermissions);
    $hasPayrollPermissions = in_array('Access-Payroll', $userPermissions);
    $hasUserAccountSummaryPermission = in_array('Access-User_account', $userPermissions);
    $hasAdministratorPermissions = in_array('Access-Administrator', $userPermissions);
    $hasKPIPermissions = in_array('Access-KPI_Managemnt', $userPermissions);
    $hasReportPermissions = in_array('Access-Reports', $userPermissions);
    $hasproductionPermissions = in_array('Access-Production_Task', $userPermissions);

@endphp
<div class="sidebar" id="sidebar">
    <ul class="nav-list d-none d-sm-block">
        <li>
            <a href="{{ url('/home') }}" id="dashboard_link">
                <i class="fa-light fa-desktop"></i>
                <span class="links_name">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        @if($hasOrganizationPermissions)
        <li>
            <a href="{{ url('/corporatedashboard') }}" id="organization_menu_link">
                <i class="fa-light fa-building"></i>
                <span class="links_name">Organization</span>
            </a>
            <span class="tooltip">Organization</span>
        </li>
        @endif

        @if($hasEmployeeManagementPermissions)
        <li>
            <a href="{{ url('/employeemanagementdashboard') }}" id="employee_menu_link">
                <i class="fa-light fa-users-gear"></i>
                <span class="links_name">Employee Management</span>
            </a>
            <span class="tooltip">Employee Management</span>
        </li>
        @endif

        @if($hasAttendanceLeavePermissions)
        <li>
            <a href="{{ url('/attendenceleavedashboard') }}" id="attendant_menu_link">
                <i class="fa-light fa-calendar-pen"></i>
                <span class="links_name">Attendance & Leave</span>
            </a>
            <span class="tooltip">Attendance & Leave</span>
        </li>
        @endif

        @if($hasShiftManagementPermissions)
        <li>
            <a href="{{ url('/shiftmanagementdashboard') }}" id="shift_menu_link">
                <i class="fa-light fa-business-time"></i>
                <span class="links_name">Shift Management</span>
            </a>
            <span class="tooltip">Shift Management</span>
        </li>
        @endif

        @if($hasReportPermissions)
        <li>
            <a href="{{ url('/reportdashboard') }}" id="report_menu_link">
                <i class="fa-light fa-file-contract"></i>
                <span class="links_name">Reports</span>
            </a>
            <span class="tooltip">Reports</span>
        </li>
        @endif

        @if($hasPayrollPermissions)
        <li>
            <a href="{{ url('/payrolldashboard') }}" id="payrollmenu">
                <i class="fa-light fa-money-check-dollar-pen"></i>
                <span class="links_name">Payroll</span>
            </a>
            <span class="tooltip">Payroll</span>
        </li>
        @endif

        @if($hasKPIPermissions)
        <li>
          <a href="{{ url('/functionalmanagementdashboard') }}" id="functional_menu_link">
            <i class="fa-light fa-chart-user"></i>
            <span class="links_name">KPI Management</span>
          </a>
          <span class="tooltip">KPI Management</span>
        </li>
        @endif

        @if($hasproductionPermissions)
            <li>
            <a href="{{ url('/productiontaskdashboard') }}" id="production_menu_link">
                <i class="fa-light fa-ballot-check"></i>
                <span class="links_name">Production & Task</span>
            </a>
            <span class="tooltip">Production & Task</span>
            </li>
        @endif


        @if($hasUserAccountSummaryPermission)
        <li>
            <a href="{{ url('/useraccountsummery') }}" id="user_information_menu_link">
                <i class="fa-light fa-id-card"></i>
                <span class="links_name">User Account Summery</span>
            </a>
            <span class="tooltip">User Account Summery</span>
        </li>
        @endif

        @if($hasAdministratorPermissions)
        <li>
            <a href="{{ url('/administratordashboard') }}" id="administrator_menu_link">
                <i class="fa-light fa-gears"></i>
                <span class="links_name">Administrator</span>
            </a>
            <span class="tooltip">Administrator</span>
        </li>
        @endif
    </ul>
    <div class="accordion d-block d-sm-none" id="accordionSidenav">
        <ul class="nav-list">
            <li>
                <a href="{{ url('/home') }}">
                    <i class="fa-light fa-desktop"></i>
                    <span class="links_name">Dashboard</span>
                </a>
            </li>
            @if($hasOrganizationPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsOrganization" aria-expanded="false" aria-controls="collapsOrganization">
                    <i class="fa-light fa-building"></i>
                    <span class="links_name">Organization <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Organization</span>
                <div class="collapse" id="collapsOrganization" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Company') }}">Company</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Branch') }}">Branch</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Bank') }}">Bank</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/JobCategory') }}">Job Category</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/SalaryAdjustment') }}">Salary Adjustments</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/LeaveDeduction') }}">Leave Deductions</a>
                    </nav>
                </div>
            </li>
            @endif
            @if($hasEmployeeManagementPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmployeee" aria-expanded="false" aria-controls="collapsEmployeee">
                    <i class="fa-light fa-users-gear"></i>
                    <span class="links_name">Employee Management <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Employee Management</span>
                <div class="collapse" id="collapsEmployeee" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubSidenavPages">
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsMasterEmp" aria-expanded="false" aria-controls="collapsMasterEmp" class="py-1">
                            <span class="links_name">Master Data <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsMasterEmp" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Skill') }}">Skill</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('JobTitle')}}">Job Titles</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('PayGrade')}}">Pay Grades</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('EmploymentStatus')}}">Job Employment Status</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('examsubjects')}}">Exam Subjects</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('dsdivision')}}">DS Divisions</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('gnsdivision')}}">GNS Divisions</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('policestation')}}">Police Station</a>
                            </nav>
                        </div>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/addEmployee') }}">Employee Details</a>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpLetters" aria-expanded="false" aria-controls="collapsEmpLetters" class="py-1">
                            <span class="links_name">Employee Letters <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpLetters" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('appoinementletter')}}" id="">Employee Appointment Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('NDAletter')}}">Employee NDA Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('warningletter')}}">Employee Warning Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('salary_incletter')}}">Employee Salary Increment Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('promotionletter')}}">Employee Promotion Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('serviceletter')}}">Employee Service Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('resignletter')}}">Employee Resignation Letter</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('end_user_letter')}}">Employee End User Letter</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpPerformance" aria-expanded="false" aria-controls="collapsEmpPerformance" class="py-1">
                            <span class="links_name">Performance Evaluation <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpPerformance" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskList')}}">Task List</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskEmployeeList')}}">Task Employee List</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskEmployeeMarksList')}}">Marks Approve</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpProduction" aria-expanded="false" aria-controls="collapsEmpProduction" class="py-1">
                            <span class="links_name">Daily Production Process <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpProduction" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('machines')}}">Machines</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('products')}}">Products</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('productionallocation')}}">Employee Allocation</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('productionending')}}">Daily Process Ending</a>
                            </nav>
                        </div>
                        {{-- <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpAllowance" aria-expanded="false" aria-controls="collapsEmpAllowance" class="py-1">
                            <span class="links_name">Allowance Amounts <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpAllowance" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('allowanceAmountList')}}">Allowance Amounts</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('emp_allowance')}}">Employee Allowance</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('allowance_approved')}}">Approved Allowance</a>
                            </nav>
                        </div> --}}
                    </nav>
                </div>
            </li>
            @endif
            @if($hasAttendanceLeavePermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAtteLeave" aria-expanded="false" aria-controls="collapseAtteLeave">
                    <i class="fa-light fa-calendar-pen"></i>
                    <span class="links_name">Attendance & Leave <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Attendance & Leave</span>
                <div class="collapse" id="collapseAtteLeave" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubAtteLeave">
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsAttendance" aria-expanded="false" aria-controls="collapsAttendance" class="py-1">
                            <span class="links_name">Attendance Information <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsAttendance" data-parent="#accordionSubAtteLeave">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('FingerprintUser')}}">Fingerprint User</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Attendance')}}">Attendance Sync</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceEdit')}}">Attendance</a>
                                {{-- <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a> --}}
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendances_all')}}">Late Attendances</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_approve')}}">OT Approve</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_approved')}}">Approved OT</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('lateminitesapprovel')}}">Late Deduction Approval</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsLeave" aria-expanded="false" aria-controls="collapsLeave" class="py-1">
                            <span class="links_name">Leave Information <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsLeave" data-parent="#accordionSubAtteLeave">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('leaverequest')}}">Leave Request</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveApply')}}">Leave Apply</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveType')}}">Leave Type</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveApprovel')}}">Leave Approvals</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Holiday')}}">Holiday</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('IgnoreDay')}}">Ignore Days</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Coverup')}}">CoverUp Details</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a>
                            </nav>
                        </div>
                    </nav>
                </div>
            </li>
            @endif
            @if($hasShiftManagementPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapeShiftManage" aria-expanded="false" aria-controls="collapeShiftManage">
                    <i class="fa-light fa-business-time"></i>
                    <span class="links_name">Shift Management <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Shift Management</span>
                <div class="collapse" id="collapeShiftManage" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Shift') }}">Employee Shifts</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ShiftType') }}">Work Shifts</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AdditionalShift.index') }}">Additional Shifts</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeshift') }}">Employee Night Shift Assign</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('empshiftextend') }}">Employee Shift Extend Assign</a>
                    </nav>
                </div>
            </li>

            @endif
            @if($hasReportPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapeReport" aria-expanded="false" aria-controls="collapeReport">
                    <i class="fa-light fa-file-contract"></i>
                    <span class="links_name">Reports <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Reports</span>
                <div class="collapse" id="collapeReport" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubReport">
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsAttenReport" aria-expanded="false" aria-controls="collapsAttenReport" class="py-1">
                            <span class="links_name">Atte. & Leave Report <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsAttenReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LateAttendance')}}">Late Attendance</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('leaveReport')}}">Leave Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveBalance')}}">Leave Balance</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_report')}}">O.T. Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('no_pay_report')}}">No Pay Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpReport" aria-expanded="false" aria-controls="collapsEmpReport" class="py-1">
                            <span class="links_name">Employee Detail Report <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('EmpoloyeeReport')}}">Employees Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('empBankReport')}}">Employee Banks</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employee_recirument_report') }}">Employee Recruitment Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeattendancereport') }}">Employee Time In-Out Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeotreport') }}">Employee Ot Report</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsDepartmentReport" aria-expanded="false" aria-controls="collapsDepartmentReport" class="py-1">
                            <span class="links_name">Department Reports <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsDepartmentReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_attendancereport') }}">Department-Wise Attendance Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_otreport')}}"> Department-Wise O.T. Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_leavereport')}}">Department-Wise Leave Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('joballocationreport')}}">Job Allocation Report</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsDepartmentReport" aria-expanded="false" aria-controls="collapsDepartmentReport" class="py-1">
                            <span class="links_name">Audit Reports <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsDepartmentReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('auditattendancereport') }}">Attendance Time In-Out Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('auditpayregister') }}">Audit Pay Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AuditReportSalarySheet') }}">Audit Salary Sheet</a>
                            </nav>
                        </div>
                    </nav>
                </div>
            </li>

            @endif
            @if($hasPayrollPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapePayroll" aria-expanded="false" aria-controls="collapePayroll">
                    <i class="fa-light fa-money-check-dollar-pen"></i>
                    <span class="links_name">Payroll  <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Payroll</span>
                <div class="collapse" id="collapePayroll" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubPayroll">
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePolicy" aria-expanded="false" aria-controls="collapsePolicy" class="py-1">
                            <span class="links_name">Policy Management <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePolicy" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('RemunerationList') }}" id="facilities">Facilities</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('PayrollProfileList') }}" id="payrollprofile">Payroll Profile</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanList') }}" id="loans">Loans</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanAdmin') }}">Loan Approval</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanInstallmentList') }}" id="loanSettlement">Loan Settlement</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeTermPaymentList') }}" id="SalaryAdditions">Salary Additions / Deduction</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('OtherFacilityPaymentList') }}" id="OtherFacilities">Other Facilities</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('SalaryIncrementList') }}" id="SalaryIncrements">Salary Increments</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('SalaryProcessSchedule') }}" id="SalaryIncrements">Salary Schedule</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeWorkSummary') }}" id="Worksummary">Work Summary</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeePayslipList') }}" id="SalaryPreperation">Salary Preperation</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('PayslipRegistry') }}" id="PayslipList">Payslip List</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePayReport" aria-expanded="false" aria-controls="collapsePayReport" class="py-1">
                            <span class="links_name">Reports <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePayReport" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportPayRegister') }}" id="payregister">Pay Register</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportEmpOvertime') }}" id="otreport">OT Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportEpfEtf') }}" id="epfetf">EPF & ETF Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSalarySheet') }}" id="salarysheet">Salary Sheet</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSalarySheetBankSlip') }}" id="salarysheetbank">Salary Sheet - Bank Slip</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportHeldSalaries') }}" id="salaryheld">Salary Sheet - Held Payments</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSixMonth') }}" id="sixmonth">Six Month Report</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportAddition') }}" id="additionreport">Additions Report</a>
                            </nav>
                        </div>
                        {{-- <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePayStatement" aria-expanded="false" aria-controls="collapsePayStatement" class="py-1">
                            <span class="links_name">Statements <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePayStatement" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpSalaryPayVoucher') }}">Employee Salary (Payment Voucher)</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpIncentivePayVoucher') }}">Employee Incentive (Payment Voucher)</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportBankAdvice') }}">Bank Advice</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportPaySummary') }}">Pay Summary</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpSalaryJournalVoucher') }}">Employee Salary (Journal Voucher)</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpEpfEtfJournalVoucher') }}">EPF and ETF (Journal Voucher)</a>
                            </nav>
                        </div> --}}
                    </nav>
                </div>
            </li>

            @endif
            @if($hasKPIPermissions)
            <li>
                <a href="{{ url('/functionalmanagementdashboard') }}" id="functional_menu_link">
                    <i class="fa-light fa-chart-user"></i>
                    <span class="links_name">KPI Management</span>
                </a>
                <span class="tooltip">KPI Management</span>
            </li>
            @endif

             @if($hasproductionPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapseproduction" aria-expanded="false" aria-controls="collapseproduction">
                    <i class="flaticon-381-background-1"></i>
                    <span class="links_name">Production & Task<i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Production & Task</span>
                <div class="collapse" id="collapseproduction" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubproduction">
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsdailypro" aria-expanded="false" aria-controls="collapsdailypro" class="py-1">
                            <span class="links_name">Daily Production Process<i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsdailypro" data-parent="#accordionSubproduction">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('machines')}}">Machines</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('products')}}">Products</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('productionallocation')}}">Employee Allocation</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('productionending')}}">Daily Process Ending</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeproductionreport')}}">Employee Production</a>
                            </nav>
                        </div>
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapstask" aria-expanded="false" aria-controls="collapstask" class="py-1">
                            <span class="links_name">Daily Task Process<i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapstask" data-parent="#accordionSubproduction">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('tasks')}}">Tasks</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('taskallocation')}}">Employee Task Allocation</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('taskending')}}">Daily Task Ending</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeetaskreport')}}">Employee Task</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeetaskproductreport')}}">Employee Task & Product</a>
                            </nav>
                        </div>
                         <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('productiontaskapprove')}}" >Production & Task Approval</a>
                    </nav>
                </div>
            </li>
            @endif



            @if($hasUserAccountSummaryPermission)
            <li>
                <a href="{{ url('/useraccountsummery') }}" id="user_information_menu_link">
                    <i class="fa-light fa-id-card"></i>
                    <span class="links_name">User Account Summery</span>
                </a>
                <span class="tooltip">User Account Summery</span>
            </li>
            @endif

            @if($hasAdministratorPermissions)
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapeAdministrator" aria-expanded="false" aria-controls="collapeAdministrator">
                    <i class="fa-light fa-gears"></i>
                    <span class="links_name">Administrator <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Administrator</span>
                <div class="collapse" id="collapeAdministrator" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPages">
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('users.index') }}" id="users_link">Users</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('roles.index') }}" id="roles_link">Roles</a>
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('permissions.index') }}" id="roles_link">Permissions</a>
                    </nav>
                </div>
            </li>

            @endif
        </ul>
    </div>
</div>