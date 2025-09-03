@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="far fa-calendar-check"></i></div>
                        <span>Attendance Edit</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm" required>
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm" required>
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <input type="date" id="filter_date" name="date" class="form-control form-control-sm" placeholder="yyyy-mm-dd" required>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_main" class="form-control form-control-sm">
                                </select>
                            </div>

                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-outline-success btn-sm fa-pull-right px-3 mr-2" name="edit_record_month" id="edit_record_month"><i class="fas fa-pencil-alt mr-2"></i>Edit - Month</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <div class="message"></div>
                            <table class="table table-striped table-bordered table-sm small" id="attendtable">
                                <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Work Month</th>
                                    <th>Department</th>
                                    <th>Company</th>
                                    <th>Check In Time</th>
                                    <th>Check Out Time</th>
                                </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>

                            <button id="approve_att" class="btn btn-primary btn-sm float-right mt-2"> Update</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

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
                                    <button type="submit" name="action_button" id="btn-save" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-pencil-alt"></i>&nbsp;Update </button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')

    <script>
        $(document).ready(function () {

            $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#attendance_collapse').addClass('show');
            $('#attendance_edit_link').addClass('active');

            let changed_records = [];

            let company = $('#company');
            let department = $('#department');

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

            $('#employee_main').select2({
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

            load_dt('');
            function load_dt(company,department, date, employee) {
                $('#attendtable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{url('/attendance_list_for_bulk_edit')}}",
                        "data": {'company':company, 'department':department, 'date':date, 'employee':employee},
                    },
                    columns: [
                        { data: 'uid' },
                        { data: 'emp_name_with_initial' },
                        { data: 'month' },
                        { data: 'dept_name' },
                        { data: 'location' },
                        { data: 'firsttimestamp' ,
                            render : function ( data, type, row, meta ) {
                                return type === 'display'  ?
                                    '<input type="datetime-local" class="form-control form-control-sm time" ' +
                                    'data-id="'+row['id']+'" ' +
                                    'data-uid="'+row['uid']+'" ' +
                                    'data-date="'+row['date']+'" ' +
                                    'data-dept_id="'+row['dept_id']+'" ' +
                                    'data-time_type="first_time" ' +
                                    'data-timestamp="'+row['firsttimestamp']+'" ' +
                                    ' value="'+row['formatted_first_timestamp']+'" ' +
                                    '/>'
                                    : data;
                            }},
                        { data: 'lasttimestamp' ,
                            render : function ( data, type, row, meta ) {

                                if(row['lasttimestamp'] != row['firsttimestamp']){

                                    return type === 'display'  ?
                                        '<input type="datetime-local" class="form-control form-control-sm time" ' +
                                        'data-id="'+row['id']+'" ' +
                                        'data-uid="'+row['uid']+'" ' +
                                        'data-date="'+row['date']+'" ' +
                                        'data-dept_id="'+row['dept_id']+'" ' +
                                        'data-time_type="last_time" ' +
                                        'data-timestamp="'+row['lasttimestamp']+'" ' +
                                        ' value="'+row['formatted_last_timestamp']+'" ' +
                                        '/>'
                                        : data;
                                }else {
                                    return type === 'display'  ?
                                        '<input type="datetime-local" class="form-control form-control-sm time" ' +
                                        'data-id="'+row['id']+'" ' +
                                        'data-uid="'+row['uid']+'" ' +
                                        'data-date="'+row['date']+'" ' +
                                        'data-dept_id="'+row['dept_id']+'" ' +
                                        'data-time_type="last_time" ' +
                                        'data-timestamp="" ' +
                                        ' value="" ' +
                                        '/>'
                                        : data;
                                }
                            }
                        },
                    ],
                    "bDestroy": true,
                    "order": [[ 6, "desc" ]],

                    "drawCallback": function( settings ) {
                        check_changed_text_boxes();

                    }
                });
            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let company = $('#company').val();
                let date = $('#filter_date').val();
                let employee = $('#employee_main').val();

                load_dt(company, department, date, employee);
            });

            // $("table").on('keyup', "input[type=text]", function () {
            //     $(this).parent().parent().css('background-color', '#f7c8c8');
            // });

            $(document).delegate("table tbody tr input[type=datetime-local]","change",function(e){
                $(this).parent().parent().css('background-color', '#f7c8c8');
                $(this).parent().parent().addClass('changed');

                let time_stamp = e.target.value;

                let b = {};
                b["time_stamp"] = time_stamp;
                b["existing_time_stamp"] = $(this).data('timestamp');
                b["time_type"] = $(this).data('time_type');
                b["id"] = $(this).data('id');
                b["uid"] = $(this).data('uid');
                b["date"] = $(this).data('date');
                b["dept_id"] = $(this).data('dept_id');

                //changed_records.push(b);
                //check if the record is already in the array
                let found = false;
                for(let i=0; i<changed_records.length; i++){
                    if(changed_records[i]['id'] == b['id']){
                        found = true;
                        break;
                    }
                }

                if(!found){
                    changed_records.push(b);
                }else{
                    //update the time stamp
                    for(let i=0; i<changed_records.length; i++){
                        if(changed_records[i]['id'] == b['id']){
                            changed_records[i]['time_stamp'] = b['time_stamp'];
                            break;
                        }
                    }
                }

            });

            $("table").on('focusout', "input[type=text]", function () {
                $(this).parent().parent().css('background-color', '#f7c8c8');
                $(this).parent().parent().addClass('changed');

                let b = {};
                b["time_stamp"] = $(this).val();
                b["existing_time_stamp"] = $(this).data('timestamp');
                b["time_type"] = $(this).data('time_type');
                b["id"] = $(this).data('id');
                b["uid"] = $(this).data('uid');
                b["date"] = $(this).data('date');
                b["dept_id"] = $(this).data('dept_id');

                //changed_records.push(b);
                //check if the record is already in the array
                let found = false;
                for(let i=0; i<changed_records.length; i++){
                    if(changed_records[i]['id'] == b['id']){
                        found = true;
                        break;
                    }
                }

                if(!found){
                    changed_records.push(b);
                }else{
                    //update the time stamp
                    for(let i=0; i<changed_records.length; i++){
                        if(changed_records[i]['id'] == b['id']){
                            changed_records[i]['time_stamp'] = b['time_stamp'];
                            break;
                        }
                    }
                }

            });

            $(document).on('click', '#approve_att', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "AttendanceEditBulkSubmit",
                    method: "POST",
                    data: {
                        'changed_records': changed_records,
                        _token: $('input[name=_token]').val(),
                    },
                    success: function (data) {
                        //$('.message').html("<div class='alert alert-success'>"+data.msg+"</div>");
                        //$('#attendtable').DataTable().ajax.reload();
                        changed_records = [];
                    }
                });

            });

            function check_changed_text_boxes(){

                for(let a = 0; a < changed_records.length; a++){
                    let time_stamp = changed_records[a]['time_stamp'];
                    let id = changed_records[a]['id'];
                    let uid = changed_records[a]['uid'];
                    let date = changed_records[a]['date'];
                    let time_type = changed_records[a]['time_type'];
                    let dept_id = changed_records[a]['dept_id'];

                    let selector = $('.time[data-id="' + id + '"][data-uid="'+ uid +'"][data-date="'+ date +'"][data-time_type="'+ time_type +'"][data-dept_id="'+ dept_id +'"]');

                    selector.val(time_stamp);
                    selector.parent().parent().css('background-color', '#f7c8c8');
                    selector.parent().parent().addClass('changed');
                }
            }

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
                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);

                            let month_n_y_arr = month_id.split('-');
                            let num_of_days = daysInMonth(month_n_y_arr[1] ,month_n_y_arr[0]);

                            let t = $('#table_month').DataTable({
                                "pageLength": 50,
                                "bDestroy": true,
                            });
                            t.clear();

                            //console.log('______________')

                            for(let i = 1; i <= num_of_days; i++)
                            {
                                let day = get_day(month_n_y_arr[0], month_n_y_arr[1] ,i);
                                //console.log( month_n_y_arr[0]+' : '+ month_n_y_arr[1]+ ' : ' + i + ' : ' +day);
                                t.row.add([
                                    i,
                                    day,
                                    '<input type="datetime-local" class="form-control form-control-sm" id="in_'+i+'" name="in_time[]" /> ' +
                                    '<input type="hidden" value="'+i+'" name="date[]" />' +
                                    '<input type="hidden" value="" id="uid_'+i+'" name="uid[]" />' +
                                    '<input type="hidden" value="" id="emp_id_'+i+'" name="emp_id[]" />' +
                                    '<input type="hidden" value="'+month_id+'-'+i+'" id="date_'+i+'" name="date_e[]" />' +
                                    '<input type="hidden" value="" id="existing_time_stamp_in_'+i+'" name="existing_time_stamp_in[]" />' +
                                    '<input type="hidden" value="" id="existing_time_stamp_out_'+i+'" name="existing_time_stamp_out[]" />'+
                                    '<input type="hidden" value="" id="existing_time_stamp_in_rfc_'+i+'" name="existing_time_stamp_in_rfc[]" />'+
                                    '<input type="hidden" value="" id="existing_time_stamp_out_rfc_'+i+'" name="existing_time_stamp_out_rfc[]" />',
                                    '<input type="datetime-local" class="form-control form-control-sm" id="out_'+i+'" name="out_time[]" /> '
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
                                    in_selector.val(value.firsttime_rfc);
                                    existing_time_stamp_in_selector.val(value.firsttimestamp);
                                    existing_time_stamp_in_rfc_selector.val(value.firsttime_rfc);
                                }

                                if(value.lasttimestamp != ''){
                                    out_selector.val(value.lasttime_rfc);
                                    existing_time_stamp_out_selector.val(value.lasttimestamp);
                                    existing_time_stamp_out_rfc_selector.val(value.lasttime_rfc);
                                }

                            });

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
                            save_btn.html(btn_prev_text);
                        }
                    },
                    error: function(res) {
                        alert(data);
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
                let save_btn=$("#btn-save");
                let btn_prev_text = save_btn.html();
                //save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
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
                            $('#bulk_response').html("<div class='alert alert-success'>"+res.msg+"</div>");

                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);
                            //$('#table_month').DataTable().clear().draw();
                           // $("#formMonth")[0].reset();
                            //$('#employee_m').val('').trigger('change');
                            $('#attendtable').DataTable().ajax.reload();
                            //scroll monthAtModal
                            $('#monthAtModal').scrollTop(0);


                            //wait 2 seconds
                            setTimeout(function(){
                                $('#monthAtModal').modal('hide');
                            }, 2000);

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
                            save_btn.html(btn_prev_text);
                        }
                    },
                    error: function(res) {
                        alert(data);
                    }
                });
            });


        });




    </script>

@endsection