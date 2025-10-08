
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

 @if(auth()->user()->can('Functional-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="functional">
        Functional <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('kpiyear')}}">KPI Year</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionaltype')}}">KRA</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionalkpi')}}">KPI</a></li>
          @endcan   
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionalparameter')}}">Parameter</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionalweightage')}}">Parameter Weightage</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionalmeasurement')}}">Measurement</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('functionalmeasurementweightage')}}">Measurement Weightage</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('kpiallocation')}}">KPI Allocation</a></li>
          @endcan
          @can('Functional-list')
          <li><a class="dropdown-item" href="{{ route('empallocation')}}">Employee Allocation</a></li>
          @endcan
          
        </ul>
  </div>
  @endif

 @if(auth()->user()->can('Behavioural-list'))
  <div class="dropdown">
    <a  role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="behavioural">
        Behavioural <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">
          @can('Behavioural-list')
          <li><a class="dropdown-item" href="{{ route('behaviouraltype')}}" id="">Atributes</a></li>
          @endcan
          @can('Behavioural-list')
          <li><a class="dropdown-item" href="{{ route('behaviouralweightage')}}">Weightage</a></li>
          @endcan
          
        </ul>
  </div>
  @endif

</div>



