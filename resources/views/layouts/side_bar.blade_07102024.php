<div class="sidebar open">
    {{-- <div class="logo-details">
      <i class="bx bxl-c-plus-plus icon"></i>
      <div class="logo_name">CodingLab</div>
      <i class="bx bx-menu" id="btn"></i>
    </div> --}}
    <ul class="nav-list">
      <li>
        <a href="{{ url('/home') }}" id="dashboard_link">
          <i id="dashboard_link_icon" class="flaticon-381-background-1"></i>
          <span class="links_name">Dashboard</span>
        </a>
        <span class="tooltip">Dashboard</span>
      </li>

      @if(auth()->user()->can('location-list') 
      || auth()->user()->can('company-list') 
      || auth()->user()->can('bank-list') 
      || auth()->user()->can('work-category-list') 
      || auth()->user()->can('month-work-hours-list'))
      <li>
        <a href="{{ url('/corporatedashboard') }}" id="organization_menu_link">
          <i id="organization_menu_link_icon" class="flaticon-381-folder-9"></i>
          <span class="links_name">Organization</span>
        </a>
        <span class="tooltip">Organization</span>
      </li>
      @endif

      @if(auth()->user()->can('job-title-list')
            || auth()->user()->can('pay-grade-list')
            || auth()->user()->can('job-category-list')
            || auth()->user()->can('job-employment-status-list')
            || auth()->user()->can('skill-list')
            || auth()->user()->can('employee-list')
            || auth()->user()->can('employee-select')
            || auth()->user()->can('pe-task-list')
            || auth()->user()->can('allowance-amount-list'))
      <li>
        <a href="{{ url('/employeemanagementdashboard') }}" id="employee_menu_link">
          <i id="employee_menu_link_icon" class="flaticon-381-user-8"></i>
          <span class="links_name">Employee Management</span>
        </a>
        <span class="tooltip">Employee Management</span>
      </li>
      @endif

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
            || auth()->user()->can('leave-list')
            || auth()->user()->can('leave-type-list')
            || auth()->user()->can('leave-approve')
            || auth()->user()->can('holiday-list'))
      <li>
        <a href="{{ url('/attendenceleavedashboard') }}" id="attendant_menu_link">
          <i id="attendant_menu_link_icon" class="flaticon-381-id-card"></i>
          <span class="links_name">Attendance & Leave</span>
        </a>
        <span class="tooltip">Attendance & Leave</span>
      </li>
      @endif

      @if(auth()->user()->can('shift-list')
            || auth()->user()->can('work-shift-list')
            || auth()->user()->can('additional-shift-list'))
      <li>
        <a href="{{ url('/shiftmanagementdashboard') }}" id="shift_menu_link">
          <i id="shift_menu_link_icon" class="flaticon-381-target"></i>
          <span class="links_name">Shift Management</span>
        </a>
        <span class="tooltip">Shift Management</span>
      </li>
      @endif

      @if(auth()->user()->can('employee-report')
            || auth()->user()->can('attendance-report')
            || auth()->user()->can('late-attendance-report')
            || auth()->user()->can('leave-report')
            || auth()->user()->can('employee-bank-report')
            || auth()->user()->can('leave-balance-report')
            || auth()->user()->can('ot-report')
            || auth()->user()->can('no-pay-report'))
      <li>
        <a href="{{ url('/reportdashboard') }}" id="report_menu_link">
          <i id="report_menu_link_icon" class="flaticon-381-file-2"></i>
          <span class="links_name">Reports</span>
        </a>
        <span class="tooltip">Reports</span>
      </li>
      @endif

      @if(auth()->user()->can('Facilities-list') 
        || auth()->user()->can('Payrollprofile-list')
        || auth()->user()->can('Loans-list') 
        || auth()->user()->can('Loans-Settlement-list')
        || auth()->user()->can('Salaryaddition-list') 
        || auth()->user()->can('Other-facilities-list')
        || auth()->user()->can('Salary-increment-list') 
        || auth()->user()->can('Work-summary-list')
        || auth()->user()->can('Salary-preperation-list') 
        || auth()->user()->can('Salary-schedule-list')
        || auth()->user()->can('Paysliplist-list')
        || auth()->user()->can('Pay-Register-Report') 
        || auth()->user()->can('OT-Report')
        || auth()->user()->can('EPF-ETF-Report') 
        || auth()->user()->can('Salary-Sheet-report')
        || auth()->user()->can('Salary-sheet-bankslip-report') 
        || auth()->user()->can('Salary-sheet-heldpayment-report')
        || auth()->user()->can('Sixmonths-report') 
        || auth()->user()->can('Addition-report')
        ||auth()->user()->can('Employee-Salary-Payment-Statement') 
        || auth()->user()->can('Employee-Incentive-Statement')
        || auth()->user()->can('Bank-Advice-Statement') 
        || auth()->user()->can('Pay-Summary-Statement')
        || auth()->user()->can('Employee-Salary-Journal-Statement') 
        || auth()->user()->can('EPF-ETF-Journal-Statement'))
      
      <li>
        <a href="{{ url('/payrolldashboard') }}" id="payrollmenu">
          <i class="flaticon-381-user-8" id="payrollmenu_icon"></i>
          <span class="links_name">Payroll</span>
        </a>
        <span class="tooltip">Payroll</span>
      </li>
      @endif

    @if(auth()->user()->can('user-account-summery-list'))
      <li>
        <a href="{{ url('/useraccountsummery') }}" id="user_information_menu_link">
          <i id="user_information_menu_link_icon" class="flaticon-381-user-1"></i>
          <span class="links_name">User Account Summery</span>
        </a>
        <span class="tooltip">User Account Summery</span>
      </li>
      @endif

      @if(auth()->user()->can('user-list') || auth()->user()->can('role-list'))
      <li>
          <a href="{{ url('/administratordashboard') }}" id="administrator_menu_link">
              <i id="administrator_menu_link_icon" class="flaticon-381-user-4"></i>
            <span class="links_name">Administrator</span>
          </a>
          <span class="tooltip">Administrator</span>
        </li>
      <li>
      @endif

        <div class="sidenav-footer" style="position: fixed;
            bottom: 0;
            width: 100%;">
            <div class="sidenav-footer-content">
                <div style="margin-bottom: 0" class="sidenav-footer-subtitle sidebarfooter-text-color">Logged in as:</div>
                <div class="sidenav-footer-title sidebarfooter-text-color">
                    @isset(Auth::user()->name)
                    {{ Auth::user()->name }}
                    @endisset</div>
            </div>
        </div>
      </li>
    </ul>
  </div>