@php
$user = auth()->user();
$hasOrganizationPermissions = $user->can('Access-Organization');
$hasEmployeeManagementPermissions = $user->can('Access-Employee-Management');
$hasAttendanceLeavePermissions = $user->can('Access-Attendance_Leave');
$hasShiftManagementPermissions = $user->can('Access-Shift_Management');
$hasPayrollPermissions = $user->can('Access-Payroll');
$hasUserAccountSummaryPermission = $user->can('Access-User_account');
$hasAdministratorPermissions = $user->can('Access-Administrator');
$hasKPIPermissions = $user->can('Access-KPI_Managemnt');
$hasReportPermissions = $user->can('Access-Reports');
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
                        @if($user->can('location-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Company') }}">Company</a>
                        @endif
                        @if($user->can('company-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Branch') }}">Branch</a>
                        @endif
                        @if($user->can('bank-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Bank') }}">Bank</a>
                        @endif
                        @if($user->can('job-category-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/JobCategory') }}">Job Category</a>
                        @endif
                        @if($user->can('SalaryAdjustment-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/SalaryAdjustment') }}">Salary Adjustments</a>
                        @endif
                        @if($user->can('Leave-Deduction-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/LeaveDeduction') }}">Leave Deductions</a>
                        @endif
                    </nav>
                </div>
            </li>
            @endif
            @if($hasEmployeeManagementPermissions)
                @php
                    $hasMasterDataAccess = $user->can('job-title-list') ||
                        $user->can('pay-grade-list') ||
                        $user->can('job-category-list') ||
                        $user->can('job-employment-status-list') ||
                        $user->can('skill-list');
                    $hasLetterAccess = $user->can('Appointment-letter-list') ||
                        $user->can('Service-letter-list') ||
                        $user->can('Warning-letter-list') ||
                        $user->can('Resign-letter-list') ||
                        $user->can('Salary-inc-letter-list') ||
                        $user->can('Promotion-letter-list');
                        $user->can('NDA-letter-list');
                        $user->can('end-user-letter-list');
                @endphp
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmployeee" aria-expanded="false" aria-controls="collapsEmployeee">
                    <i class="fa-light fa-users-gear"></i>
                    <span class="links_name">Employee Management <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Employee Management</span>
                <div class="collapse" id="collapsEmployeee" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubSidenavPages">
                        @if($hasMasterDataAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsMasterEmp" aria-expanded="false" aria-controls="collapsMasterEmp" class="py-1">
                            <span class="links_name">Master Data <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsMasterEmp" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('skill-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/Skill') }}">Skill</a>
                                @endif
                                @if($user->can('job-title-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('JobTitle')}}">Job Titles</a>
                                @endif
                                @if($user->can('pay-grade-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('PayGrade')}}">Pay Grades</a>
                                @endif
                                @if($user->can('job-employment-status-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('EmploymentStatus')}}">Job Employment Status</a>
                                @endif
                                @if($user->can('ExamSubject-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('examsubjects')}}">Exam Subjects</a>
                                @endif
                                @if($user->can('DSDivision-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('dsdivision')}}">DS Divisions</a>
                                @endif
                                @if($user->can('GNSDivision-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('gnsdivision')}}">GNS Divisions</a>
                                @endif
                                @if($user->can('PoliceStation-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('policestation')}}">Police Station</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($user->can('employee-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('/addEmployee') }}">Employee Details</a>
                        @endif
                        @if($hasLetterAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpLetters" aria-expanded="false" aria-controls="collapsEmpLetters" class="py-1">
                            <span class="links_name">Employee Letters <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpLetters" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('Appointment-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('appoinementletter')}}" id="">Employee Appointment Letter</a>
                                @endif
                                @if($user->can('NDA-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('NDAletter')}}">Employee NDA Letter</a>
                                @endif
                                @if($user->can('Warning-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('warningletter')}}">Employee Warning Letter</a>
                                @endif
                                @if($user->can('Salary-inc-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('salary_incletter')}}">Employee Salary Increment Letter</a>
                                @endif
                                @if($user->can('Promotion-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('promotionletter')}}">Employee Promotion Letter</a>
                                @endif
                                @if($user->can('Service-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('serviceletter')}}">Employee Service Letter</a>
                                @endif
                                @if($user->can('Resign-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('resignletter')}}">Employee Resignation Letter</a>
                                @endif
                                @if($user->can('end-user-letter-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('end_user_letter')}}">Employee End User Letter</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($user->can('pe-task-list'))
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpPerformance" aria-expanded="false" aria-controls="collapsEmpPerformance" class="py-1">
                            <span class="links_name">Performance Evaluation <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpPerformance" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('allowance-amount-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskList')}}">Task List</a>
                                @endif
                                @if($user->can('employee-allowance-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskEmployeeList')}}">Task Employee List</a>
                                @endif
                                @if($user->can('employee-allowance-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('peTaskEmployeeMarksList')}}">Marks Approve</a>
                                @endif
                            </nav>
                        </div>
                        @endif
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
                        @if($user->can('allowance-amount-list'))
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpAllowance" aria-expanded="false" aria-controls="collapsEmpAllowance" class="py-1">
                            <span class="links_name">Allowance Amounts <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpAllowance" data-parent="#accordionSubSidenavPages">
                            <nav class="sidenav-menu-nested nav">
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('allowanceAmountList')}}">Allowance Amounts</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('emp_allowance')}}">Employee Allowance</a>
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('allowance_approved')}}">Approved Allowance</a>
                            </nav>
                        </div>
                        @endif
                    </nav>
                </div>
            </li>
            @endif
            @if($hasAttendanceLeavePermissions)
            @php
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

                $hasLeaveAccess = $user->can('leave-list') ||
                    $user->can('leave-type-list') ||
                    $user->can('leave-approve') ||
                    $user->can('holiday-list') ||
                    $user->can('IgnoreDay-list') ||
                    $user->can('Coverup-list') ||
                    $user->can('Holiday-Deduction-list') ||
                    $user->can('LeaveRequest-list');
            @endphp
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapseAtteLeave" aria-expanded="false" aria-controls="collapseAtteLeave">
                    <i class="fa-light fa-calendar-pen"></i>
                    <span class="links_name">Attendance & Leave <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Attendance & Leave</span>
                <div class="collapse" id="collapseAtteLeave" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubAtteLeave">
                        @if($hasMasterDataAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsAttendance" aria-expanded="false" aria-controls="collapsAttendance" class="py-1">
                            <span class="links_name">Attendance Information <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsAttendance" data-parent="#accordionSubAtteLeave">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('finger-print-device-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('FingerprintDevice')}}">Fingerprint Device</a>
                                @endif
                                @if($user->can('finger-print-user-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('FingerprintUser')}}">Fingerprint User</a>
                                @endif
                                @if($user->can('attendance-device-clear-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceDeviceClear')}}">Attendance Device Clear</a>
                                @endif

                                @if($user->can('attendance-sync'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Attendance')}}">Attendance Sync</a>
                                @endif
                                @if($user->can('attendance-create'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceEdit')}}">Attendance Add</a>
                                @endif
                                @if($user->can('attendance-edit'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceEditBulk')}}">Attendance Edit</a>
                                @endif
                                @if($user->can('late-attendance-create'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendance_by_time')}}">Late Attendance Mark</a>
                                @endif
                                @if($user->can('late-attendance-approve'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendance_by_time_approve')}}">Late Attendance Approve</a>
                                @endif
                                @if($user->can('late-attendance-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('late_attendances_all')}}">Late Attendances</a>
                                @endif
                                @if($user->can('attendance-incomplete-data-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('incomplete_attendances')}}">Incomplete Attendances</a>
                                @endif
                                @if($user->can('ot-approve'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_approve')}}">OT Approve</a>
                                @endif
                                @if($user->can('ot-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_approved')}}">Approved OT</a>
                                @endif
                                @if($user->can('attendance-approve'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AttendanceApprovel')}}">Attendance Approval</a>
                                @endif
                                @if($user->can('Lateminites-Approvel-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('lateminitesapprovel')}}">Late Deduction Approval</a>
                                @endif
                                @if($user->can('MealAllowanceApprove-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('mealallowanceapproval')}}">Salary Adjustments Approval</a>
                                @endif
                                @if($user->can('Holiday-DeductionApprove-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('holidaydeductionapproval')}}">Leave Deduction Approval</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($hasLeaveAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsLeave" aria-expanded="false" aria-controls="collapsLeave" class="py-1">
                            <span class="links_name">Leave Information <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsLeave" data-parent="#accordionSubAtteLeave">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('LeaveRequest-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('leaverequest')}}">Leave Request</a>
                                @endif
                                @if($user->can('leave-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveApply')}}">Leave Apply</a>
                                @endif
                                @if($user->can('leave-type-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveType')}}">Leave Type</a>
                                @endif
                                @if($user->can('leave-approve'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveApprovel')}}">Leave Approvals</a>
                                @endif
                                @if($user->can('holiday-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Holiday')}}">Holiday</a>
                                @endif
                                @if($user->can('IgnoreDay-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('IgnoreDay')}}">Ignore Days</a>
                                @endif
                                @if($user->can('Coverup-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Coverup')}}">CoverUp Details</a>
                                @endif
                                @if($user->can('Holiday-Deduction-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('HolidayDeduction')}}">Holiday Deduction</a>
                                @endif
                            </nav>
                        </div>
                        @endif
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
                        @if($user->can('shift-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('Shift') }}">Employee Shifts</a>
                        @endif
                        @if($user->can('work-shift-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ShiftType') }}">Work Shifts</a>
                        @endif
                        @if($user->can('additional-shift-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('AdditionalShift.index') }}">Additional Shifts</a>
                        @endif

                        @if($user->can('employee-shift-allocation-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeshift') }}">Employee Night Shift Assign</a>
                        @endif
                        
                        @if($user->can('employee-shift-extend-list'))
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('empshiftextend') }}">Employee Shift Extend Assign</a>
                        @endif
                    </nav>
                </div>
            </li>
            @endif
            @if($hasReportPermissions)
            @php
                $hasAttendanceReports = $user->can('attendance-report') ||
                    $user->can('late-attendance-report') ||
                    $user->can('leave-report') ||
                    $user->can('leave-balance-report') ||
                    $user->can('ot-report') ||
                    $user->can('no-pay-report');

                $hasEmployeeDetails = $user->can('employee-report') || 
                    $user->can('employee-bank-report') ||
                    $user->can('employee-resign-report') ||
                    $user->can('employee-recruitment-report') ||
                    $user->can('employee-time-in-out-report') ||
                    $user->can('employee-actual-ot-report');

                $hasDeptWiseReports = $user->can('department-wise-ot-report') || 
                    $user->can('department-wise-leave-report') || 
                    $user->can('department-wise-attendance-report');
            @endphp
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapeReport" aria-expanded="false" aria-controls="collapeReport">
                    <i class="fa-light fa-file-contract"></i>
                    <span class="links_name">Reports <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Reports</span>
                <div class="collapse" id="collapeReport" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubReport">
                        @if($hasAttendanceReports)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsAttenReport" aria-expanded="false" aria-controls="collapsAttenReport" class="py-1">
                            <span class="links_name">Atte. & Leave Report <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsAttenReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('attendance-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('attendetreportbyemployee')}}">Attendance Report</a>
                                @endif
                                @if($user->can('late-attendance-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LateAttendance')}}">Late Attendance</a>
                                @endif
                                @if($user->can('leave-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('leaveReport')}}">Leave Report</a>
                                @endif
                                @if($user->can('leave-balance-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('LeaveBalance')}}">Leave Balance</a>
                                @endif
                                @if($user->can('ot-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('ot_report')}}">O.T. Report</a>
                                @endif
                                @if($user->can('no-pay-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('no_pay_report')}}">No Pay Report</a>
                                @endif
                                @if($user->can('employee-absent-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" id="absent_report_link" href="{{ route('employee_absent_report') }}">Employee Absent Report</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($hasEmployeeDetails)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsEmpReport" aria-expanded="false" aria-controls="collapsEmpReport" class="py-1">
                            <span class="links_name">Employee Detail Report <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsEmpReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('employee-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('EmpoloyeeReport')}}">Employees Report</a>
                                @endif
                                @if($user->can('employee-bank-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('empBankReport')}}">Employee Banks</a>
                                @endif
                                @if($user->can('employee-resign-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" id="resignation_report_link" href="{{ route('employee_resign_report') }}">Employee Resign Report</a>
                                @endif
                                @if($user->can('employee-recruitment-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employee_recirument_report') }}">Employee Recruitment Report</a>
                                @endif
                                @if($user->can('employee-time-in-out-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeattendancereport') }}">Employee Time In-Out Report</a>
                                @endif
                                @if($user->can('employee-actual-ot-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('employeeotreport') }}">Employee Ot Report</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($hasEmployeeDetails)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsDepartmentReport" aria-expanded="false" aria-controls="collapsDepartmentReport" class="py-1">
                            <span class="links_name">Department Reports <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsDepartmentReport" data-parent="#accordionSubReport">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('department-wise-attendance-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_attendancereport') }}">Department-Wise Attendance Report</a>
                                @endif
                                @if($user->can('department-wise-ot-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_otreport')}}"> Department-Wise O.T. Report</a>
                                @endif
                                @if($user->can('department-wise-leave-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('departmentwise_leavereport')}}">Department-Wise Leave Report</a>
                                @endif
                                @if($user->can('department-wise-leave-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('joballocationreport')}}">Job Allocation Report</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($user->can('attendance-audit-report'))
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
                        @endif
                    </nav>
                </div>
            </li>
            @endif
            @if($hasPayrollPermissions)
                @php
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

                    $hasReportAccess = $user->can('Pay-Register-Report') || 
                        $user->can('OT-Report') || 
                        $user->can('EPF-ETF-Report') || 
                        $user->can('Salary-Sheet-report') || 
                        $user->can('Salary-sheet-bankslip-report') || 
                        $user->can('Salary-sheet-heldpayment-report') || 
                        $user->can('Sixmonths-report') || 
                        $user->can('Addition-report');
                    
                    $hasStatementAccess = $user->can('Employee-Salary-Payment-Statement') || 
                        $user->can('Employee-Incentive-Statement') || 
                        $user->can('Bank-Advice-Statement') || 
                        $user->can('Pay-Summary-Statement') || 
                        $user->can('Employee-Salary-Journal-Statement') || 
                        $user->can('EPF-ETF-Journal-Statement');
                @endphp
            <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapePayroll" aria-expanded="false" aria-controls="collapePayroll">
                    <i class="fa-light fa-money-check-dollar-pen"></i>
                    <span class="links_name">Payroll  <i class="fas fa-angle-down"></i></span>
                </a>
                <span class="tooltip">Payroll</span>
                <div class="collapse" id="collapePayroll" data-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav accordion" id="accordionSubPayroll">
                        @if($hasPolicyAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePolicy" aria-expanded="false" aria-controls="collapsePolicy" class="py-1">
                            <span class="links_name">Policy Management <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePolicy" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('Facilities-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('RemunerationList') }}" id="facilities">Facilities</a>
                                @endif
                                @if($user->can('Payrollprofile-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('PayrollProfileList') }}" id="payrollprofile">Payroll Profile</a>
                                @endif
                                @if($user->can('Loans-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanList') }}" id="loans">Loans</a>
                                @endif
                                @if($user->can('Loan Approve'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanAdmin') }}">Loan Approval</a>
                                @endif
                                @if($user->can('Loans-Settlement-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeLoanInstallmentList') }}" id="loanSettlement">Loan Settlement</a>
                                @endif
                                @if($user->can('Salaryaddition-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeTermPaymentList') }}" id="SalaryAdditions">Salary Additions / Deduction</a>
                                @endif
                                @if($user->can('Other-facilities-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('OtherFacilityPaymentList') }}" id="OtherFacilities">Other Facilities</a>
                                @endif
                                @if($user->can('Salary-increment-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('SalaryIncrementList') }}" id="SalaryIncrements">Salary Increments</a>
                                @endif
                                @if($user->can('Work-summary-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('SalaryProcessSchedule') }}" id="SalaryIncrements">Salary Schedule</a>
                                @endif
                                @if($user->can('Salary-preperation-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeeWorkSummary') }}" id="Worksummary">Work Summary</a>
                                @endif
                                @if($user->can('Salary-schedule-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmployeePayslipList') }}" id="SalaryPreperation">Salary Preperation</a>
                                @endif
                                @if($user->can('Paysliplist-list'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('PayslipRegistry') }}" id="PayslipList">Payslip List</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($hasReportAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePayReport" aria-expanded="false" aria-controls="collapsePayReport" class="py-1">
                            <span class="links_name">Reports <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePayReport" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('Pay-Register-Report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportPayRegister') }}" id="payregister">Pay Register</a>
                                @endif
                                @if($user->can('OT-Report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportEmpOvertime') }}" id="otreport">OT Report</a>
                                @endif
                                @if($user->can('EPF-ETF-Report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportEpfEtf') }}" id="epfetf">EPF and ETF</a>
                                @endif
                                @if($user->can('Salary-Sheet-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSalarySheet') }}" id="salarysheet">Salary Sheet</a>
                                @endif
                                @if($user->can('Salary-sheet-bankslip-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSalarySheetBankSlip') }}" id="salarysheetbank">Salary Sheet - Bank Slip</a>
                                @endif
                                @if($user->can('Salary-sheet-heldpayment-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportHeldSalaries') }}" id="salaryheld">Salary Sheet - Held Payments</a>
                                @endif
                                @if($user->can('Sixmonths-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportSixMonth') }}" id="sixmonth">Six Month Report</a>
                                @endif
                                @if($user->can('Addition-report'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportAddition') }}" id="additionreport">Additions Report</a>
                                @endif
                            </nav>
                        </div>
                        @endif
                        @if($hasStatementAccess)
                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#collapsePayStatement" aria-expanded="false" aria-controls="collapsePayStatement" class="py-1">
                            <span class="links_name">Statements <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePayStatement" data-parent="#accordionSubPayroll">
                            <nav class="sidenav-menu-nested nav">
                                @if($user->can('Employee-Salary-Payment-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpSalaryPayVoucher') }}">Employee Salary (Payment Voucher)</a>
                                @endif
                                @if($user->can('Employee-Incentive-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpIncentivePayVoucher') }}">Employee Incentive (Payment Voucher)</a>
                                @endif
                                @if($user->can('Bank-Advice-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportBankAdvice') }}">Bank Advice</a>
                                @endif
                                @if($user->can('Pay-Summary-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('ReportPaySummary') }}">Pay Summary</a>
                                @endif
                                @if($user->can('Employee-Salary-Journal-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpSalaryJournalVoucher') }}">Employee Salary (Journal Voucher)</a>
                                @endif
                                @if($user->can('EPF-ETF-Journal-Statement'))
                                <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ url('EmpEpfEtfJournalVoucher') }}">EPF and ETF (Journal Voucher)</a>
                                @endif
                            </nav>
                        </div>
                        @endif
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
                        @can('user-list')
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('users.index') }}" id="users_link">Users</a>
                        @endcan
                        @can('role-list')
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('roles.index') }}" id="roles_link">Roles</a>
                        @endcan
                        @can('permission-list')
                        <a class="nav-link p-0 px-3 py-1 small text-dark" href="{{ route('permissions.index') }}" id="roles_link">Permissions</a>
                        @endcan
                    </nav>
                </div>
            </li>
            @endif
        </ul>
    </div>
</div>