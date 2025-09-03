<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasCorporateAccess = $user->can('location-list') || 
                         $user->can('company-list') || 
                         $user->can('bank-list') || 
                         $user->can('work-category-list') || 
                         $user->can('month-work-hours-list') ||
                         $user->can('SalaryAdjustment-list') ||
                         $user->can('Leave-Deduction-list');
  @endphp

  @if($hasCorporateAccess)
      <div class="dropdown">
        @if($user->can('location-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/Company') }}" id="companylink">Company <span class="caret"></span></a>
        @endif
        @if($user->can('company-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/Branch') }}" id="branchlink">Branch <span class="caret"></span></a>
        @endif
        @if($user->can('bank-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/Bank') }}" id="banklink">Bank <span class="caret"></span></a>
        @endif
        @if($user->can('job-category-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/JobCategory') }}" id="jobcategorylink">Job Category <span class="caret"></span></a>
        @endif
        @if($user->can('SalaryAdjustment-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/SalaryAdjustment') }}" id="salary_adjustmentlink">Salary Adjustments <span class="caret"></span></a>
        @endif
        @if($user->can('Leave-Deduction-list'))
        <a role="button" class="btn navbtncolor" href="{{ url('/LeaveDeduction') }}" id="leave_deductionlink">Leave Deductions <span class="caret"></span></a>
        @endif
      </div>
  @endif
</div>