@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-calendar-day"></i></div>
                    <span>Attendants Add</span>
                </h1>
            </div>
        </div>
    </div>      
    <div class="container-fluid mt-4">
        <div class="card mb-2">
            <div class="card-body">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Location</label>
                            <select name="location" id="location" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Employee</label>
                            <select name="employee" id="employee" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Date : From - To</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                </div>
                                <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter"> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-3 mr-2" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add - Single Date</button>
                        <button type="button" class="btn btn-outline-success btn-sm fa-pull-right px-3 mr-2" name="create_record_month" id="create_record_month"><i class="fas fa-plus mr-2"></i>Add - Month</button>
                        <button type="button" class="btn btn-outline-dark btn-sm fa-pull-right px-3 mr-2" name="create_record_dept_wise" id="create_record_dept_wise"><i class="fas fa-plus mr-2"></i>Add - Department Wise</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div id="response"></div>
                    </div>
                    <div class="col-md-12">
                        <div class=" text-center" id="empty_msg">
                            <div class="alert alert-info"> <span> Filter Company and Department to view attendance </span> </div>
                        </div>
                    </div>
                    <div class="col-12" id="attendtable_outer">
                        <table class="table table-striped table-bordered table-sm small" id="attendtable">
                            <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Location</th>
                                <th>Department</th>
                                <th class="text-right">Action</th>
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
    

    <!-- Modal Area Start -->
    <div class="modal fade" id="AttendaddModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Attendant</h5>
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
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Employee</label>
                                        <select name="employee" id="employee_single" class="form-control form-control-sm">
                                            <option value="">Select...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Attend Date</label>
                                        <input type="date" name="attdate" id="attdate" class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Attend Time</label>
                                        <input type="time" name="atttime" id="atttime" class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Off Time</label>
                                        <input type="time" name="offTime" id="offTime" class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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

    <div class="modal fade" id="monthAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Attendant - Month</h5>
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
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Employee</label>
                                            <select name="employee" id="employee_m" class="form-control form-control-sm">
                                                <option value="">Select...</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Month</label>
                                            <input type="month" id="month_m" name="month" class="form-control form-control-sm" min="2021-01" value="{{Date('Y-m')}}" />
                                        </div>
                                    </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered table-striped table-hover" id="table_month">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Day</th>
                                                <th>In Time</th>
                                                <th>Out Time</th>
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
                                    <button type="submit" name="action_button" id="btn-save" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DepartmentAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel1">Attendant - Department Wise</h5>
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
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Company <span class="text-danger">*</span> </label>
                                        <select name="company" id="company_dept_wise" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold text-dark">Department <span class="text-danger">*</span></label>
                                        <select name="department" id="department_dept_wise" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small font-weight-bold text-dark">Date <span class="text-danger">*</span></label>
                                        <input type="date" id="date_dept_wise" name="date" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small font-weight-bold text-dark">&nbsp; </label> <br>
                                        <button type="button" name="action_button" id="btn-dept_wise" class="btn btn-outline-success btn-sm fa-pull-right px-4"><i class="fas fa-search"></i>&nbsp;Find</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered table-striped table-hover" id="table_dept_wise">
                                        <thead>
                                        <tr>
                                            <th>Emp Id</th>
                                            <th>Employee</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
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
                                    <button type="submit" name="action_button" id="btn-save_dept_wise" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Attendant Update</h5>
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
    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="getdataModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Please check the devices connection and confirm?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="comfirm_button" id="comfirm_button" class="btn btn-danger px-3 btn-sm">Confirm</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->			 
</main>
              
@endsection

@section('script')

