<?php $page_stitle = 'Report on Late Attendance - Multi Offset HRM'; ?>
@extends('layouts.app')

@section('content')

<main>
     <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Late Attendance Report</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body mt-2 p-0 p-2">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                class="fas fa-filter mr-1"></i> Filter
                            Records</button><br><br>
                    </div>

                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-sm small" id="attendtable">
                            <thead>
                                <tr>
                                    <th>EMP ID</th> 
                                    <th>NAME</th>   
                                    <th>DEPARTMENT</th>
                                    <th>DATE</th>
                                    <th>CHECK IN</th>
                                    <th>CHECK OUT</th>   
                                    <th>STATUS</th>
                                </tr>
                            </thead>                            
                            <tbody>
                            </tbody>
                        </table>
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
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                             </div>
                         </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                             </div>
                         </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                             </div>
                         </li>
                         <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date* </label>
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd"  required>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date*</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd" required>
                                </div>
                            </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Status</label>
                                <select name="latestatus" id="latestatus" class="form-control form-control-sm">
                                    <option value="">Select Late Status</option>
                                    <option value="1">Late Coming</option>
                                    <option value="2">Early Going</option>
                                </select>
                             </div>
                         </li>
                         <li>
                             <div class="col-md-12 d-flex justify-content-between">
                                 <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                     <i class="fas fa-redo mr-1"></i> Reset
                                 </button>
                                  <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                                     <i class="fas fa-search mr-2"></i>Search
                                 </button>
                             </div>
                         </li>
                     </form>
                 </ul>
             </div>
         </div>


    </div>
</main>
              
@endsection

@section('script')

<script>


$(document).ready(function () {

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeereportmaster').addClass('navbtnactive');

    let company = $('#company');
    let department = $('#department');
    let employee = $('#employee');

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
                    page: params.page || 1,
                    company: company.val(),
                    department: department.val()
                }
            },
            cache: true
        }
    });

    load_dt('','','','','');
    function load_dt(department,from_date,to_date,employee,latestatus){
        $('#attendtable').DataTable({
             "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Late Attendance Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Late Attendance Reports', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Late Attendance Reports',
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
                "url": "{{url('/late_attendance_report_list')}}",
                "data": {'department':department,
                         'fromdate':from_date,
                         'to_date':to_date,
                        'employee':employee,
                        'latestatus':latestatus },
            },
            columns: [
                { data: 'uid' },
                { 
                    data: 'employee_display',
                    name: 'employee_display' 
                },
                { data: 'dept_name' },
                { data: 'date' },
                { data: 'check_in_time'},
                { data: 'check_out_time'},
                { data: 'status'}
            ],
            "bDestroy": true,
            "order": [[ 3, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let employee = $('#employee').val();
        let latestatus = $('#latestatus').val();

        load_dt(department,from_date,to_date,employee,latestatus);
        closeOffcanvasSmoothly();

    });


});
$(document).ready(function () {


    var date = new Date();

    // $('#formModaladd #timestamp').datepicker({
    //     todayBtn: 'linked',
    //     format: 'yyyy-mm-dd',
    //     autoclose: true
    // });
    //
    // $('#formModal #adtimestamp').datepicker({
    //     todayBtn: 'linked',
    //     format: 'yyyy-mm-dd',
    //     autoclose: true
    // });

    $('#create_record').click(function () {
        $('.modal-title').text('Add New Attendance');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModaladd').modal('show');
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
                    // $('#titletable').DataTable().ajax.reload();
                    location.reload();
                }
                $('#form_result1').html(html);
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
                $('.modal-title').text('Edit Attendent');
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
            },

            error: function (data) {
                $('#message').html(data);
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
                htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td></tr>';
                htmlhead += '<tr><th>User id</th><th>Timestamp</th><th>Action</th>';
                var html = '';

                html += '<tr>';
                html += '<td id="aduserid">' + id + '</td>';
                html += '<td contenteditable> <input type="datetime-local" id="adtimestamp" name="adtimestamp" placeholder="YYYY-MM-DD - HH:ii p" ></td>';
                html += '<td><button type="button" class="btn btn-success btn-xs" id="add">Add</button></td></tr>';
                for (var count = 0; count < data.length; count++) {
                    html += '<tr>';
                    html += '<td  >' + data[count].uid + '</td>';
                    html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';
                    html += '<td><button type="button" class="btn btn-danger btn-xs addelete" id="' + data[count].id + '">Delete</button></td></tr>';
                }
                $('#attendTable thead').html(htmlhead);
                $('#attendTable tbody').html(html);
            }
        })
    });

    $(document).on('click', '#add', function () {
        var _token = $('input[name="_token"]').val();
        var userid = $('#aduserid').text();
        var timestamp = $('#adtimestamp').val();
        //alert(userid);
        if (userid != '' && timestamp != '') {
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
            $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
        }
    });

    $(document).on('blur', '.timestamp', function () {
        var _token = $('input[name="_token"]').val();
        var timestamp = $(this).data("timestamp");
        var timestamp = $(this).text();
        var id = $(this).data("id");

        if (timestamp != '') {


            $.ajax({
                url: "AttendentUpdateLive",
                method: "POST",
                data: {
                    id: id,
                    timestamp: timestamp,
                    _token: _token
                },
                success: function (data) {
                    $('#message').html(data);
                    $('#AttendviewModal').modal('hide');
                    location.reload();
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
                    $('#message').html(data);
                    location.reload()
                }
            });
        }
    });

});

$(document).on('click', '.view_button', function () {
    id = $(this).attr('data-uid');
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
        url: "LateAttendentView",
        dataType: "json",
        data: formdata,
        success: function (data) {
            $('#AttendviewModal').modal('show');
            $('.modal-title').text('View Attendent');
            var htmlhead = '';
            htmlhead += '<tr><td>Emp ID :' + id + '</td><td colspan="2" >Name :' + emp_name_with_initial + '</td></tr>';
            htmlhead += '<tr><th>TimeStamp</th><th>OnDuty Time</th><th>OffDuty Time</th>';
            var html = '';
            html += '<tr>';


            for (var count = 0; count < data.length; count++) {
                html += '<tr>';
                html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';
                html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].onduty_time + '</td>';
                html += '<td contenteditable class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].offduty_time + '</td>';

            }
            $('#attendTable thead').html(htmlhead);
            $('#attendTable tbody').html(html);
        }
    })
});
</script>

@endsection