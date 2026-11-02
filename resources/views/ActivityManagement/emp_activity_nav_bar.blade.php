<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
    <div class="dropdown">
        <a role="button" data-toggle="dropdown" class="btn navbtncolor" href="#" id="iou">
        IOU <span class="caret"></span></a>
        <ul class="dropdown-menu multi-level dropdownmenucolor" role="menu" aria-labelledby="dropdownMenu">

        <li><a class="dropdown-item" href="{{ route('IOUReason')}}">IOU Reason</a></li>
        <li><a class="dropdown-item" href="{{ route('IOURequest')}}">IOU Request</a></li>
        </ul>
    </div>

    <a role="button" class="btn navbtncolor" href="{{ route('VisitPlace') }}" id="visitplace">Visit Places <span class="caret"></span></a>

    <a role="button" class="btn navbtncolor" href="{{ route('EmpCalendar') }}" id="empcalendar">Employee Calendar <span class="caret"></span></a>
  
</div>
