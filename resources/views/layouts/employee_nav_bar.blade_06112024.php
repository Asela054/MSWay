
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('job-title-list')
  || auth()->user()->can('pay-grade-list')
  || auth()->user()->can('job-category-list')
  || auth()->user()->can('job-employment-status-list')
  || auth()->user()->can('skill-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="employeemaster">
        Master Data <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('skill-list')
            <li><a class="dropdown-item" href="{{ route('Skill')}}">Skill</a></li>
            @endcan
            @can('job-title-list')
            <li><a class="dropdown-item" href="{{ route('JobTitle')}}">Job Titles</a></li>
            @endcan
            @can('pay-grade-list')
            <li><a class="dropdown-item" href="{{ route('PayGrade')}}">Pay Grades</a></li>
            @endcan
            @can('job-employment-status-list')
            <li><a class="dropdown-item" href="{{ route('EmploymentStatus')}}">Job Employment Status</a></li>
            @endcan
            @can('ExamSubject-list')
            <li><a class="dropdown-item" href="{{ route('examsubjects')}}">Exam Subjects</a></li>
            @endcan
            @can('DSDivision-list')
            <li><a class="dropdown-item" href="{{ route('dsdivision')}}">DS Divisions</a></li>
            @endcan
            @can('GNSDivision-list')
            <li><a class="dropdown-item" href="{{ route('gnsdivision')}}">GNS Divisions</a></li>
            @endcan
            @can('PoliceStation-list')
            <li><a class="dropdown-item" href="{{ route('policestation')}}">Police Station</a></li>
            @endcan
        </ul>
  </div>
  @endif

  @can('employee-list')
  <a role="button" class="btn navbtncolor" href="{{ route('addEmployee') }}" id="employeeinformation">Employee Details <span class="caret"></span></a>
  @endcan


@if(auth()->user()->can('Appointment-letter-list')
  || auth()->user()->can('Appointment-letter-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="appointmentletter">
        Employee Letters <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('Appointment-letter-list')
          <li><a class="dropdown-item" href="{{ route('appoinementletter')}}" id="">Employee Appointment Letter</a></li>
          @endcan
          @can('Appointment-letter-list')
            <li><a class="dropdown-item" href="{{ route('serviceletter')}}">Employee Service Letter</a></li>
            @endcan
          @can('Appointment-letter-list')
            <li><a class="dropdown-item" href="{{ route('warningletter')}}">Employee Warning Letter</a></li>
            @endcan
          @can('Appointment-letter-list')
            <li><a class="dropdown-item" href="{{ route('resignletter')}}">Employee Resignation Letter</a></li>
            @endcan
          @can('Appointment-letter-list')
            <li><a class="dropdown-item" href="{{ route('salary_incletter')}}">Employee Salary Increment Letter</a></li>
            @endcan
          @can('Appointment-letter-list')
            <li><a class="dropdown-item" href="{{ route('promotionletter')}}">Employee Promotion Letter</a></li>
            @endcan
              
            
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('pe-task-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="performanceinformation">
      Performance Evaluation <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('allowance-amount-list')
            <li><a class="dropdown-item" href="{{ route('peTaskList')}}">Task List</a></li>
            @endcan
            @can('employee-allowance-list')
            <li><a class="dropdown-item" href="{{ route('peTaskEmployeeList')}}">Task Employee List</a></li>
            @endcan
            @can('employee-allowance-list')
            <li><a class="dropdown-item" href="{{ route('peTaskEmployeeMarksList')}}">Marks Approve</a></li>
            @endcan
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('allowance-amount-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="allowanceinformation">
      Allowance Amounts <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('allowance-amount-list')
            <li><a class="dropdown-item" href="{{ route('allowanceAmountList')}}">Allowance Amounts</a></li>
            @endcan
            @can('employee-allowance-list')
            <li><a class="dropdown-item" href="{{ route('emp_allowance')}}">Employee Allowance</a></li>
            @endcan
            @can('employee-allowance-list')
            <li><a class="dropdown-item" href="{{ route('allowance_approved')}}">Approved Allowance</a></li>
            @endcan
        </ul>
  </div>
  @endif

    </div>


