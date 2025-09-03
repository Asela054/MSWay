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

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                 {!! Form::model($permission, ['route' => ['permissions.update', $permission->id], 'method' => 'PUT']) !!}
                        <div class="row">
                            
                            <!-- Permission Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    {!! Form::text('name', old('name', $permission->name), ['placeholder' => 'Permission name', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>

                            <!-- Guard Name -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Guard Name:</strong>
                                    <select name="guard_name" class="form-control form-control-sm">
                                        <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>web</option>
                                        <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>api</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Module -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Module:</strong>
                                    <select name="module" class="form-control form-control-sm">
                                        <option value="">-- Select Module --</option>
                                        @foreach($modules as $module)
                                           <option value="{{ $module->module }}" {{ old('module', $permission->module ?? '') == $module->module ? 'selected' : '' }}>
                                                {{ $module->module }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#administrator_menu_link').addClass('active');
            $('#administrator_menu_link_icon').addClass('active');
            $('#permissions_link').addClass('navbtnactive');
        });
    </script>
@endsection