<script>
$(document).ready(function() {

    $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
    $('#attendance_collapse').addClass('show');
    $('#attendance_add_link').addClass('active');

    $('#attendtable_outer').css('display', 'none');

    let company = $('#company');
    let department = $('#department');
    let employee = $('#employee');
    let location = $('#location');

    company.select2({
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

    employee.select2({
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

    location.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("location_list_sel2")}}',
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
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    function load_dt(department, employee, location, from_date, to_date){
        $('#attendtable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{!! route('attendance_list_for_edit') !!}",
                "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
            },
            columns: [
                { data: 'uid', name: 'uid' },
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                { data: 'formatted_date', name: 'formatted_date' },
                { data: 'first_time_stamp', name: 'first_time_stamp' },
                { data: 'last_time_stamp', name: 'last_time_stamp' },
                { data: 'location', name: 'location' },
                { data: 'dep_name', name: 'dep_name' },
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "bDestroy": true,
            "order": [
                [3, "desc"]
            ]
        });
    }

    //load_dt('', '', '', '', '');

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);

        $('#empty_msg').css('display', 'none');
        $('#attendtable_outer').css('display', 'block');
    });

    $('#create_record_month').click(function () {
        $('#form_result').html('');
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

    function get_day(year, month ,date){
        let date_str = year+'-'+month+'-'+date;
        const days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        let day_no = new Date(date_str).getDay();
        return days[day_no];
    }

    function daysInMonth (month, year) {
        return new Date(year, month, 0).getDate();
    }

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



    $('#formMonth').on('submit',function(e) {
        e.preventDefault();
        let save_btn=$("#btn-save");
        save_btn.prop("disabled", true);
        save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
        let formData = new FormData($('#formMonth')[0]);
        let url_text = '{{ url("/attendance_add_bulk_submit") }}';
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
                    $('#bulk_response').html("<div class='alert alert-success'>"+res.msg+"</div>");
                    //save_btn.prop("disabled", false);
                    save_btn.html('Add' );
                    save_btn.prop("disabled", false);
                    if($.fn.DataTable.isDataTable('#table_month')){
                        $('#table_month').DataTable().clear().draw();
                    }
                    $("#formMonth")[0].reset();
                    $('#monthAtModal').modal('hide');
                    //$('#attendtable').DataTable().ajax.reload();
                    //if attendtable found on dom
                    if($.fn.DataTable.isDataTable('#attendtable')){
                        $('#attendtable').DataTable().ajax.reload();
                    }

                    //employee_m reset
                    $('#employee_m').val('').trigger('change');
                    $('#month_m').val('');


                }else {

                    var html = '';
                    if (res.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < res.errors.length; count++) {
                            html +=   res.errors[count]+'<br>' ;
                        }
                        html += '</div>';
                    }

                    $('#bulk_response').html(html);

                    save_btn.prop("disabled", false);
                    save_btn.html('Add' );
                }
            },
            error: function(res) {
                alert(data);
            }
        });
    });


    function fill_month_table(month_id){

        let month_n_y_arr = month_id.split('-');
        let num_of_days = daysInMonth(month_n_y_arr[1] ,month_n_y_arr[0]);

        let t = $('#table_month').DataTable({
            "pageLength":50,
            destroy: true,

        });
        t.clear();

        for(let i = 1; i <= num_of_days; i++)
        {
            let day = get_day(month_n_y_arr[0], month_n_y_arr[1] ,i);
            t.row.add([
                i,
                day,
                '<input type="time" class="form-control form-control-sm" name="in_time[]" /> ' +
                '<input type="hidden" value="'+i+'" name="date[]" />',
                '<input type="time" class="form-control form-control-sm" name="out_time[]" /> '
            ]).node().id = i;
            t.draw( false );
        }

    }

    $('#create_record_dept_wise').click(function () {
        $('#dept_wise_response').html('');
        $('#DepartmentAtModal').modal('show');
    });

    let company_dept_wise = $('#company_dept_wise');
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
                    company: company_dept_wise.val()
                }
            },
            cache: true
        }
    });

    $(company_dept_wise).on('change', function() {
        department_dept_wise.val('').trigger('change');
    });

    $('#btn-dept_wise').click( function (e) {
        let dept = department_dept_wise.val();
        let date = $('#date_dept_wise').val();
        if(dept != '' && date != '' )
        {
            fill_dept_wise_table(dept, date);
        }
    } );



    function fill_dept_wise_table(dept, date){

        //get department employee list
        $.ajax({
            url: '{{url("get_dept_emp_list")}}',
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                dept: dept,
                date: date
            },
            dataType: 'json',
            success: function(res) {
                let t = $('#table_dept_wise').DataTable({
                    "pageLength":50,
                });
                t.clear();

                for(let i = 0; i < res.length; i++)
                {
                    t.row.add([
                        res[i].emp_id,
                        res[i].emp_name_with_initial,
                        '<input type="time" class="form-control form-control-sm" name="in_time[]" /> ' +
                        '<input type="hidden" value="'+res[i].emp_id+'" name="emp_id[]" /> ',
                        '<input type="time" class="form-control form-control-sm" name="out_time[]" /> '
                    ]).node().id = res[i].emp_id;
                    t.draw( false );
                }
            },
            error: function(res) {
                alert(res);
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
                    $('#dept_wise_response').html("<div class='alert alert-success'>"+res.msg+"</div>");
                    //save_btn.prop("disabled", false);
                    save_btn.html('Add' );
                    save_btn.prop("disabled", false);
                    $('#table_dept_wise').DataTable().clear().draw();
                    $("#form_dept_wise")[0].reset();
                    $('#DepartmentAtModal').modal('hide');
                    $('#attendtable').DataTable().ajax.reload();
                }else {

                    var html = '';
                    if (res.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < res.errors.length; count++) {
                            html +=   res.errors[count]+'<br>' ;
                        }
                        html += '</div>';
                    }

                    $('#dept_wise_response').html(html);

                    save_btn.prop("disabled", false);
                    save_btn.html('Add' );
                }
            },
            error: function(res) {
                alert(data);
                save_btn.prop("disabled", false);
                save_btn.html('Add' );
            }
        });
    });



    $('#create_record').click(function () {
        $('.modal-title').text('Add New Attendance');
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

        alert(1)


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
                    $('#formAdd')[0].reset();
                    $('#attendtable').DataTable().ajax.reload();
                    setTimeout(function() { $('#AttendaddModal').modal('hide'); }, 1000);
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
        var aid = $(this).attr('id');
        // alert(aid);
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
                $('.modal-title').text('Edit Attendant');
                $('#action_button').val('Edit');
                $('#action').val('Edit');
                $('#formModaladd').modal('show');
            }
        })
    });

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "Attendance/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload();
            }
        })
    });

    $(document).on('click', '.getdata', function () {

        var device = $('#device').val();
        if (device != '') {
            $('#getdataModal').modal('show');


        } else {
            alert('Select Location');
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
                setTimeout(function () {
                    $('#confirmModal').modal('hide');

                }, 100);
                location.reload();
            }
        })
    });

    $(document).on('click', '.edit_button', function () {
        id = $(this).attr('uid');
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
                $('.modal-title').text('Edit Attendent');
                $('#AttendviewModal').modal('show');
                var htmlhead = '';
                htmlhead += '<tr><td>Emp ID :' + id + '</td><td colspan="2">Name :' + emp_name_with_initial + '</td></tr>';
                htmlhead += '<tr> <th>Timestamp</th><th class="text-right">Action</th>';
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
                    }
                    if (settimestamp > setbegining_checkout) {
                        html += '<td> Checkout</td>';
                    }
                    html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';
                    html += '<td class="text-right"><button type="button" class="btn btn-outline-danger btn-sm addelete" id="' + data[count].id + '"><i class="far fa-trash-alt"></i></button></td></tr>';
                }
                $('#attendTable thead').html(htmlhead);
                $('#attendTable tbody').html(html);
            }
        })
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
                    $('#message').html(data);
                    $('#AttendviewModal').modal('hide');
                    location.reload();
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
                    $('#message').html(data);
                    $('#AttendviewModal').modal('hide');
                    /// location.reload();
                }
            })
        } else {
            $('#message').html("<div class='alert alert-danger'>Enter some value</div>");
        }
    });

    $(document).on('click', '.addelete', function () {
        var id = $(this).attr("id");
        var _token = $('input[name="_token"]').val();

        if (confirm("Are you sure you want to delete this records?")) {
            $.ajax({
                url: "AttendentDeleteLive",
                method: "POST",
                data: {
                    id: id,
                    _token: _token
                },
                success: function (data) {
                    let html = '<div class="alert alert-success">' + data + '</div>';
                    $('#response').html(html)
                    $('#attendtable').DataTable().ajax.reload();
                    $('#AttendviewModal').modal('hide');
                }
            });
        }
    });

    $(document).on('click', '.view_button', function () {
        id = $(this).attr('uid');
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
            url: "AttendentView",
            dataType: "json",
            data: formdata,
            success: function (data) {
                $('#AttendviewModal').modal('show');
                $('.modal-title').text('View Attendent');
                var htmlhead = '';
                htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td></tr>';
                htmlhead += '<tr> <th>Timestamp</th>';
                var html = '';
                html += '<tr>';


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
                    }
                    if (settimestamp > setbegining_checkout) {
                        html += '<td> Checkout</td>';
                    }


                    html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';

                }
                $('#attendTable thead').html(htmlhead);
                $('#attendTable tbody').html(html);
            }
        });
    });

    $(document).on('click', '.delete_button', function () {
        let uid = $(this).data("uid");
        let date = $(this).data("date");

        if (confirm("Are you sure you want to delete this?")) {
            $.ajax({
                url: "{{ route('Attendance.delete') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    uid: uid,
                    date: date
                },
                success: function (data) {
                    $('#attendtable').DataTable().ajax.reload(null, false);
                    $('#response').html('<div class="alert alert-success">' + data.msg + '</div>');
                }
            });
        }
    });

 });
</script>

@endsection