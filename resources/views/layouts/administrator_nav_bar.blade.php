
<div class="row nowrap" style="padding-top: 5px;padding-bottom: 5px;">

  @if(auth()->user()->can('user-list') || auth()->user()->can('role-list'))
      <div class="dropdown">
        @can('user-list')
        <a role="button" class="btn navbtncolor" href="{{ route('users.index') }}" id="users_link">Users <span class="caret"></span></a>
        @endcan
        @can('role-list')
        <a role="button" class="btn navbtncolor" href="{{ route('roles.index') }}" id="roles_link">Roles <span class="caret"></span></a>
        @endcan
        @can('permission-list')
        <a role="button" class="btn navbtncolor" href="{{ route('permissions.index') }}" id="roles_link">Permissions <span class="caret"></span></a>
        @endcan
      </div>
  @endif
    </div>


