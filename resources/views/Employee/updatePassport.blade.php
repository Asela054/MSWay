@extends('layouts.app')
@section('style')
    <style>
        .help-block {
            color: red;
        }
    </style>
@endsection
@section('content')
    <main>
        <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                        <span>Passport Update</span>
                    </h1>
                </div>
            </div>
        </div>    

       <div class="container-fluid mt-2 p-0 p-2">
            <div class="row">
                <div class="col-lg-9 col-md-12">
                    <div id="default">
                        <div class="card mb-4">
                            <div class="card-header">Edit Passport Details</div>
                            <div class="card-body">
                                <form id="PdetailsForm" class="form-horizontal" method="POST"
                                    action="{{ route('passportUpdate') }}">
                                    {{ csrf_field() }}
                                    
                                    <input class="form-control" id="emp_pass_id" name="emp_pass_id" type="hidden"
                                        value="{{$id}}">
                                    
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="issue_date">Issued Date</label>
                                                <input class="form-control form-control-sm" id="issue_date" name="issue_date"
                                                    value="{{$passport->emp_pass_issue_date}}"
                                                    type="date">
                                                @if ($errors->has('issue_date'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('issue_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="expire_date">Expire Date</label>
                                                <input class="form-control form-control-sm" id="expire_date" name="expire_date"
                                                    type="date"
                                                    value="{{$passport->emp_pass_expire_date}}">
                                                @if ($errors->has('expire_date'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('expire_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_comments">Comments</label>
                                                <input class="form-control form-control-sm" id="pass_comments" name="pass_comments"
                                                    type="text"
                                                    value="{{$passport->emp_pass_comments}}">
                                                @if ($errors->has('pass_comments'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_comments') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="pass_type">Passport Type</label>
                                                <input class="form-control form-control-sm" id="pass_type" name="pass_type" type="text"
                                                    value="{{$passport->emp_pass_type}}">
                                                @if ($errors->has('pass_type'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_type') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_status">Passport Status</label>
                                                <input class="form-control form-control-sm" id="pass_status" name="pass_status"
                                                    type="text"
                                                    value="{{$passport->emp_pass_status}}">
                                                @if ($errors->has('pass_status'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_status') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_review">Passport Review</label>
                                                <input class="form-control form-control-sm" id="pass_review" name="pass_review"
                                                    type="text"
                                                    value="{{$passport->emp_pass_review}}">
                                                @if ($errors->has('pass_review'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_review') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="epf_no">EPF #</label>
                                                <input class="form-control form-control-sm" id="epf_no" name="epf_no"
                                                    type="text"
                                                    value="{{$passport->epf_no}}">
                                                @if ($errors->has('epf_no'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('epf_no') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mt-3">
                                                <input type="hidden" name="emp_id" value="{{$passport->emp_id}}"/>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                <button type="reset" class="btn btn-danger btn-sm">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.employeeRightBar')
            </div>
        </div>

    </main>
@endsection

@section('script')
    <script>
        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
        $('#view_passport_link').addClass('active');
    </script>
@endsection
