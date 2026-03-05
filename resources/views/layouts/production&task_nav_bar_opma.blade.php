<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
    <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="dailymaster">
      Master Data<span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

      <li><a class="dropdown-item" href="{{ route('opma_machines')}}">Machines</a></li>

      <li><a class="dropdown-item" href="{{ route('products')}}">Styles</a></li>

      <li><a class="dropdown-item" href="{{ route('products')}}">Sizes</a></li>

    </ul>
  </div>
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="dailyprocess">
      Daily Production Process <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

      <li><a class="dropdown-item" href="{{ route('productionallocation')}}">Employee Allocation</a></li>

      <li><a class="dropdown-item" href="{{ route('productionending')}}">Daily Process Ending</a></li>

      <li><a class="dropdown-item" href="{{ route('employeeproductionreport')}}">Employee Production </a></li>

    </ul>
  </div>

  
   <a role="button" class="btn navbtncolor" href="{{ route('productiontaskapprove') }}" id="taskapprove">Production Approval <span class="caret"></span></a>
 
</div>


