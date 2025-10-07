@extends('layouts.app')

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
                        <span>Employee Details</span>
                    </h1>
                </div>
            </div>
    </div>    
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-success btn-sm fa-pull-right ml-2" name="upload_record" id="upload_record"><i class="fas fa-upload mr-2"></i>Upload Employee</button>
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight"
                            aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Options</button>
                    </div><br><br>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                            <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>NAME</th>
                                    <th>NIC NO</th>
                                    <th>ETF NO</th>
                                    <th>DEPARTMENT</th>
                                    <th>JOIN DATE</th>
                                    <th>POSITION</th>
                                    <th>JOB CATEGORY</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-right">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                    <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <form class="form-horizontal" id="formFilter">
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Location</label>
                                <select name="location" id="location_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark"> From Date </label>
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark"> To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"  placeholder="yyyy-mm-dd">
                            </div>
                        </li>
                        <li>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm fa-pull-right ml-2" id="btn-filter">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                                <button type="button" class="btn btn-danger btn-sm fa-pull-right ml-2" id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                            </div>
                        </li>
                    </form>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="empModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title" id="staticBackdropLabel">Add Employee Record</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formemployee" class="form-horizontal">
                                {{ csrf_field() }}	
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">EPF No*</label>
                                        <input type="text" name="etfno" id="etfno" class="form-control form-control-sm" required />
                                        @if ($errors->has('etfno'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('etfno') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Employee ID*</label>
                                        <input type="text" name="emp_id" id="emp_id" class="form-control form-control-sm {{ $errors->has('emp_id') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('emp_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">First Name*</label>
                                        <input type="text" name="firstname" id="firstname" class="form-control form-control-sm {{ $errors->has('firstname') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('firstname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('firstname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Middle Name</label>
                                        <input type="text" name="middlename" id="middlename" class="form-control form-control-sm {{ $errors->has('middlename') ? ' has-error' : '' }}" />
                                        @if ($errors->has('middlename'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('middlename') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Last Name</label>
                                        <input type="text" name="lastname" id="lastname" class="form-control form-control-sm {{ $errors->has('lastname') ? ' has-error' : '' }}" />
                                        @if ($errors->has('lastname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('lastname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder">Full Name</label>
                                    <input type="text" name="emp_fullname" id="emp_fullname" class="form-control form-control-sm {{ $errors->has('emp_fullname') ? ' has-error' : '' }}" />
                                    @if ($errors->has('emp_fullname'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('emp_fullname') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Name with Initial*</label>
                                        <input type="text" name="emp_name_with_initial" id="emp_name_with_initial" class="form-control form-control-sm {{ $errors->has('emp_name_with_initial') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_name_with_initial'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('emp_name_with_initial') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Calling Name*</label>
                                        <input type="text" name="calling_name" id="calling_name" class="form-control form-control-sm {{ $errors->has('calling_name') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('calling_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('calling_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder">Identity Card No*</label>
                                    <input type="text" name="emp_id_card" id="emp_id_card" class="form-control form-control-sm {{ $errors->has('emp_id_card') ? ' has-error' : '' }}" required />
                                    @if ($errors->has('emp_id_card'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('emp_id_card') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Personal Number</label>
                                        <input type="text" name="telephone" id="telephone" class="form-control form-control-sm {{ $errors->has('telephone') ? ' has-error' : '' }}" />
                                        @if ($errors->has('telephone'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('telephone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Mobile Number*</label>
                                        <input type="text" name="emp_mobile" id="emp_mobile" class="form-control form-control-sm {{ $errors->has('emp_mobile') ? ' has-error' : '' }}" required />
                                        @if ($errors->has('emp_mobile'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('emp_mobile') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Office Extension</label>
                                        <input type="text" name="emp_work_telephone" id="emp_work_telephone" class="form-control form-control-sm {{ $errors->has('emp_work_telephone') ? ' has-error' : '' }}" />
                                        @if ($errors->has('emp_work_telephone'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('emp_work_telephone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder">Photograph</label>
                                    <input type="file" data-preview="#preview" class="form-control form-control-sm {{ $errors->has('photograph') ? ' has-error' : '' }}" name="photograph" id="photograph">
                                    <img class="" id="preview" src="" style="max-width: 200px; max-height: 200px; width: auto; height: auto;">
                                    @if ($errors->has('photograph'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('photograph') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Employee Status*</label>
                                        <select name="status" id="status" class="form-control form-control-sm shipClass {{ $errors->has('status') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($employmentstatus as $employmentstatu)
                                                <option value="{{$employmentstatu->id}}">{{$employmentstatu->emp_status}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('status'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Work Location*</label>
                                        <select name="location" class="form-control form-control-sm shipClass {{ $errors->has('location') ? ' has-error' : '' }}" required>
                                            <option value="">Please Select</option>
                                            @foreach($branch as $branches)
                                                <option value="{{$branches->id}}">{{$branches->location}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('location'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('location') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Employee Job*</label>
                                        <select name="employeejob" class="form-control form-control-sm shipClass {{ $errors->has('employeejob') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($title as $titles)
                                                <option value="{{$titles->id}}">{{$titles->title}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employeejob'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('employeejob') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Work Shift*</label>
                                        <select name="shift" class="form-control form-control-sm shipClass {{ $errors->has('shift') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($shift_type as $shift_types)
                                                <option value="{{$shift_types->id}}">{{$shift_types->shift_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('shift'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('shift') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Company*</label>
                                        <select name="employeecompany" id="company" class="form-control form-control-sm shipClass {{ $errors->has('employeecompany') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($company as $companies)
                                                <option value="{{$companies->id}}">{{$companies->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('employeecompany'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('employeejob') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Department*</label>
                                        <select name="department" id="department" class="form-control form-control-sm shipClass {{ $errors->has('department') ? ' has-error' : '' }}" required>
                                            <option value="">Select</option>
                                            @foreach($departments as $dept)
                                                <option value="{{$dept->id}}">{{$dept->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('department'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('department') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-row mb-1 no_of_leaves" hidden>
                                    <div class="col">
                                        <label class="small font-weight-bolder">No of Casual Leaves</label>
                                        <input type="number" name="no_of_casual_leaves" id="no_of_casual_leaves" class="form-control form-control-sm {{ $errors->has('no_of_casual_leaves') ? ' has-error' : '' }}" />
                                        @if ($errors->has('no_of_casual_leaves'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('no_of_casual_leaves') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">No of Annual Leaves</label>
                                        <input type="number" name="no_of_annual_leaves" id="no_of_annual_leaves" class="form-control form-control-sm {{ $errors->has('no_of_annual_leaves') ? ' has-error' : '' }}" />
                                        @if ($errors->has('no_of_annual_leaves'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('no_of_annual_leaves') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <button type="reset" class="btn btn-primary btn-sm"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>   
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="userlogModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee User Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="userlogform_result"></span>

                            <form id="userlogform" method="post">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">E-mail</label>
                                    <input type="text" class="form-control form-control-sm {{ $errors->has('email') ? ' has-error' : '' }} shipClass" id="email" name="email" placeholder="Type Employee Email">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Password</label>
                                    <input type="password" class="form-control form-control-sm {{ $errors->has('password') ? ' has-error' : '' }} shipClass" id="inputEmail4" name="password">
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Confirm Password</label>
                                    <input type="password" class="form-control form-control-sm {{ $errors->has('comfirmpassword') ? ' has-error' : '' }} shipClass" id="password-confirm" name="password_confirmation">
                                    @if ($errors->has('comfirmpassword'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comfirmpassword') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="userlog_action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" id="userlog_userid" name="userid">
                                <input type="hidden" id="userlog_name" name="name">
                                <input type="hidden" name="action" id="userlog_action" />
                                <input type="hidden" name="hidden_id" id="userlog_hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fpModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee User Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="fpform_result"></div>
                            <form id="fpform" method="post">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">ID</label>
                                    <input type="text" name="id" id="id" class="form-control form-control-sm" readonly />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Emp Id: </label>
                                    <input type="text" name="userid" id="fp_userid" class="form-control form-control-sm" readonly />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">name: </label>
                                    <input type="text" name="name" id="fp_name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">cardno: </label>
                                    <input type="text" name="cardno" id="cardno" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">role: </label>
                                    <select name="role" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        <option value="0">User</option>
                                        <option value="4">Admin</option>
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">password: </label>
                                    <input type="text" name="password" id="password" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">FP Location: </label>
                                    <select name="devices" class="form-control form-control-sm shipClass">
                                        <option value="">Select</option>
                                        @foreach($device as $devices)
                                        <option value="{{$devices->ip}}">{{$devices->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="fp_action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="fp_action" />
                                <input type="hidden" name="hidden_id" id="fp_hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->
</main>
    
{{-- resignation model --}}
<div class="modal fade" id="resignationformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Resignation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col"> 
                        <div id="resignform_result"></div>
                    <div class="form-group mb-1">
                        <label class="small font-weight-bold text-dark">Resign Date</label>
						<input type="date" class="form-control form-control-sm" id="resigndate" name="resigndate">
                    </div>

					<div class="form-group mb-1">
						<label class="small font-weight-bold text-dark">Comment</label>
						<textarea class="form-control form-control-sm" id="resignremark" name="resignremark"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <button type="button" name="resign_button" id="resign_button" class="btn btn-primary btn-sm fa-pull-right px-4 resign_button"><i class="fas fa-plus"></i>&nbsp;approve</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSV Modal -->
<div class="modal fade" id="uploadModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Upload Employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result1"></span>
                            <form method="post" id="formTitle1" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-group mb-1">
                                    <label class="control-label col" >
                                    File Content :
                                        <a class="col" href="{{ url('/public/csvsample/add_employees_format.csv') }}">
                                        CSV Format-Download Sample File
                                        </a>
                                    </label>
                                </div>	
                                <div class="fields">
                                    <div class="input-group mb-3">
                                        <input type="file" class="form-control" id="import_csv" name="import_csv" accept=".csv" required>
                                        <label class="input-group-text" for="import_csv">Upload</label>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="csv_action_button" class="btn btn-success btn-sm">
                                        <i class="fas fa-upload mr-2"></i>Import CSV
                                    </button>                               
                                 </div>
                                <input type="hidden" name="action" id="csv_action" value="Upload" />
                                <input type="hidden" name="hidden_id" id="csv_hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
<script>
$(document).ready(function () {

    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#employeeinformation').addClass('navbtnactive');

    $("#etfno").focusout(function(){
        let val = $(this).val();
        $('#emp_id').val(val);
    });

    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select a Company',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("company_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    department_f.select2({
        placeholder: 'Select a Department',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                    location: location_f.val()
                }
            },
            cache: true
        }
    });

    employee_f.select2({
        placeholder: 'Select a Employee',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                    location: location_f.val(),
                    department: department_f.val()
                }
            },
            cache: true
        }
    });

    location_f.select2({
        placeholder: 'Select Location',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("location_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                }
            },
            cache: true
        }
    });

    function load_dt(department, employee, location, from_date, to_date){
        $('#emptable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Details',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            "order": [
                [0, "desc"]
            ],
            ajax: {
                url: scripturl + "/employee_list.php",
                type: "POST",
                data: {
                    department: department,
                    employee: employee,
                    location: location,
                    from_date: from_date,
                    to_date: to_date
                },
            },
            columns: [
                {
                    "targets": -1,
                    "className": '',
                    "data": 'emp_id',
                    "name": 'emp_id',
                    "render": function(data, type, full) {
                        var linkColor = full['is_resigned'] == 1 ? 'style="color: red;"' : '';
                        return '<a href="viewEmployee/'+full['id']+'" '+linkColor+'>'+data+'</a>';
                    }
                },
                {
                    "targets": -1,
                    "className": '',
                    "data": 'employee_display', 
                    "name": 'employee_display',
                    "render": function(data, type, full) {
                        var linkColor = full['is_resigned'] == 1 ? 'style="color: red;"' : '';
                        return '<a href="viewEmployee/'+full['id']+'" '+linkColor+'>'+(data || '')+'</a>';
                    }
                },
                { 
                    data: 'emp_national_id', 
                    name: 'emp_national_id'
                },
                { 
                    data: 'emp_etfno', 
                    name: 'emp_etfno'
                },
                { 
                    data: 'name', 
                    name: 'name'
                },
                { 
                    data: 'emp_join_date', 
                    name: 'emp_join_date'
                },
                { 
                    data: 'title', 
                    name: 'title'
                },
                { 
                    data: 'category', 
                    name: 'category'
                },
                {
                    "targets": -1,
                    "className": 'text-center',
                    "data": 'emp_status',
                    "name": 'emp_status',
                    "render": function(data, type, full) {
                        if (full['is_resigned'] == 1) {
                            return '<span class="text-danger">Resigned</span>';
                        } else {
                            return '<span class="text-success">'+full['emp_status']+'</span>';
                        }
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var is_resigned = row.is_resigned;
                        var buttons = '';

                        if (is_resigned == 0) {
                            buttons += '<a style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="View Employee Details" class="btn btn-dark btn-sm" href="viewEmployee/' + row.id + '"><i class="far fa-clipboard"></i></a>';
                        }

                        if (is_resigned == 0) {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Add Employee Fingerprint Details" class="btn btn-primary btn-sm addfp" id="' + row.emp_id + '" name="' + (row.emp_name_with_initial || '') + '"><i class="fas fa-sign-in-alt"></i></button>';
                        }

                        if (is_resigned == 0) {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Add Employee User Login Details" class="btn btn-secondary btn-sm adduserlog" id="' + row.emp_id + '" name="' + (row.emp_name_with_initial || '') + '"><i class="fas fa-user"></i></button>';
                        }

                        if (is_resigned == 0) {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Resign Employee" class="btn btn-warning btn-sm resign" id="' + row.emp_id + '" name="' + (row.emp_name_with_initial || '') + '"><i class="fas fa-user-times"></i></button>';
                        }

                        if (is_resigned == 0) {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Delete Employee Details" class="btn btn-danger btn-sm delete" id="' + row.id + '"><i class="far fa-trash-alt"></i></button>';
                        }

                        return buttons;
                    }
                }
            ],
            order: [[0, "asc"]],
            destroy: true
        });
    }    

    load_dt('', '', '', '', '');

    $('#from_date').on('change', function() {
        let fromDate = $(this).val();
        $('#to_date').attr('min', fromDate); 
    });

    $('#to_date').on('change', function() {
        let toDate = $(this).val();
        $('#from_date').attr('max', toDate); 
    });

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department_f').val();
        let employee = $('#employee_f').val();
        let location = $('#location_f').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
    });

    document.getElementById('btn-reset').addEventListener('click', function() {
        document.getElementById('formFilter').reset();

        $('#company_f').val('').trigger('change');   
        $('#location_f').val('').trigger('change');
        $('#department_f').val('').trigger('change');
        $('#employee_f').val('').trigger('change');
        $('#from_date').val('');                     
        $('#to_date').val('');                       

        load_dt('', '', '', '', '');
    });

});
$(document).ready(function () {

    let company = $('#company');
    let department = $('#department');
    department.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val()
                }
            },
            cache: true
        }
    });

    $('#create_record').click(function () {

        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#empModal').modal('show');
        $('.modal-title').text('Add Employee Record');
    });

    $('#formemployee').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';
        var formData = new FormData(this);
        //alert(formData);

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('empoyeeRegister') }}";
        }

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: action_url,
            method: "POST",
            //data:$(this).serialize(),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (data) {

                var html = '';
                if (data.errors) {
                    var errorMessages = '';
                    for (var count = 0; count < data.errors.length; count++) {
                        errorMessages += data.errors[count] + '\n';
                    }
                    alert("Error(s):\n" + errorMessages); 
                }
                if (data.success) {
                    alert("Success: " + data.success);
                    $('#formemployee')[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                    $('#formemployee').modal('hide');
                }
                $('#form_result').html(html);
            }
        });
    });

    $('#upload_record').click(function() {
        $('.modal-title').text('Upload Employees Record');
        $('#csv_action_button').html('Import');
        $('#csv_action').val('Upload');
        $('#form_result1').html('');
        $('#formTitle1')[0].reset();

        $('#uploadModal').modal('show'); 
    });

    $('#formTitle1').on('submit', function(event) {
        event.preventDefault();
        if ($('#csv_action').val() == 'Upload') {
            var formData = new FormData(this); 
            var fileInput = $('#import_csv')[0].files[0];
            if (!fileInput || fileInput.type !== 'text/csv') {
                alert('Please upload a valid CSV file.');
                return;
            }
            $('#csv_action_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Importing...');
            $.ajax({
                url: "{{ route('import') }}",
                method: "POST",
                data: formData,
                processData: false, 
                contentType: false, 
                dataType: "json",
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        html += '<h6><i class="fas fa-exclamation-triangle mr-2"></i>CSV Import Errors:</h6>';
                        html += '<ul class="mb-0">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<li>' + data.errors[count] + '</li>';
                        }
                        html += '</ul>';
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">';
                        html += '<i class="fas fa-check-circle mr-2"></i>' + data.success;
                        html += '</div>';
                        $('#formTitle1')[0].reset();
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                    $('#form_result1').html(html);
                    $('#csv_action_button').prop('disabled', false).html('<i class="fas fa-upload mr-2"></i>Import CSV');
                },
                error: function(xhr, status, error) {
                    var html = '<div class="alert alert-danger">';
                    html += '<i class="fas fa-exclamation-triangle mr-2"></i>';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        html += 'CSV Import Errors:<ul class="mb-0 mt-2">';
                        xhr.responseJSON.errors.forEach(function(error) {
                            html += '<li>' + error + '</li>';
                        });
                        html += '</ul>';
                    } else {
                        html += 'An unexpected error occurred. Please try again.';
                    }
                    html += '</div>';
                    $('#form_result1').html(html);
                    $('#csv_action_button').prop('disabled', false).html('<i class="fas fa-upload mr-2"></i>Import CSV');
                }
            });
        }
    });

    var user_id;

    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this employee? ");
        if (r == true) {
            user_id = $(this).attr('id');

            $.ajax({
                url: "EmployeeDestroy/destroy/" + user_id,
                beforeSend: function () {
                    // Optional: Show loading state
                },
                success: function (data) {
                    const actionObj = {
                        icon: 'fas fa-trash-alt',
                        title: '',
                        message: 'Employee Record Deleted Successfully',
                        url: '',
                        target: '_blank',
                        type: 'danger'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    actionreload(actionJSON);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            })
        }
    });

    //userlog 

    $(document).on('click', '.adduserlog', function () {
        $('.modal-title').text('Add Employee User Login');
        $('#action_button').val('Add');
        $('#userlogform #id').val($(this).attr('data-id'));
        var id = $(this).attr('id');
        var name = $(this).attr('name');
        $('#userlog_userid').val(id);
        $('#userlog_name').val(name);
        $('#userlog_action').val('Add');
        $('#userlogform_result').html('');

        $('#userlogModal').modal('show');
    });

    $('#userlogform').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#userlog_action').val() == 'Add') {
            action_url = "{{ route('addUserLogin') }}";
        }


        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {

                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    // $('#fpform')[0].close();
                    //$('#emptable').DataTable().ajax.reload();
                    location.reload();
                }
                $('#userlogform_result').html(html);
            }
        });
    });

    //userlog

    //fingerprint 

    $(document).on('click', '.addfp', function () {
        $('.modal-title').text('Add Employee to Fingerprint');
        emp_id = $(this).attr('id');
        name = $(this).attr('name');

        $('#fp_action').val('Add');
        $('#fpform #id').val(emp_id);
        $('#fp_userid').val(emp_id);
        $('#fp_name').val(name);

        $('#action').val('Add');
        $('#fpform_result').html('');

        $('#fpModal').modal('show');
    });

    $('#fpform').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#fp_action').val() == 'Add') {
            action_url = "{{ route('addFingerprintUser') }}";
        }


        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {

                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#fpform_result').html(html);
                    $('#fpform')[0].close();
                    //$('#emptable').DataTable().ajax.reload();

                    location.reload();
                }

            }
        });
    });

    //fingerprint

    // resination
    $(document).on('click', '.resign', function () {
        user_id = $(this).attr('id');

        $('#resignform_result').html('');
        $('#resignationformModal').modal('show');
        $('#resigndate').val(''); 

        $.ajax({
            url: '{!! route("getEmployeeJoinDate") !!}',
            type: 'GET',
            data: { id: user_id },
            success: function (response) {
                if (response.join_date) {
                    $('#resigndate').attr('min', response.join_date);
                } else {
                    alert('Failed to fetch the join date.');
                }
            },
            error: function () {
                alert('An error occurred while fetching the join date.');
            }
        });
    });
    
    $(document).on('click', '.resign_button', async function () {
        var checkresignationdate = $('#resigndate').val()
        if(checkresignationdate == '') {
            alert('Please Select Date');
        } else {
            var r = await Otherconfirmation("You want to resign this employee? ");
            if (r == true) {
                var resignationdate = $('#resigndate').val();
                var resignationremark = $('#resignremark').val();
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                
                $.ajax({
                    url: '{!! route("employeeresignation") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        recordID: user_id,
                        resignationdate: resignationdate,
                        resignationremark: resignationremark,
                    },
                    beforeSend: function () {
                        // Optional: Show loading state
                    },
                    success: function (data) {
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-user-times',
                                title: '',
                                message: 'Employee Resignation Processed Successfully',
                                url: '',
                                target: '_blank',
                                type: 'warning'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                            
                            $('#resignationformModal').modal('hide');
                        } else if (data.errors) {
                            var html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                            $('#resignform_result').html(html);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                })
            }
        }
    });

    $('#ok_button2').click(function () {
        var resignationdate= $('#resigndate').val();
        var resignationremark= $('#resignremark').val();
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        $.ajax({
            url: '{!! route("employeeresignation") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    recordID: user_id,
                    resignationdate: resignationdate,
                    resignationremark: resignationremark,
                },
            beforeSend: function () {
                $('#ok_button2').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                    }
                    $('#resignform_result').html(html);

                    $('#confirmModal2').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                }, 2000);
                location.reload();
            }
        })
    });



});


</script>

@endsection