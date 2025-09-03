<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasMasterDataAccess = $user->can('job-title-list') ||
                         $user->can('pay-grade-list') ||
                         $user->can('job-category-list') ||
                         $user->can('job-employment-status-list') ||
                         $user->can('skill-list');
  @endphp

  @if($hasMasterDataAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="employeemaster">
        Master Data <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('skill-list'))
            <li><a class="dropdown-item" href="{{ route('Skill')}}">Skill</a></li>
          @endif
          @if($user->can('job-title-list'))
            <li><a class="dropdown-item" href="{{ route('JobTitle')}}">Job Titles</a></li>
          @endif
          @if($user->can('pay-grade-list'))
            <li><a class="dropdown-item" href="{{ route('PayGrade')}}">Pay Grades</a></li>
          @endif
          @if($user->can('job-employment-status-list'))
            <li><a class="dropdown-item" href="{{ route('EmploymentStatus')}}">Job Employment Status</a></li>
          @endif
          @if($user->can('ExamSubject-list'))
            <li><a class="dropdown-item" href="{{ route('examsubjects')}}">Exam Subjects</a></li>
          @endif
          @if($user->can('DSDivision-list'))
            <li><a class="dropdown-item" href="{{ route('dsdivision')}}">DS Divisions</a></li>
          @endif
          @if($user->can('GNSDivision-list'))
            <li><a class="dropdown-item" href="{{ route('gnsdivision')}}">GNS Divisions</a></li>
          @endif
          @if($user->can('PoliceStation-list'))
            <li><a class="dropdown-item" href="{{ route('policestation')}}">Police Station</a></li>
          @endif
        </ul>
  </div>
  @endif

  @if($user->can('employee-list'))
  <a role="button" class="btn navbtncolor" href="{{ route('addEmployee') }}" id="employeeinformation">Employee Details <span class="caret"></span></a>
  @endif

  @php
    $hasLetterAccess = $user->can('Appointment-letter-list') ||
                     $user->can('Service-letter-list') ||
                     $user->can('Warning-letter-list') ||
                     $user->can('Resign-letter-list') ||
                     $user->can('Salary-inc-letter-list') ||
                     $user->can('Promotion-letter-list');
                     $user->can('NDA-letter-list');
                     $user->can('end-user-letter-list');
  @endphp

  @if($hasLetterAccess)
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="appointmentletter">
        Employee Letters <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('Appointment-letter-list'))
          <li><a class="dropdown-item" href="{{ route('appoinementletter')}}" id="">Employee Appointment Letter</a></li>
          @endif
          @if($user->can('NDA-letter-list'))
            <li><a class="dropdown-item" href="{{ route('NDAletter')}}">Employee NDA Letter</a></li>
          @endif
          @if($user->can('Warning-letter-list'))
            <li><a class="dropdown-item" href="{{ route('warningletter')}}">Employee Warning Letter</a></li>
          @endif
          @if($user->can('Salary-inc-letter-list'))
            <li><a class="dropdown-item" href="{{ route('salary_incletter')}}">Employee Salary Increment Letter</a></li>
          @endif
          @if($user->can('Promotion-letter-list'))
            <li><a class="dropdown-item" href="{{ route('promotionletter')}}">Employee Promotion Letter</a></li>
          @endif
          @if($user->can('Service-letter-list'))
            <li><a class="dropdown-item" href="{{ route('serviceletter')}}">Employee Service Letter</a></li>
          @endif
          @if($user->can('Resign-letter-list'))
            <li><a class="dropdown-item" href="{{ route('resignletter')}}">Employee Resignation Letter</a></li>
          @endif
          @if($user->can('end-user-letter-list'))
            <li><a class="dropdown-item" href="{{ route('end_user_letter')}}">Employee End User Letter</a></li>
          @endif
        </ul>
  </div>
  @endif

  @if($user->can('pe-task-list'))
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="performanceinformation">
      Performance Evaluation <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @if($user->can('allowance-amount-list'))
            <li><a class="dropdown-item" href="{{ route('peTaskList')}}">Task List</a></li>
          @endif
          @if($user->can('employee-allowance-list'))
            <li><a class="dropdown-item" href="{{ route('peTaskEmployeeList')}}">Task Employee List</a></li>
          @endif
          @if($user->can('employee-allowance-list'))
            <li><a class="dropdown-item" href="{{ route('peTaskEmployeeMarksList')}}">Marks Approve</a></li>
          @endif
        </ul>
  </div>
  @endif

  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="dailyprocess">
      Daily Production Process <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          
            <li><a class="dropdown-item" href="{{ route('machines')}}">Machines</a></li>
            
            <li><a class="dropdown-item" href="{{ route('products')}}">Products</a></li>
            
            <li><a class="dropdown-item" href="{{ route('productionallocation')}}">Employee Allocation</a></li>

            <li><a class="dropdown-item" href="{{ route('productionending')}}">Daily Process Ending</a></li>
           
        </ul>
  </div>

  @if($user->can('allowance-amount-list'))
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="javascript:void(0);" id="allowanceinformation">
      Allowance Amounts <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('allowanceAmountList')}}">Allowance Amounts</a></li>
          <li><a class="dropdown-item" href="{{ route('emp_allowance')}}">Employee Allowance</a></li>
          <li><a class="dropdown-item" href="{{ route('allowance_approved')}}">Approved Allowance</a></li>
        </ul>
  </div>
  @endif
</div>