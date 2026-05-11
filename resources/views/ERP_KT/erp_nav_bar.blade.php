<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="erp_kt_master">
      Master Data <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

      <li><a class="dropdown-item" href="{{ route('kt_customer')}}">Customer</a></li>
      <li><a class="dropdown-item" href="{{ route('kt_inquiry')}}">Inquiry</a></li>
      <li><a class="dropdown-item" href="{{ route('kt_machines')}}">Machine</a></li>
      <li><a class="dropdown-item" href="{{ route('kt_special_rate')}}">Special Rate</a></li>
    </ul>
  </div>
  <div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="erp_kt_calculations">
      Calculations<span class="caret"></span></a>
    <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

      <li><a class="dropdown-item" href="{{ route('kt_quotations')}}">Quotation</a></li>
       <li><a class="dropdown-item" href="{{ route('kt_inquiry_approve')}}">Inquiry Approve</a></li>
        <li><a class="dropdown-item" href="{{ route('kt_job_create')}}">Job Create</a></li>
        <li><a class="dropdown-item" href="{{ route('kt_job_approve')}}">Job Approve</a></li>
    </ul>
  </div>
</div>
