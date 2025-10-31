<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="functional">
        Functional <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('kpiyear')}}">KPI Year</a></li>
          <li><a class="dropdown-item" href="{{ route('functionaltype')}}">KRA</a></li>
          <li><a class="dropdown-item" href="{{ route('functionalkpi')}}">KPI</a></li>
          <li><a class="dropdown-item" href="{{ route('functionalparameter')}}">Parameter</a></li>
          <li><a class="dropdown-item" href="{{ route('functionalweightage')}}">Parameter Weightage</a></li>
          <li><a class="dropdown-item" href="{{ route('functionalmeasurement')}}">Measurement</a></li>
          <li><a class="dropdown-item" href="{{ route('functionalmeasurementweightage')}}">Measurement Weightage</a></li>
          <li><a class="dropdown-item" href="{{ route('kpiallocation')}}">KPI Allocation</a></li>
          <li><a class="dropdown-item" href="{{ route('empallocation')}}">Employee Allocation</a></li>
        </ul>
  </div>

  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="behavioural">
        Behavioural <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          <li><a class="dropdown-item" href="{{ route('behaviouraltype')}}" id="">Atributes</a></li>
          <li><a class="dropdown-item" href="{{ route('behaviouralweightage')}}">Weightage</a></li>
        </ul>
  </div>

</div>