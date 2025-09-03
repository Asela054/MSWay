<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasShiftAccess = $user->can('shift-list') ||
                    $user->can('work-shift-list') ||
                    $user->can('additional-shift-list') ||
                    $user->can('employee-shift-allocation-list') ||
                    $user->can('employee-shift-extend-list');
  @endphp

  @if($hasShiftAccess)
      <div class="dropdown">
        @if($user->can('shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('Shift') }}" id="shift_link">Employee Shifts <span class="caret"></span></a>
        @endif
        @if($user->can('work-shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('ShiftType') }}" id="work_shift_link">Work Shifts <span class="caret"></span></a>
        @endif
        @if($user->can('additional-shift-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('AdditionalShift.index') }}" id="additional_shift_link">Additional Shifts <span class="caret"></span></a>
        @endif

        @if($user->can('employee-shift-allocation-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('employeeshift') }}" id="employeeshift_link">Employee Night Shift Assign <span class="caret"></span></a>
        @endif
        
        @if($user->can('employee-shift-extend-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('empshiftextend') }}" id="employeeshift_extend_link">Employee Shift Extend Assign <span class="caret"></span></a>
        @endif
      </div>
  @endif
</div>