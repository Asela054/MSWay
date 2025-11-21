<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="employeemaster">
        Master Data <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('Skill')}}">Skill</a></li>
          <li><a class="dropdown-item" href="{{ route('Hierarchy')}}">Company Hierarchy</a></li>
          <li><a class="dropdown-item" href="{{ route('JobTitle')}}">Job Titles</a></li>
          <li><a class="dropdown-item" href="{{ route('PayGrade')}}">Pay Grades</a></li>
          <li><a class="dropdown-item" href="{{ route('EmploymentStatus')}}">Job Employment Status</a></li>
          <li><a class="dropdown-item" href="{{ route('Financial')}}">Financial Category</a></li>
          <li><a class="dropdown-item" href="{{ route('examsubjects')}}">Exam Subjects</a></li>
          <li><a class="dropdown-item" href="{{ route('dsdivision')}}">DS Divisions</a></li>
          <li><a class="dropdown-item" href="{{ route('gnsdivision')}}">GNS Divisions</a></li>
          <li><a class="dropdown-item" href="{{ route('policestation')}}">Police Station</a></li>
        </ul>
  </div>

  <a role="button" class="btn navbtncolor" href="{{ route('addEmployee') }}" id="employeeinformation">Employee Details <span class="caret"></span></a>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="appointmentletter">
        Employee Letters <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('appoinementletter')}}" id="">Employee Appointment Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('NDAletter')}}">Employee NDA Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('warningletter')}}">Employee Warning Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('salary_incletter')}}">Employee Salary Increment Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('promotionletter')}}">Employee Promotion Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('serviceletter')}}">Employee Service Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('resignletter')}}">Employee Resignation Letter</a></li>
          <li><a class="dropdown-item" href="{{ route('end_user_letter')}}">Employee End User Letter</a></li>
        </ul>
  </div>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="training">
        Training Management <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('Trainingtype')}}" id="">Training Type</a></li>
          <li><a class="dropdown-item" href="{{ route('TrainingAllocation')}}">Training Allocation</a></li>
          <li><a class="dropdown-item" href="{{ route('train_attendance')}}">Training Attendance</a></li>
          <li><a class="dropdown-item" href="{{ route('train_summary')}}">Training Summary</a></li>
        </ul>
  </div>

  {{--<div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="performanceinformation">
      Performance Evaluation <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('peTaskList')}}">Task List</a></li>
          <li><a class="dropdown-item" href="{{ route('peTaskEmployeeList')}}">Task Employee List</a></li>
          <li><a class="dropdown-item" href="{{ route('peTaskEmployeeMarksList')}}">Marks Approve</a></li>
        </ul>
  </div>--}}

  {{--<div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="allowanceinformation">
      Allowance Amounts <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('allowanceAmountList')}}">Allowance Amounts</a></li>
          <li><a class="dropdown-item" href="{{ route('emp_allowance')}}">Employee Allowance</a></li>
          <li><a class="dropdown-item" href="{{ route('allowance_approved')}}">Approved Allowance</a></li>
        </ul>
  </div>--}}
  
</div>