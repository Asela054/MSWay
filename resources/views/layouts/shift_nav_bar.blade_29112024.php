
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('shift-list')
  || auth()->user()->can('work-shift-list')
  || auth()->user()->can('additional-shift-list'))
      <div class="dropdown">
        @can('shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('Shift') }}" id="shift_link">Employee Shifts <span class="caret"></span></a>
        @endcan
        @can('work-shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('ShiftType') }}" id="work_shift_link">Work Shifts <span class="caret"></span></a>
        @endcan
        @can('additional-shift-list')
        <a role="button" class="btn navbtncolor" href="{{ route('AdditionalShift.index') }}" id="additional_shift_link">Additional Shifts <span class="caret"></span></a>
        @endcan
      </div>
  @endif
    </div>


