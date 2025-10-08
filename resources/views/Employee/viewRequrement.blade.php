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
                        <span>Employee Requrement</span>
                    </h1>
                </div>
            </div>
        </div>    
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="row">
                <div class="col-lg-9 col-md-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-2">
                                <div class="card-header">Add Employee Recruitment Details</div>
                                <div class="card-body"> 
                                    <form class="form-horizontal" method="POST" action="{{ route('EmployeeRequrementinsert') }}">
                                        {{ csrf_field() }}
                                        <input type="hidden" class="form-control form-control-sm" id="empid" name="empid" value="{{$id}}">
                                        <input type="hidden" class="form-control form-control-sm" id="recordoption" name="recordoption" value="{{$recordoption}}">
                                        <input type="hidden" class="form-control form-control-sm" id="recordID" name="recordID" value="{{$requrement['id']}}">
                            
                                        {{-- First Interview Section --}}
                                        <h6 class="title-style my-3"><span>First Interview</span></h6>
                                        <div class="form-row">
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">The Interviewer</label>
                                                <select name="first_interwer" id="first_interwer" class="form-control form-control-sm shipClass" required>
                                                    <option value="">Select</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{$employee->id}}" {{$employee->id == $requrement['first_interviewer'] ? 'selected' : ''}}>
                                                            {{$employee->emp_name_with_initial}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Date</label>
                                                <input type="date" class="form-control form-control-sm" id="first_interw_date" name="first_interw_date" value="{{$requrement['first_interview_date']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Outcome</label>
                                                <input type="text" class="form-control form-control-sm" id="first_interw_outcome" name="first_interw_outcome" value="{{$requrement['first_interview_outcome']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Comments</label>
                                                <input type="text" class="form-control form-control-sm" id="first_interw_comments" name="first_interw_comments" value="{{$requrement['first_interview_comments']}}">
                                            </div>
                                        </div>

                                        {{-- Second Interview Section --}}
                                        <h6 class="title-style my-3"><span>Second Interview</span></h6>
                                        <div class="form-row">
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">The Interviewer</label>
                                                <select name="second_interwer" id="second_interwer" class="form-control form-control-sm shipClass">
                                                    <option value="">Select</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{$employee->id}}" {{$employee->id == $requrement['second_interviewer'] ? 'selected' : ''}}>
                                                            {{$employee->emp_name_with_initial}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Date</label>
                                                <input type="date" class="form-control form-control-sm" id="second_interw_date" name="second_interw_date" value="{{$requrement['second_interview_date']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Outcome</label>
                                                <input type="text" class="form-control form-control-sm" id="second_interw_outcome" name="second_interw_outcome" value="{{$requrement['second_interview_outcome']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Comments</label>
                                                <input type="text" class="form-control form-control-sm" id="second_interw_comments" name="second_interw_comments" value="{{$requrement['second_interview_comments']}}">
                                            </div>
                                        </div>

                                        {{-- Third Interview Section --}}
                                        <h6 class="title-style my-3"><span>Third Interview</span></h6>
                                        <div class="form-row">
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">The Interviewer</label>
                                                <select name="third_interwer" id="third_interwer" class="form-control form-control-sm shipClass">
                                                    <option value="">Select</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{$employee->id}}" {{$employee->id == $requrement['third_interviewer'] ? 'selected' : ''}}>
                                                            {{$employee->emp_name_with_initial}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Date</label>
                                                <input type="date" class="form-control form-control-sm" id="third_interw_date" name="third_interw_date" value="{{$requrement['third_interview_date']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Interview Outcome</label>
                                                <input type="text" class="form-control form-control-sm" id="third_interw_outcome" name="third_interw_outcome" value="{{$requrement['third_interview_outcome']}}">
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                                <label class="small font-weight-bold text-dark">Comments</label>
                                                <input type="text" class="form-control form-control-sm" id="third_interw_comments" name="third_interw_comments" value="{{$requrement['third_interview_comments']}}">
                                            </div>
                                        </div>

                                        <div class="form-group mt-3 text-right">
                                            @can('employee-edit')
                                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                                    <i class="fas fa-save"></i>&nbsp;Save
                                                </button>
                                            @endcan
                                        </div>
                                        <input type="hidden" class="form-control" id="id" name="id" value="{{$id}}">
                                    </form>
                                </div>
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
        $(document).ready(function() {

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
        $('#view_emprequment_link').addClass('active');

        $('#first_interwer').select2();
        $('#second_interwer').select2();
        $('#third_interwer').select2();

        });
    </script>
@endsection
