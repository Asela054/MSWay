@extends('layouts.app')

@section('content')
    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-lg-9">
                    <div id="default">
                        <div class="card mb-4">

                            <div class="card-body">
                                <div class="accordion" id="accordionExample">
                                    <div class="card card-header-actions mb-4">
                                        <div class="card-header">
                                            Work Experience
                                            @can('employee-edit')
                                                <button type="button" name="create_work" id="create_work"
                                                    class="btn btn-success btn-sm">Add
                                                </button>
                                            @endcan
                                        </div>

                                        <div id="expModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel"> Work
                                                            Experience</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <span id="expform_result"></span>
                                                        <form id="formexp" class="form-horizontal" method="POST">
                                                            {{ csrf_field() }}

                                                            <div class="form-group  ">
                                                                <label for="exampleFormControlInput1">
                                                                    Company</label>
                                                                <input type="hidden" class="form-control form-control-sm" id="emp_id"
                                                                       name="emp_id" value="{{$id}}">

                                                                <input class="form-control form-control-sm" id="company" name="company"
                                                                       type="text">
                                                                @if ($errors->has('company'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('company') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group  ">
                                                                <label for="exampleFormControlInput1">
                                                                    Job Title</label>
                                                                <input class="form-control form-control-sm" id="jobtitle"
                                                                       name="jobtitle" type="text">
                                                                @if ($errors->has('jobtitle'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('jobtitle') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group  ">
                                                                <label for="exampleFormControlInput1">
                                                                    From </label>
                                                                <input class="form-control form-control-sm" id="fromdate"
                                                                       name="fromdate" type="date">
                                                                @if ($errors->has('fromdate'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('fromdate') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group  ">
                                                                <label for="exampleFormControlInput1">
                                                                    To</label>
                                                                <input class="form-control form-control-sm" id="todate" name="todate"
                                                                       type="date">
                                                                @if ($errors->has('todate'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('todate') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>


                                                            <div class="form-group  ">
                                                                <label for="inputEmail3">Comment</label>
                                                                <textarea class="form-control form-control-sm" id="comment"
                                                                          name="comment" rows="3"></textarea>
                                                                @if ($errors->has('comment'))
                                                                    <span class="help-block">
                                                            <strong>{{ $errors->first('comment') }}</strong>
                                                        </span>
                                                                @endif
                                                            </div>


                                                            <br/>
                                                            <div class="form-group">
                                                                <input type="hidden" name="action" id="action"/>
                                                                <input type="hidden" name="hidden_id" id="hidden_id"/>
                                                                @can('employee-edit')
                                                                    <input type="submit" name="action_button"
                                                                       id="action_button" class="btn btn-warning float-right btn-sm"
                                                                       value="Add"/>
                                                                @endcan
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="expconfirmModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger  btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 align="center" style="margin:0;">Are you sure you want to
                                                            remove this data?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" name="expok_button" id="expok_button"
                                                                class="btn btn-danger  btn-sm">OK
                                                        </button>
                                                        <button type="button" class="btn btn-default  btn-sm"
                                                                data-dismiss="modal">Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="container-fluid mt-n1">
                                            <div class="card mb-4">

                                                <div class="card-body">
                                                    <div class="datatable table-responsive">
                                                        <table class="table table-bordered table-hover" id="dataTable"
                                                               width="100%" cellspacing="0">
                                                            <thead>
                                                            <tr>
                                                                <th>Company</th>
                                                                <th>Job Title</th>
                                                                <th>From</th>
                                                                <th>To</th>
                                                                <th>Comments</th>
                                                                <th>Action</th>

                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            @foreach($experience as $experiences)
                                                                <tr>
                                                                    <td>{{$experiences->emp_company}}</td>
                                                                    <td>{{$experiences->emp_jobtitle}}</td>
                                                                    <td>{{$experiences->emp_from_date}}</td>
                                                                    <td>{{$experiences->emp_to_date}}</td>
                                                                    <td>{{$experiences->emp_comment}}</td>
                                                                    <td>
                                                                        @can('employee-edit')
                                                                            <button name="expedit" id="{{$experiences->id}}"
                                                                                    class="expedit btn btn-primary btn-sm mr-1 mt-1"
                                                                                    type="submit"> <i class="fa fa-pencil-alt"></i>
                                                                            </button>
                                                                            <button type="submit" name="expdelete"
                                                                                    id="{{$experiences->id}}"
                                                                                    class="expdelete btn btn-danger btn-sm  mr-1 mt-1">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        @endcan
                                                                    </td>
                                                                </tr>

                                                            @endforeach


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                    <div class="card card-header-actions mb-4">
                                        <div class="card-header">
                                            Education
                                            @can('employee-edit')
                                                <button type="button" name="create_education" id="create_education"
                                                        class="btn btn-success btn-sm">Add
                                                </button>
                                            @endcan
                                        </div>


                                        <div id="edumodel" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Education</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger  btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <span id="eduform_result"></span>
                                                        <form id="formedu" class="form-horizontal" method="POST">
                                                            {{ csrf_field() }}

                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">Level </label>
                                                                <div class="form-group">
                                                                    <select class="form-control form-control-sm" id="level"
                                                                            name="level">
                                                                        <option>Select</option>
                                                                        <option value="Grade 5">Grade 5</option>
                                                                        <option value="O/L">O/L</option>
                                                                        <option value="A/L">A/L</option>
                                                                        <option value="Diploma">Diploma</option>
                                                                        <option value="HND">HND</option>
                                                                        <option value="Degree">Degree</option>
                                                                    </select>

                                                                </div>
                                                                @if ($errors->has('level'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('level') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    Institute</label>
                                                                <input type="hidden" class="form-control form-control-sm" id="emp_id"
                                                                       name="emp_id" value="{{$id}}">

                                                                <input class="form-control form-control-sm" id="instiitute"
                                                                       name="instiitute" type="text">
                                                                @if ($errors->has('instiitute'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('instiitute') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    Year</label>
                                                                <input class="form-control form-control-sm" id="year" name="year"
                                                                       type="number" min="1900" max="2099" step="1" value="{{Date('Y')}}">

                                                                @if ($errors->has('year'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('year') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    SPA/Score</label>
                                                                <input class="form-control form-control-sm" id="score" name="score"
                                                                       type="text">
                                                                @if ($errors->has('score'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('score') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    Specification</label>
                                                                <input class="form-control form-control-sm" id="specification"
                                                                       name="specification" type="text">
                                                                @if ($errors->has('specification'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('specification') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    From </label>
                                                                <input class="form-control form-control-sm" id="startdate"
                                                                       name="startdate" type="date">
                                                                @if ($errors->has('startdate'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('startdate') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group ">
                                                                <label for="exampleFormControlInput1">
                                                                    To</label>
                                                                <input class="form-control form-control-sm" id="enddate" name="enddate"
                                                                       type="date">
                                                                @if ($errors->has('enddate'))
                                                                    <span class="help-block">
                                                                <strong>{{ $errors->first('enddate') }}</strong>
                                                            </span>
                                                                @endif
                                                            </div>

                                                            <br/>
                                                            <div class="form-group" align="center">
                                                                @can('employee-edit')
                                                                    <input type="hidden" name="eduaction" id="eduaction"
                                                                           value="Add"/>
                                                                    <input type="hidden" name="edu_hidden_id"
                                                                           id="edu_hidden_id"/>
                                                                    <input type="submit" name="eduaction_button"
                                                                           id="eduaction_button" class="btn btn-warning float-right  btn-sm"
                                                                           value="Add"/>
                                                                @endcan
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="educonfirmModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger  btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 align="center" style="margin:0;">Are you sure you want to
                                                            remove this data?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        @can('employee_edit')
                                                            <button type="button" name="eduok_button" id="eduok_button"
                                                                    class="btn btn-danger  btn-sm">OK
                                                            </button>
                                                            <button type="button" class="btn btn-default  btn-sm"
                                                                    data-dismiss="modal">Cancel
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="container-fluid mt-n1">
                                            <div class="card mb-4">

                                                <div class="card-body">
                                                    <div class="datatable table-responsive">
                                                        <table class="table table-bordered table-hover" id="dataTable"
                                                               width="100%" cellspacing="0">
                                                            <thead>
                                                            <tr>
                                                                <th>Level</th>
                                                                <th>Institute</th>
                                                                <th>Specification</th>
                                                                <th>Year</th>
                                                                <th>Score</th>
                                                                <th>Action</th>

                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            @foreach($education as $educations)
                                                                <tr>
                                                                    <td>{{$educations->emp_level}}</td>
                                                                    <td>{{$educations->emp_institute}}</td>
                                                                    <td>{{$educations->emp_specification}}</td>
                                                                    <td>{{$educations->emp_year}}</td>
                                                                    <td>{{$educations->emp_gpa}}</td>
                                                                    <td>
                                                                        @can('employee-edit')
                                                                            <button name="eduedit" id="{{$educations->id}}"
                                                                                    class="eduedit btn btn-primary btn-sm  mr-1 mt-1"
                                                                                    type="submit">
                                                                                <i class="fa fa-pencil-alt"></i>
                                                                            </button>
                                                                            <button type="submit" name="edudelete"
                                                                                    id="{{$educations->id}}"
                                                                                    class="edudelete btn btn-danger btn-sm  mr-1 mt-1">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        @endcan
                                                                    </td>
                                                                </tr>

                                                            @endforeach


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="card card-header-actions mb-4">
                                        <div class="card-header">
                                            Skill
                                            @can('employee-edit')
                                                <button type="button" name="create_skill" id="create_skill"
                                                        class="btn btn-success btn-sm">Add
                                                </button>
                                            @endcan
                                        </div>

                                        <div id="skillModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Skill</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger  btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <span id="skillform_result"></span>
                                                        <form id="formskill" class="form-horizontal" method="POST">
                                                            {{ csrf_field() }}
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">Skill </label>
                                                                <div class="form-group">
                                                                    <select class=" form-control form-control-sm" id="skill"
                                                                            name="skill" required>
                                                                        <option value="">Select</option>
                                                                        <option value="Swimming">Swimming</option>
                                                                        <option value="Sport">Sport</option>
                                                                        <option value="Cricket">Cricket</option>
                                                                        <option value="Other">Other</option>
                                                                    </select>

                                                                </div>
                                                                @if ($errors->has('skill'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('skill') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="exampleFormControlInput1">
                                                                    Year of Experience </label>
                                                                <input type="hidden" class="form-control form-control-sm" id="emp_id"
                                                                       name="emp_id" value="{{$id}}">

                                                                <input class="form-control form-control-sm" id="experience"
                                                                       name="experience" type="text">
                                                                @if ($errors->has('experience'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('experience') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>


                                                            <div class="form-group">
                                                                <label for="inputEmail3">Comment</label>
                                                                <textarea class="form-control form-control-sm" id="skill_comment"
                                                                          name="comment" rows="3"></textarea>
                                                                @if ($errors->has('comment'))
                                                                    <span class="help-block">
                                                                        <strong>{{ $errors->first('comment') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>


                                                            <br/>
                                                            <div class="form-group">
                                                                @can('employee-edit')
                                                                    <input type="hidden" name="skillaction" id="skillaction"
                                                                           value="Add"/>
                                                                    <input type="hidden" name="skill_hidden_id"
                                                                           id="skill_hidden_id"/>
                                                                    <input type="submit" name="skillaction_button"
                                                                           id="skillaction_button" class="btn btn-warning float-right  btn-sm"
                                                                           value="Add"/>
                                                                @endcan
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="skillconfirmModal" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                                                        <button class="close" type="button" data-dismiss="modal"
                                                                aria-label="Close"><span class="btn btn-danger btn-sm"
                                                                                         aria-hidden="true">X</span>
                                                        </button>

                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 align="center" style="margin:0;">Are you sure you want to
                                                            remove this data?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" name="skillok_button" id="skillok_button"
                                                                class="btn btn-danger">OK
                                                        </button>
                                                        <button type="button" class="btn btn-default  btn-sm"
                                                                data-dismiss="modal">Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="container-fluid mt-n1">
                                            <div class="card mb-4">

                                                <div class="card-body">
                                                    <div class="datatable table-responsive">
                                                        <table class="table table-bordered table-hover" id="dataTable"
                                                               width="100%" cellspacing="0">
                                                            <thead>
                                                            <tr>
                                                                <th>Skill</th>
                                                                <th>Experience</th>
                                                                <th>Comments</th>
                                                                <th class="text-right">Action</th>

                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            @foreach($skill as $skills)
                                                                <tr>
                                                                    <td>{{$skills->emp_skill}}</td>
                                                                    <td>{{$skills->emp_experience}}</td>
                                                                    <td>{{$skills->emp_comment}}</td>
                                                                    <td class="text-right">
                                                                        @can('employee-edit')
                                                                            <button name="skilledit" id="{{$skills->id}}"
                                                                                    class="skilledit btn btn-primary btn-sm  mr-1 mt-1"
                                                                                    type="submit">
                                                                                <i class="fa fa-pencil-alt"></i>
                                                                            </button>
                                                                            <button type="submit" name="skilldelete"
                                                                                    id="{{$skills->id}}"
                                                                                    class="skilldelete btn btn-danger btn-sm  mr-1 mt-1">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        @endcan
                                                                    </td>
                                                                </tr>

                                                            @endforeach


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                @include('layouts.employeeRightBar')

            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card mb-4">
                <div class="card-header">Attachments</div>
                <div class="card-body">
                    <div class="sbp-preview">
                        <div class="sbp-preview-content">
                            <form class="form-horizontal" method="POST" action="{{ route('qulificationAttachment') }}"
                                  enctype="multipart/form-data">
                                {{ csrf_field() }}


                                <div class="form-row">
                                    <label class="form-label col-md-2">Select File</label>
                                    <div class="custom-file  col-lg-6">
                                        <input type="hidden" class="form-control form-control-sm" id="id" name="id" value="{{$id}}">
                                        <input type="file" class="custom-file-input form-control form-control-sm" id="empattachment"
                                               name="empattachment">
                                        @if ($errors->has('empattachment'))
                                            <span class="help-block">
                                                            <strong>{{ $errors->first('empattachment') }}</strong>
                                                        </span>
                                        @endif
                                        <label class="custom-file-label" for="validatedCustomFile">Choose
                                            file...</label>
                                        <div class="invalid-feedback">Example invalid custom file feedback</div>
                                    </div>
                                </div>

                                <div class="form-row mt-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">Comment</label>
                                    <div class="form-group">
                                        <textarea class="form-control form-control-sm" id="empcomment" name="empcomment"
                                                  rows="3"></textarea>
                                        @if ($errors->has('empcomment'))
                                            <span class="help-block">
                                                            <strong>{{ $errors->first('empcomment') }}</strong>
                                                        </span>
                                        @endif
                                    </div>
                                </div>

                                @can('employee-edit')
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </main>

@endsection
@section('script')
    <script>

        $(document).ready(function () {

            $('#employee_menu_link').addClass('active');
            $('#employee_menu_link_icon').addClass('active');
            $('#employeeinformation').addClass('navbtnactive');
            $('#view_qualification_link').addClass('navbtnactive');


            $('#create_work').click(function () {

                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                $('#expModal').modal('show');
            });


            $('#create_skill').click(function () {

                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                $('#skillModal').modal('show');
            });

            $('#create_education').click(function () {

                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                $('#edumodel').modal('show');
            });

        });


        // $('#todate').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
        // $('#fromdate').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
        // $('#enddate').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
        // $('#startdate').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });


        $('#formexp').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';
            var formData = new FormData(this);
            //alert(formData);

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('WorkExprienceInsert') }}";
            }

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('WorkExprience.update') }}";
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


                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formexp')[0].reset();
                        location.reload()
                    }
                    $('#expform_result').html(html);
                }
            });
        });

        $(document).on('click', '.expedit', function () {
            var id = $(this).attr('id');
            $('#expform_result').html('');
            $.ajax({
                url: "../WorkExprience/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#company').val(data.result.emp_company);
                    $('#jobtitle').val(data.result.emp_jobtitle);
                    $('#fromdate').val(data.result.emp_from_date);
                    $('#todate').val(data.result.emp_to_date);
                    $('#comment').val(data.result.emp_comment);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Exprience');
                    $('#action_button').val('Edit');
                    $('#action').val('Edit');
                    $('#expModal').modal('show');
                }
            })
        });


        var user_id;

        $(document).on('click', '.expdelete', function () {
            user_id = $(this).attr('id');
            $('#expconfirmModal').modal('show');
        });

        $('#expok_button').click(function () {
            $.ajax({
                url: "../WorkExprience/destroy/" + user_id,
                beforeSend: function () {
                    $('#expok_button').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        $('#formedu').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';
            var formData = new FormData(this);
            //alert(formData);

            if ($('#eduaction').val() == 'Add') {
                action_url = "{{ route('educationInsert') }}";
            }
            if ($('#eduaction').val() == 'Edit') {
                action_url = "{{ route('EmployeeEducation.update') }}";
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


                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formedu')[0].reset();
                        location.reload()
                    }
                    $('#eduform_result').html(html);

                }

            });
        });

        $(document).on('click', '.eduedit', function () {
            var id = $(this).attr('id');

            $('#eduform_result').html('');
            $.ajax({
                url: "../EmployeeEducation/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#level').val(data.result.emp_level);
                    $('#instiitute').val(data.result.emp_institute);
                    $('#specification').val(data.result.emp_specification);
                    $('#year').val(data.result.emp_year);
                    $('#score').val(data.result.emp_gpa);
                    $('#startdate').val(data.result.emp_start_date);
                    $('#enddate').val(data.result.emp_end_date);
                    $('#edu_hidden_id').val(id);
                    $('.modal-title').text('Edit Education');
                    $('#eduaction_button').val('Edit');
                    $('#eduaction').val('Edit');
                    $('#edumodel').modal('show');
                }
            })
        });


        var user_id;

        $(document).on('click', '.edudelete', function () {
            user_id = $(this).attr('id');
            $('#educonfirmModal').modal('show');
        });

        $('#eduok_button').click(function () {
            $.ajax({
                url: "../EmployeeEducation/destroy/" + user_id,
                beforeSend: function () {
                    $('#eduok_button').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

        $('#formskill').on('submit', function (event) {
            event.preventDefault();
            var action_url = '';
            var formData = new FormData(this);
            //alert(formData);

            if ($('#skillaction').val() == 'Add') {
                action_url = "{{ route('skillInsert') }}";
            }
            if ($('#skillaction').val() == 'Edit') {
                action_url = "{{ route('EmployeeSkill.update') }}";
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


                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formskill')[0].reset();
                        location.reload()
                    }
                    $('#skillform_result').html(html);

                }

            });
        });


        $(document).on('click', '.skilledit', function () {
            var id = $(this).attr('id');

            $('#skillform_result').html('');
            $.ajax({
                url: "../EmployeeSkill/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#skill').val(data.result.emp_skill);
                    $('#experience').val(data.result.emp_experience);
                    $('#skill_comment').val(data.result.emp_comment);
                    $('#skill_hidden_id').val(id);
                    $('.modal-title').text('Edit Education');
                    $('#skillaction_button').val('Edit');
                    $('#skillaction').val('Edit');
                    $('#skillModal').modal('show');
                }
            })
        });


        var user_id;

        $(document).on('click', '.skilldelete', function () {
            user_id = $(this).attr('id');

            $('#skillconfirmModal').modal('show');
        });

        $('#skillok_button').click(function () {
            $.ajax({
                url: "../EmployeeSkill/destroy/" + user_id,
                beforeSend: function () {
                    $('#skillok_button').text('Deleting...');
                },
                success: function (data) {
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#user_table').DataTable().ajax.reload();
                        alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
        });

    </script>
@endsection
