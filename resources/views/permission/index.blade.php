@extends('layouts.app')
@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.administrator_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            @can('role-create')
                                <a class="btn btn-success btn-sm float-right" href="{{ route('permissions.create') }}"> Create New Permission</a>
                            @endcan
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <span>{{ $message }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 table-responsive">

                            <table class="table table-bordered" id="permissiontable">
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Module</th>
                                    <th width="280px">Action</th>
                                </tr>
                                @foreach ($permissions as $key => $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->module }}</td>
                                        <td>
                                            
{{--                                            @can('permission-edit')--}}
                                                <a class="btn btn-primary btn-sm" href="{{ route('permissions.edit',$permission->id) }}"><i class="fa fa-pencil-alt"></i></a>
{{--                                            @endcan--}}
                                            @can('permission-edit')
                                                @if($permission->id != 1)
                                                    <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}" id="delete_form" style="display:inline">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this permission?')"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('script')
    <script>
        $(document).ready(function(){
        $('#permissiontable').DataTable({
            order: [[0, 'asc']] // Column index 0, ascending order
        });

            $('#administrator_menu_link').addClass('active');
            $('#administrator_menu_link_icon').addClass('active');
            $('#permissions_link').addClass('navbtnactive');

        });
    </script>
@endsection
