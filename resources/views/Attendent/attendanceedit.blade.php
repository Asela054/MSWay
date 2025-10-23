@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                @include('layouts.attendant&leave_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                        <span>Attendance</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">

                            <div class="col-sm-12 col-md-12">
                                <div class="d-flex flex-wrap justify-content-end mb-2">
                                    <div class="col-sm-12 col-md-auto mb-1 px-1">
                                        <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="create_record" id="create_record">
                                            <i class="fas fa-plus mr-2"></i>Add - Single Date
                                        </button>
                                    </div>
                                    <div class="col-sm-12 col-md-auto mb-1 px-1">
                                        <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="edit_record_month" id="edit_record_month">
                                            <i class="fas fa-pencil-alt mr-2"></i>Add / Edit - Month
                                        </button>
                                    </div>
                                    <div class="col-sm-12 col-md-auto mb-1 px-1">
                                        <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="create_record_dept_wise" id="create_record_dept_wise">
                                            <i class="fas fa-plus mr-2"></i>Add - Department Wise
                                        </button>
                                    </div>
                                    <div class="col-sm-12 col-md-auto mb-1 px-1">
                                        <button type="button" class="btn btn-success btn-sm px-2 w-100" name="csv_upload_record" id="csv_upload_record">
                                            <i class="fas fa-upload mr-2"></i>Upload CSV
                                        </button>
                                    </div>
                                    <div class="col-sm-12 col-md-auto mb-1 px-1">
                                        <button type="button" class="btn btn-success btn-sm px-2 w-100" name="create_record_upload" id="create_record_upload">
                                            <i class="fa fa-upload mr-2"></i>Upload Attendance TXT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-2" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Options</button>
                                </div><br><br>
                        <div class="col-12" id="attendtable_outer">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="attendtable">
                                <thead>
                                <tr>
                                    <th>EMPLOYEE ID</th>
                                    <th>NAME</th>
                                    <th>DATE</th>
                                    <th>CHECK IN</th>
                                    <th>CHECK OUT</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
                                    <th class="text-right">ACTION</th>
                                     <th class="d-none">EMPNAME</th>
                                    <th class="d-none">CALLING</th>
                                    <th class="d-none">EMPID</th>
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
             @include('layouts.filter_menu_offcanves')
        </div>


        <!-- Modal Area Start -->
        <div class="modal fade" id="AttendaddModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add New Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formAdd" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bold text-dark">Employee*</label>
                                            <select name="employee" id="employee_single" class="form-control form-control-sm" required>
                                                <option value="">Select...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bold text-dark">In Time*</label>
                                            <input type="datetime-local" name="in_time_s" id="in_time_s" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" required/>
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bold text-dark">Out Time*</label>
                                            <input type="datetime-local" name="out_time_s" id="out_time_s" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" required/>
                                        </div>

                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="DepartmentAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel1">Attendance - Department Wise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="dept_wise_response"></div>
                        <form method="post" id="form_dept_wise" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col">
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bold text-dark">Company <span class="text-danger">*</span> </label>
                                            <select name="company" id="company_dept_wise" class="form-control form-control-sm">
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bold text-dark">Location <span class="text-danger">*</span> </label>
                                            <select name="location" id="location_dept_wise" class="form-control form-control-sm" >
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bold text-dark">Department <span class="text-danger">*</span> </label>
                                            <select name="department" id="department_dept_wise" class="form-control form-control-sm" >
                                            </select>
                                        </div>

                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bold text-dark">Date <span class="text-danger">*</span></label>
                                            <input type="date" id="date_dept_wise" name="date" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bold text-dark">&nbsp; </label> <br>
                                            <button type="button" name="action_button" id="btn-dept_wise" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-search"></i>&nbsp;Find</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered table-striped table-hover" id="table_dept_wise">
                                            <thead>
                                            <tr>
                                                <th>EMP ID</th>
                                                <th>EMPLOYEE</th>
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="btn-save_dept_wise" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AttendviewModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">View Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div id="message"></div>
                                <table id='attendTable' class="table table-striped table-bordered table-sm small">
                                    <thead>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AttendeditModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Edit Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div id="message"></div>
                                <table id='attendTable' class="table table-striped table-bordered table-sm small">
                                    <thead>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    
        <!-- Modal Area End -->

        <div class="modal fade" id="monthAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Attendance - Month</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="bulk_response"></div>
                        <form method="post" id="formMonth" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col">
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bold text-dark">Employee *</label>
                                            <select name="employee" id="employee_m" class="form-control form-control-sm" required>
                                                <option value="">Select...</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bold text-dark">Month*</label>
                                            <input type="month" id="month_m" name="month" class="form-control form-control-sm" min="2021-01" value="{{Date('Y-m')}}" required/>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="loading"></div>
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered table-striped table-hover" id="table_month">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>DAY</th>
                                                <th>IN TIME</th>
                                                <th>OUT TIME</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="btn-save" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-pencil-alt"></i>&nbsp;Update </button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel1">Upload Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="upload_response"></div>
                        <form method="post" id="formUpload" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col">
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-4">
                                            <label class="small font-weight-bold text-dark">Date*</label>
                                            <input required type="date" id="date_u" name="date" class="form-control form-control-sm" value="{{Date('Y-m-d')}}" />
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label class="small font-weight-bold text-dark">Machine* </label>
                                            <select id="machine" name="machine" class="form-control form-control-sm" required>
                                                <option value="">Select Machine</option>
                                                @foreach($fingerprints as $fingerprint)
                                                <option value="{{$fingerprint->id}}">{{$fingerprint->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <label class="small font-weight-bold text-dark">TXT File*</label>
                                            <input required type="file" id="txt_file_u" name="txt_file_u" class="form-control form-control-sm" accept="text/plain"/>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="loading"></div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="btn-upload" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-upload"></i>&nbsp;Upload </button>
                                    </div>
                                </div>
                            </div>

                        </form>

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
                        <h5 class="modal-title" id="staticBackdropLabel">Upload Attendance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <div class="modal-body">
                    <form method="post" target="_self" id="formTitle1" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-12">
                                <label class="font-weight-bolder small">
                                    File Content :
                                    <a class="font-weight-normal" href="{{ url('/public/csvsample/add_attendances_format.csv') }}">
                                        CSV Format-Download Sample File
                                    </a>
                                </label>
                            </div>
                        </div>
                        <div class="form-row mt-3">
                            <div class="col-12">
                                <h6 class="title-style small"><span>Upload File</span></h6>
                                <div class="input-group input-group-sm">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="import_csv" id="import_csv" aria-describedby="inputGroupFileAddon04" accept=".csv" required>
                                        <label class="custom-file-label" for="import_csv">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" name="action_button" id="action_button" required="required">Import CSV</button>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Upload" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>


    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function() {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

            $('#attendtable_outer').css('display', 'none');

             let company_f = $('#company');
            let department_f = $('#department');
            let location_f = $('#location');
            let employee_f = $('#employee');
            let area_f = $('#area_f');

            company_f.select2({
                placeholder: 'Select...',
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

            area_f.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("area_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val()
                        }
                    },
                    cache: true
                }
            });

            location_f.select2({
                placeholder: 'Select...',
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
                            area: area_f.val()
                        }
                    },
                    cache: true
                }
            });

            department_f.select2({
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
                            company: company_f.val(),
                            area: area_f.val(),
                            location: location_f.val()
                        }
                    },
                    cache: true
                }
            });

            employee_f.select2({
                placeholder: 'Select...',
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
                            area: area_f.val(),
                            location: location_f.val(),
                            department: department_f.val()
                        }
                    },
                    cache: true
                }
            });

            //employee_m
            $('#employee_m').select2({
                placeholder: 'Select...',
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
                            area: area_f.val(),
                            location: location_f.val(),
                            department: department_f.val()
                        }
                    },
                    cache: true,
                    dropdownParent: $('#monthAtModal')
                }
            });

            load_dt();
            $('#empty_msg').css('display', 'none');
            $('#attendtable_outer').css('display', 'block');

            function load_dt(company, location, department, employee, from_date, to_date) {
                $('#attendtable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Attendance  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Attendance  Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Attendance   Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    ajax: {
                        url: scripturl + "/attendance_list_for_edit.php",
                        type: "POST",
                        data: {
                            company: company,
                            location: location,
                            department: department,
                            employee: employee,
                            from_date: from_date,
                            to_date: to_date,
                        }
                    },
                    columns: [
                        {
                            "data": "uid",
                            "name": "uid",
                        },
                        {
                            "data": "employee_display",
                            "name": "employee_display",
                        },
                        {
                            "data": "date",
                            "name": "date",
                        },
                        {
                            "data": "first_time_stamp",
                            "name": "first_time_stamp",
                        },
                        {
                            "data": "last_time_stamp",
                            "name": "last_time_stamp",
                        },
                        {
                            "data": "location",
                            "name": "location",
                        },
                        {
                            "data": "dep_name",
                            "name": "dep_name",
                        },
                        {
                            "data": "id",
                            "name": "action",
                            "className": 'text-right',
                            "orderable": false,
                            "searchable": false,
                            "render": function(data, type, full) {
                                var uid = full['uid'];
                                var date = full['date'];
                                var emp_name = full['emp_name_with_initial'];
                                var button = '';

                                    button += '<button type="button" class="btn btn-dark btn-sm view_button" data-uid="' + uid + '" data-recorddate="' + date + '" data-name="' + emp_name + '" title="View" data-toggle="tooltip">' +
                                            '<i class="fas fa-eye"></i>' +
                                            '</button> ';
                              
                                    // button += '<button type="button" class="btn btn-primary btn-sm edit_button" data-uid="'+ uid + '" data-date="' + date + '" data-name="' + emp_name + '" data-toggle="tooltip" title="Edit">' +
                                    //         '<i class="fas fa-pencil-alt"></i>' +
                                    //         '</button> ';
                               

                                    button += '<button type="button" class="btn btn-danger btn-sm delete_button" data-uid="' + uid + '" data-date="' + date + '" data-name="' + emp_name + '" data-toggle="tooltip" title="Delete">' +
                                            '<i class="fas fa-trash-alt"></i>' +
                                            '</button>';

                                return button;
                            }
                        },
                        {"data": "emp_name_with_initial", 
                        "name": "emp_name_with_initial", 
                        "visible": false
                        },
                        {"data": "calling_name",
                         "name": "calling_name", 
                         "visible": false
                        },
                        {"data": "emp_id", 
                        "name": "emp_id", 
                        "visible": false
                        }
                    ],
                    destroy: true,
                    order: [[2, "desc"]],
                     drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });
            }

            $('#from_date').on('change', function() {
                let fromDate = $(this).val();
                $('#to_date').attr('min', fromDate);
            });

            $('#to_date').on('change', function() {
                let toDate = $(this).val();
                $('#from_date').attr('max', toDate);
            });

            $('#formFilter').on('submit', function(e) {
                e.preventDefault();
                let company = $('#company').val();
                let location = $('#location').val();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                load_dt(company, location, department, employee, from_date, to_date);
                closeOffcanvasSmoothly();
            });

            $('#edit_record_month').click(function () {
                $('#bulk_response').html('');
                $('#monthAtModal').modal('show');
            });

            let emp = $('#employee_m');
            let month = $('#month_m');

            $(emp).on('change', function() {
                let emp_id = emp.val();
                let month_id = month.val();
                if(emp_id != '' && month_id != '' )
                {
                    fill_month_table(month_id);
                }
            });

            $(month).on('change', function() {
                let emp_id = emp.val();
                let month_id = month.val();
                if(emp_id != '' && month_id != '' )
                {
                    fill_month_table(month_id);
                }
            });

            function fill_month_table(month_id){
                //get month attendances for the selected employee

                // $('#bulk_response').html('<i class="fa fa-spinner fa-spin"></i> loading...');

                let save_btn=$("#edit_record_month");
                let btn_prev_text = save_btn.html();
                save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let url_text = '{{ url("/attendance_list_for_month_edit") }}';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: url_text,
                    type: 'POST',
                    data: {
                        'month': month_id,
                        'emp': emp.val(),
                    },
                    success: function(res) {
                        if (res.status == 1) {

                            let month_n_y_arr = month_id.split('-');
                            let num_of_days = daysInMonth(month_n_y_arr[1] ,month_n_y_arr[0]);

                            let t = $('#table_month').DataTable({
                                "pageLength": 50,
                                "bDestroy": true,
                            });
                            t.clear();

                            for(let i = 1; i <= num_of_days; i++)
                            {
                                let day = get_day(month_n_y_arr[0], month_n_y_arr[1] ,i);
                                //console.log( month_n_y_arr[0]+' : '+ month_n_y_arr[1]+ ' : ' + i + ' : ' +day);
                                t.row.add([
                                    i,
                                    day,
                                    '<input type="datetime-local" class="form-control form-control-sm in_date_time" placeholder="YYYY-MM-DD HH:MM" id="in_'+i+'" name="in_time[]" /> ' +
                                    '<input type="hidden" value="'+i+'" name="date[]" />' +
                                    '<input type="hidden" value="" id="uid_'+i+'" name="uid[]" />' +
                                    '<input type="hidden" value="" id="emp_id_'+i+'" name="emp_id[]" />' +
                                    '<input type="hidden" value="'+month_id+'-'+i+'" id="date_'+i+'" name="date_e[]" />' +
                                    '<input type="hidden" value="" id="existing_time_stamp_in_'+i+'" name="existing_time_stamp_in[]" />' +
                                    '<input type="hidden" value="" id="existing_time_stamp_out_'+i+'" name="existing_time_stamp_out[]" />'+
                                    '<input type="hidden" value="" id="existing_time_stamp_in_rfc_'+i+'" name="existing_time_stamp_in_rfc[]" />'+
                                    '<input type="hidden" value="" id="existing_time_stamp_out_rfc_'+i+'" name="existing_time_stamp_out_rfc[]" />',
                                    '<input type="datetime-local" class="form-control form-control-sm out_date_time" placeholder="YYYY-MM-DD HH:MM" id="out_'+i+'" name="out_time[]" /> '
                                ]).node().id = i;
                                t.draw( false );
                            }

                            //loop through the response and fill the table
                            let attendances = res.attendances;
                            $.each(attendances, function(key,value) {
                                let date_no_arr = value.date.split(' ');
                                let date_only_arr = date_no_arr[0].split('-');
                                let date_no = parseInt(date_only_arr[2]);

                                let in_selector = $('#in_'+date_no);
                                let out_selector = $('#out_'+date_no);
                                let uid_selector = $('#uid_'+date_no);
                                let date_selector = $('#date_'+date_no);
                                let existing_time_stamp_in_selector = $('#existing_time_stamp_in_'+date_no);
                                let existing_time_stamp_out_selector = $('#existing_time_stamp_out_'+date_no);
                                let existing_time_stamp_in_rfc_selector = $('#existing_time_stamp_in_rfc_'+date_no);
                                let existing_time_stamp_out_rfc_selector = $('#existing_time_stamp_out_rfc_'+date_no);
                                let emp_id_selector = $('#emp_id_'+date_no);

                                uid_selector.val(value.uid);
                                date_selector.val(value.date);
                                emp_id_selector.val(value.emp_id);

                                if(value.firsttimestamp != ''){
                                    in_selector.val(value.firsttime_24);
                                    existing_time_stamp_in_selector.val(value.firsttime_24);
                                    existing_time_stamp_in_rfc_selector.val(value.firsttime_rfc);
                                }

                                if(value.lasttimestamp != ''){
                                    out_selector.val(value.lasttime_24);
                                    existing_time_stamp_out_selector.val(value.lasttime_24);
                                    existing_time_stamp_out_rfc_selector.val(value.lasttime_rfc);
                                }

                            });
                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);

                        }else {
                           
                          if (res.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }


                            save_btn.prop("disabled", false);
                            save_btn.html(btn_prev_text);
                        }
                    },
                    error: function(res) {
                           if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    }
                });

            }

            function tConvert (time) {
                // Check correct time format and split into components
                time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

                if (time.length > 1) { // If time format correct
                    time = time.slice (1);  // Remove full string match value
                    time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                    time[0] = +time[0] % 12 || 12; // Adjust hours
                }
                return time.join (''); // return adjusted time or original string
            }

            function get_day(year, month ,date){
                const days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                let date_rec = new Date(year+ '-' + month + '-' + date).getDay();
                return days[date_rec];
            }

            function daysInMonth (month, year) {
                return new Date(year, month, 0).getDate();
            }

            $('#formMonth').on('submit',function(e) {
                e.preventDefault();
                let formData = new FormData($('#formMonth')[0]);
                let url_text = '{{ url("/attendance_update_bulk_submit") }}';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: url_text,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                        if (res.status == 1) {
                             const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: 'Attendance Updated',
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            $('#formMonth')[0].reset();
                            actionreload(actionJSON);

                        }else {

                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    }
                });
            });

            $('#employee_single').select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_sel2")}}',
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

            $('#create_record_dept_wise').click(function () {
                $('#dept_wise_response').html('');
                $('#DepartmentAtModal').modal('show');
            });

            let company_dept_wise = $('#company_dept_wise');
            let area_dept_wise = $('#area_dept_wise');
            let location_dept_wise = $('#location_dept_wise');
            let department_dept_wise = $('#department_dept_wise');

            company_dept_wise.select2({
                placeholder: 'Select...',
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

            area_dept_wise.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("area_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_dept_wise.val()
                        }
                    },
                    cache: true
                }
            });

            location_dept_wise.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_dept_wise.val(),
                            area: area_dept_wise.val()
                        }
                    },
                    cache: true
                }
            });

            department_dept_wise.select2({
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
                            company: company_dept_wise.val(),
                            area: area_dept_wise.val(),
                            location: location_dept_wise.val()
                        }
                    },
                    cache: true
                }
            });

            $(company_dept_wise).on('change', function() {
                // //company_dept_wise.val('').trigger('change');
                // area_dept_wise.val('').trigger('change');
                // location_dept_wise.val('').trigger('change');
                // department_dept_wise.val('').trigger('change');
            });

            $('#btn-dept_wise').click( function (e) {
                let company = company_dept_wise.val();
                let area = area_dept_wise.val();
                let location = location_dept_wise.val();
                let dept = department_dept_wise.val();
                let date = $('#date_dept_wise').val();
                if(dept != '' && date != '' )
                {
                    $('#btn-dept_wise').html('<i class="fa fa-spinner fa-spin"></i> Loading');
                    $('#btn-dept_wise').attr('disabled', true);

                    fill_dept_wise_table(company, area, location, dept, date);
                }
            } );

            function fill_dept_wise_table(company, area, location, dept, date) {
                let f_in_date = date;

                //get department employee list
                $.ajax({
                    url: '{{url("get_dept_emp_list")}}',
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        company: company,
                        area: area,
                        location: location,
                        dept: dept,
                        date: date
                    },
                    dataType: 'json',
                    success: function(res) {
                        let tbody = $('#table_dept_wise tbody');
                        tbody.empty(); // Clear existing rows

                        var dataArray = res;

                        for (var i = 0; i < res.length; i++) {
                            var newRow = $("<tr>" +
                                                "<td>" + res[i].emp_id + "</td>" +
                                                "<td>" + res[i].emp_name_with_initial + "</td>" +
                                                "<td>"+
                                                    "<div class='input-group'>" +
                                                        "<input type='date' class='form-control form-control-sm date' value='" + f_in_date + "' name='in_date[]' />" +
                                                        "<input type='time' class='form-control form-control-sm in_time' name='in_time[]' />" +
                                                    "</div>" +
                                                    "<input type='hidden' value='" + res[i].emp_id + "' name='emp_id[]' />" +
                                                "</td>" +
                                                "<td>"+
                                                    "<div class='input-group'>" +
                                                        "<input type='date' class='form-control form-control-sm date' value='" + f_in_date + "' name='out_date[]' />" +
                                                        "<input type='time' class='form-control form-control-sm out_time' name='out_time[]' />" +
                                                    "</div>" +
                                                "</td>" +
                                        "</tr>"); 
                            tbody.append(newRow);
                        }

                    }
                });
            }

            $('#form_dept_wise').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-save_dept_wise");
                save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let formData = new FormData($('#form_dept_wise')[0]);
                let url_text = '{{ url("/attendance_add_dept_wise_submit") }}';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: url_text,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                         if (res.status == 1) {
                             const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: res.status,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            $('#form_dept_wise')[0].reset();
                            actionreload(actionJSON);

                        }else {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    }
                });
            });

            $('#create_record').click(function () {
                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                $('#AttendaddModal').modal('show');

            });
            $('#formModaladd #uid').change(function () {
                var id = $(this).val();
                // alert(id);
                $('#formModaladd #id').val(id);
            })

            $('#formAdd').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('Attendance.store') }}";
                }

                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('Attendance.update') }}";
                }

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (data) {
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        }
                    }
                });
            });

            $(document).on('click', '.edit',async function () {
                    var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                     var aid = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "/Attendance/" + aid + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#uid').val(data.result.uid);
                        $('#id').val(data.result.id);
                        $('#state').val(data.result.state);
                        $('#timestamp').val(data.result.timestamp);
                        $('#hidden_id').val(aid);
                        $('.modal-title').text('Add Attendance');
                        $('#action_button').val('Edit');
                        $('#action').val('Edit');
                        $('#formModaladd').modal('show');
                    }
                })
                }
            });

            var user_id;

            $(document).on('click', '.delete',async function () {
                 var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    user_id = $(this).attr('id');
                    $.ajax({
                    url: "Attendance/destroy/" + user_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                         const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                    }
                })
                }
            });



            $(document).on('click', '.getdata', function () {

                var device = $('#device').val();
                if (device != '') {
                    $('#getdataModal').modal('show');

                } else {
                    Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Select Location',
                        showConfirmButton: false,
                        timer: 2500
                        });
                }

            });

            $('#comfirm_button').click(function () {

                var device = $('#device').val();
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('Attendance.getdevicedata') }}",
                    method: "POST",
                    data: {
                        device: device,
                        _token: _token
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $('#comfirm_button').text('Procesing...');
                    },
                    success: function (data) {
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        }
                    }
                })
            });

            $(document).on('click', '.edit_button', async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    let id = $(this).data("uid");
                    date = $(this).attr('data-date');
                    emp_name_with_initial = $(this).attr('data-name');

                    var formdata = {
                        _token: $('input[name=_token]').val(),
                        id: id,
                        date: date
                    };
                    // alert(date);
                    $('#form_result').html('');
                    $.ajax({
                        url: "AttendentUpdate",
                        dataType: "json",
                        data: formdata,
                        success: function (data) {
                            $('#AttendeditModal').modal('show');
                            var htmlhead = '';
                            htmlhead += '<tr><td>Emp ID :' + id + '</td><td colspan="2">Name :' + emp_name_with_initial + '</td></tr>';
                            htmlhead += '<tr> <th> Type </th> <th>Date & Time</th><th class="text-right">Action</th>';
                            var html = '';

                            html += '<tr>';
                            html += '<td id="aduserid" colspan="3"><span style="display: none;">' + id + '</span></td>';
                            html += '</tr>';
                            for (var count = 0; count < data.length; count++) {
                                html += '<tr>';
                                const timestamp = new Date(data[count].timestamp);
                                const date = data[count].date;
                                const begining_checkout = data[count].begining_checkout;
                                const ending_checkin = data[count].ending_checkin;
                                const checkdate = date.slice(0, -8)

                                var checkbegining_checkout = checkdate + begining_checkout + ':00';
                                var checkending_checkin = checkdate + ending_checkin + ':00';

                                var setbegining_checkout = new Date(checkbegining_checkout).getTime();
                                var setcheckending_checkin = new Date(checkending_checkin).getTime();
                                var settimestamp = timestamp.getTime();

                                html += '<tr>';
                                if (settimestamp < setbegining_checkout) {
                                    html += '<td> Checkin</td>';
                                } else {
                                    html += '<td> Checkout</td>';
                                }

                                html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';
                                html += '<td class="text-right"><button type="button" class="btn btn-danger btn-sm addelete" id="' + data[count].id + '"><i class="far fa-trash-alt"></i></button></td></tr>';
                            }
                            $('#attendTable thead').html(htmlhead);
                            $('#attendTable tbody').html(html);
                        }
                    })
                }

            });

            $(document).on('click', '#add', function () {
                var _token = $('input[name="_token"]').val();
                var userid = $('#aduserid').text();
                var formdate = $('#formdate').val();
                var formtime = $('#formtime').val();
                var timestamp = formdate + ' ' + formtime;
                //alert(userid);
                if (formdate != '' && formtime != '') {
                    $.ajax({
                        url: "AttendentInsertLive",
                        method: "POST",
                        data: {
                            userid: userid,
                            timestamp: timestamp,
                            _token: _token
                        },
                        success: function (data) {
                            if (data.errors) {
                                        const actionObj = {
                                            icon: 'fas fa-warning',
                                            title: '',
                                            message: 'Record Error',
                                            url: '',
                                            target: '_blank',
                                            type: 'danger'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        action(actionJSON);
                                    }
                                    if (data.success) {
                                        const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message: data.success,
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        actionreload(actionJSON);
                                    }
                        }
                    });
                } else {
                    $('#message').html("<div class='alert alert-danger'>Please Select Date and Time</div>");
                }
            });

            $(document).on('blur', '.timestamp', function () {
                var _token = $('input[name="_token"]').val();

                var timestamp = $(this).text();
                var userid = $('#aduserid').text();
                var id = $(this).data("id");

                if (timestamp != '') {


                    $.ajax({
                        url: "AttendentUpdateLive",
                        method: "POST",
                        data: {
                            id: id,
                            userid: userid,
                            timestamp: timestamp,
                            _token: _token
                        },
                        success: function (data) {
                            if (data.errors) {
                                        const actionObj = {
                                            icon: 'fas fa-warning',
                                            title: '',
                                            message: 'Record Error',
                                            url: '',
                                            target: '_blank',
                                            type: 'danger'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        action(actionJSON);
                                    }
                                    if (data.success) {
                                        const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message: data.success,
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        actionreload(actionJSON);
                                    }
                        }
                    })
                }
            });

            $(document).on('click', '.addelete',async function () {
                var r = await Otherconfirmation("You want to Remove this ? ");
                if (r == true) {
                    var id = $(this).attr("id");
                    var _token = $('input[name="_token"]').val();
                    var $deleteButton = $(this);
                    $.ajax({
                        url: "AttendentDeleteLive",
                        method: "POST",
                        data: {
                            id: id,
                            _token: _token
                        },
                        success: function (data) {
                           const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: 'Record Remove Successfully',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);

                              reloadModalTableData();
                        }
                    });
                }

            });

            $(document).on('click', '.view_button', function () {
                let id = $(this).attr('data-uid');  
                let recorddate = $(this).attr('data-recorddate');
                emp_name_with_initial = $(this).attr('data-name');

             

                var formdata = {
                    _token: $('input[name=_token]').val(),
                    id: id,
                    date: recorddate
                };
                // alert(date);
                $('#form_result').html('');
                $.ajax({
                    url: "AttendentView",
                    dataType: "json",
                    data: formdata,
                    success: function (data) {
                        $('#AttendviewModal').modal('show');
                        var htmlhead = '';
                        htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td></tr>';
                        htmlhead += '<tr><th>TYPE</th><th>TIMESTAMPS</th></tr>';
                        var html = '';

                        if (data.length > 0) {
                            data.forEach(function(record, index) {
                                var type = '';
                                
                                if (index === 0) {
                                    type = 'Check In';
                                }
                                else if (index === data.length - 1) {
                                    type = 'Check Out';
                                }
                                else {
                                    type = 'Additional ' + index;
                                    
                                }

                                html += '<tr>';
                                html += '<td>' + type + '</td>';
                                html += '<td contenteditable class="timestamp" data-timestamp="' + record.timestamp + '" data-id="' + record.uid + '" data-attendance-id="' + record.id + '">';
                                html += record.timestamp;
                                html += '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="2">No attendance records found</td></tr>';
                        }
                        
                        $('#attendTable thead').html(htmlhead);
                        $('#attendTable tbody').html(html);
                    }
                });
            });

              $(document).on('click', '.delete_button', async function () {
                var r = await Otherconfirmation("You want to Remove this ? ");
                if (r == true) {
                    let id = $(this).data("uid");
                    date = $(this).attr('data-date');
                    emp_name_with_initial = $(this).attr('data-name');

                    
                $('#AttendeditModal')
                            .data('current-id', id)
                            .data('current-date', date)
                            .data('current-name', emp_name_with_initial);

                    var formdata = {
                        _token: $('input[name=_token]').val(),
                        id: id,
                        date: date
                    };
                    // alert(date);
                    $('#form_result').html('');
                    $.ajax({
                        url: "AttendentUpdate",
                        dataType: "json",
                        data: formdata,
                       success: function (data) {
                            $('#AttendeditModal').modal('show');
                            var htmlhead = '';
                            htmlhead += '<tr><td>Emp ID :' + id + '</td><td colspan="2">Name :' + emp_name_with_initial + '</td></tr>';
                            htmlhead += '<tr> <th> Type </th> <th>Date & Time</th><th class="text-right">Action</th>';
                            var html = '';

                            if (data.length > 0) {
                                data.forEach(function(record, index) {
                                    var type = '';
                                    
                                    if (index === 0) {
                                        type = 'Check In';
                                    }
                                    else if (index === data.length - 1) {
                                        type = 'Check Out';
                                    }
                                    else {
                                        type = 'Additional ' + index;
                                    }

                                    html += '<tr>';
                                    html += '<td>' + type + '</td>';
                                    html += '<td contenteditable class="timestamp" data-timestamp="' + record.timestamp + '" data-id="' + record.uid + '" data-attendance-id="' + record.id + '">' + record.timestamp + '</td>';
                                    html += '<td class="text-right"><button type="button" class="btn btn-danger btn-sm addelete" id="' + record.id + '"><i class="far fa-trash-alt"></i></button></td>';
                                    html += '</tr>';
                                });
                            } else {
                                html += '<tr><td colspan="3">No attendance records found</td></tr>';
                            }
                            
                            $('#attendTable thead').html(htmlhead);
                            $('#attendTable tbody').html(html);
                        }
                    })
                }

            });

            // $(document).on('click', '.delete_button',async function () {
            //      var r = await Otherconfirmation("You want to remove this ? ");
            //     if (r == true) {
            //         let uid = $(this).data("uid");
            //         let date = $(this).data("date");
            //         $.ajax({
            //             url: "{{ route('Attendance.delete') }}",
            //             method: "POST",
            //             data: {
            //                 _token: '{{ csrf_token() }}',
            //                 uid: uid,
            //                 date: date
            //             },
            //             success: function (data) {
            //                 const actionObj = {
            //                     icon: 'fas fa-trash-alt',
            //                     title: '',
            //                     message: 'Record Remove Successfully',
            //                     url: '',
            //                     target: '_blank',
            //                     type: 'danger'
            //                 };
            //                 const actionJSON = JSON.stringify(actionObj, null, 2);
            //                 actionreload(actionJSON);
            //             }
            //         });
            //     } 
            // });


            $('#create_record_upload').click(function () {
                $('#uploadAtModal').modal('show');
            });

            $('#formUpload').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-upload");
                let btn_prev_text = save_btn.html();

                //save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                let formData = new FormData($('#formUpload')[0]);
                let url_text = '{{ url("/attendance_upload_txt_submit") }}';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                $.ajax({
                    url: url_text,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                        if (res.status == 1) {
                           const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);

                        }else {
                           const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    }
                });
            });

            $('#csv_upload_record').click(function() {
                $('.modal-title').text('Upload Attendance Record');
                $('#action_button').html('Import');
                $('#action').val('Upload');
                $('#form_result1').html('');
                $('#formTitle1')[0].reset();

                $('#uploadModal').modal('show'); 
            });

            $('#formTitle1').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this); 
                var fileInput = $('#import_csv')[0].files[0];

                if (!fileInput || fileInput.type !== 'text/csv') {
                     Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Please upload a valid CSV file!',
                        showConfirmButton: false,
                        timer: 2500
                        });
                    return;
                }

                $.ajax({
                    url: "{{ route('importAttendance') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function (data) {
                        if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: 'Record Error',
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-save',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        }
                    }
                });
            });

        });

      // Function to reload modal table data
        function reloadModalTableData() {
            // Extract data from the current modal header
            var headerText = $('#attendTable thead tr:first').text();
            var idMatch = headerText.match(/Emp ID :(\w+)/);
            var nameMatch = headerText.match(/Name :([^]+?)(?=Type|$)/);
            
            var id = idMatch ? idMatch[1].trim() : '';
            var emp_name_with_initial = nameMatch ? nameMatch[1].trim() : '';
            
            // Get date from the first timestamp in the table or use stored data
            var firstTimestamp = $('#attendTable tbody tr:first .timestamp').data('timestamp');
            var date = firstTimestamp ? firstTimestamp.split(' ')[0] : '';
            
            // If we can't extract from table, try to get from stored data
            if (!id || !date) {
                id = $('#AttendeditModal').data('current-id');
                date = $('#AttendeditModal').data('current-date');
                emp_name_with_initial = $('#AttendeditModal').data('current-name');
            }

            if (id && date) {
                var formdata = {
                    _token: $('input[name=_token]').val(),
                    id: id,
                    date: date
                };

                $('#form_result').html('');
                $.ajax({
                    url: "AttendentUpdate",
                    dataType: "json",
                    data: formdata,
                    success: function (data) {
                        // Keep modal open and refresh the table
                        var htmlhead = '';
                        htmlhead += '<tr><td>Emp ID :' + id + '</td><td colspan="2">Name :' + emp_name_with_initial + '</td></tr>';
                        htmlhead += '<tr> <th> Type </th> <th>Date & Time</th><th class="text-right">Action</th>';
                        var html = '';

                        if (data.length > 0) {
                            data.forEach(function(record, index) {
                                var type = '';
                                
                                if (index === 0) {
                                    type = 'Check In';
                                }
                                else if (index === data.length - 1) {
                                    type = 'Check Out';
                                }
                                else {
                                    type = 'Additional ' + index;
                                }

                                html += '<tr>';
                                html += '<td>' + type + '</td>';
                                html += '<td contenteditable class="timestamp" data-timestamp="' + record.timestamp + '" data-id="' + record.uid + '" data-attendance-id="' + record.id + '">' + record.timestamp + '</td>';
                                html += '<td class="text-right"><button type="button" class="btn btn-danger btn-sm addelete" id="' + record.id + '"><i class="far fa-trash-alt"></i></button></td>';
                                html += '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="3">No attendance records found</td></tr>';
                        }
                        
                        $('#attendTable thead').html(htmlhead);
                        $('#attendTable tbody').html(html);
                    }
                });
            }
        }
    </script>

@endsection