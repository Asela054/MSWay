@extends('layouts.app')
@section('style')
    <style>
        .help-block{
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
                        <span>Passport</span>
                    </h1>
                </div>
            </div>
        </div>    
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="row">
                <div class="col-lg-9">
                    <div id="default">
                        <div class="card mb-4">
                            <div class="card-header">Add Passport Details</div>
                            <div class="card-body">
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                @endif

                                <form id="PdetailsForm" class="form-horizontal" method="POST"
                                    action="{{ route('passportInsert') }}">
                                    {{ csrf_field() }}
                                    
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="emp_id">Employee Id</label>
                                                <input class="form-control form-control-sm" id="emp_id" name="emp_id" type="text"
                                                    value="{{$id}}" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label for="issue_date">Issued Date*</label>
                                                <input class="form-control form-control-sm" id="issue_date" name="issue_date" value="{{old('issue_date')}}"
                                                    type="date" required>
                                                @if ($errors->has('issue_date'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('issue_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="expire_date">Expire Date*</label>
                                                <input class="form-control form-control-sm" id="expire_date" name="expire_date" value="{{old('expire_date')}}"
                                                    type="date" required>
                                                @if ($errors->has('expire_date'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('expire_date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="pass_type">Passport Type</label>
                                                <input class="form-control form-control-sm" id="pass_type" name="pass_type" type="text" value="{{old('pass_type')}}">
                                                @if ($errors->has('pass_type'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_type') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_status">Passport Status</label>
                                                <input class="form-control form-control-sm" id="pass_status" name="pass_status" value="{{old('pass_status')}}"
                                                    type="text">
                                                @if ($errors->has('pass_status'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_status') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_review">Passport Review</label>
                                                <input class="form-control form-control-sm" id="pass_review" name="pass_review" value="{{old('pass_review')}}"
                                                    type="text">
                                                @if ($errors->has('pass_review'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_review') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="epf_no">Passport Number*</label>
                                                <input class="form-control form-control-sm" id="epf_no" name="epf_no" value="{{old('epf_no')}}"
                                                    type="text" required>
                                                @if ($errors->has('epf_no'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('epf_no') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="pass_comments">Comments</label>
                                                <input class="form-control form-control-sm" id="pass_comments" name="pass_comments" value="{{old('pass_comments')}}"
                                                    type="text">
                                                @if ($errors->has('pass_comments'))
                                                    <span class="help-block text-danger">
                                                        <strong>{{ $errors->first('pass_comments') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mt-2 text-right">
                                                @can('employee-edit')
                                                    <button type="submit" class="btn btn-primary btn-sm px-4 mb-2 mb-sm-0"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                                    <button type="reset" class="btn btn-danger btn-sm mr-2 mb-2 mb-sm-0"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr class="border-dark">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th>ISSUE DATE</th>
                                        <th>EXPIRY DATE</th>
                                        <th class="d-none d-md-table-cell">COMMENTS</th>
                                        <th>PASSPORT TYPE</th>
                                        <th class="d-none d-lg-table-cell">STATUS</th>
                                        <th class="d-none d-lg-table-cell">REVIEW</th>
                                        <th class="d-none d-md-table-cell">EPF #</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($passport as $passports)
                                        <tr>
                                            <td>{{$passports->emp_pass_issue_date}}</td>
                                            <td>{{$passports->emp_pass_expire_date}}</td>
                                            <td class="d-none d-md-table-cell">{{$passports->emp_pass_comments}}</td>
                                            <td>{{$passports->emp_pass_type}}</td>
                                            <td class="d-none d-lg-table-cell">{{$passports->emp_pass_status}}</td>
                                            <td class="d-none d-lg-table-cell">{{$passports->emp_pass_review}}</td>
                                            <td class="d-none d-md-table-cell">{{$passports->epf_no}}</td>

                                            <td class="text-right">
                                                @can('employee-edit')
                                                    <a href="{{route('passportEdit',$passports->emp_pass_id)}}" class="btn btn-sm btn-primary mr-1 mt-1"><i class="fa fa-pencil-alt"></i></a>
                                                    <a href="{{route('passportDestroy',$passports->emp_pass_id)}}" class="btn btn-sm btn-danger mr-1 mt-1"><i class="fa fa-trash"></i></a>
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
            @include('layouts.employeeRightBar')
        </div>
    </main>

@endsection
@section('script')
    <script>
        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
        $('#view_passport_link').addClass('active');

        $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Passport  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Passport Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Passport  Information',
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
        }
        );

        // $('#issue_date').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
        // $('#expire_date').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
        // $('#review_date').datepicker({
        //     format: "yyyy/mm/dd",
        //     autoclose: true
        // });
    </script>
@endsection
