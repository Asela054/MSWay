<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">
  @php
    $user = auth()->user();
    $hasAdminAccess = $user->can('user-list') || $user->can('role-list');
  @endphp

  @if($hasAdminAccess)
      <div class="dropdown">
        @if($user->can('user-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('users.index') }}" id="users_link">Users <span class="caret"></span></a>
        @endif
        @if($user->can('role-list'))
        <a role="button" class="btn navbtncolor" href="{{ route('roles.index') }}" id="roles_link">Roles <span class="caret"></span></a>
        @endif
      </div>
  @endif
</div>