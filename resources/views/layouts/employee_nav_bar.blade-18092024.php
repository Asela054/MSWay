
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
        </ul>
  </div>
  @endif

  @if(auth()->user()->can('employee-list')
      || auth()->user()->can('employee-select'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" data-target="#" href="#" id="employeeinformation">
        Employee Information <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('employee-list')
            <li><a class="dropdown-item" href="{{ route('addEmployee')}}">Employee Details</a></li>
            @endcan
            @can('employee-select')
            <li><a class="dropdown-item" href="{{ route('selectEmployeeIndex')}}">Employee Select</a></li>
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


