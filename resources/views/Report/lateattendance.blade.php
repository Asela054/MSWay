<?php $page_stitle = 'Report on Late Attendance - Multi Offset HRM'; ?>
@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.reports_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card mb-2">
            <div class="card-body">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                        </div>
                        <div class="col">
                            <label class="small font-weight-bold text-dark">Date Range: From - To</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                       placeholder="yyyy-mm-dd">
                                <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-row mb-1">
                        <div class="col-3">
                                <label class="small font-weight-bold text-dark">Status</label>
                                <select name="latestatus" id="latestatus" class="form-control form-control-sm">
                                    <option value="">Select Late Status</option>
                                    <option value="1">Late Coming</option>
                                    <option value="2">Early Going</option>
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
                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="attendtable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th> 
                                    <th>Name</th>   
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>   
                                    <th>Status</th>
                                    <th>Action</th>
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
                                <tbody>
                                </tbody>
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
    <!-- Modal Area End -->
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
            "columnDefs": [
                {
                    "targets": -1,
                    "orderable": false
                },
                {
                    "targets": -2,
                    "orderable": false
                },
                {
                    "targets": -3,
                    "orderable": false
                },
                {
                    "targets": -4,
                    "orderable": false
                },
                {
                    "targets": -5,
                    "orderable": false
                },
                {
                    "targets": -6,
                    "orderable": false
                },
            ],
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
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
                { data: 'status', title: 'Status' },
                { data: 'uid' ,
                    render : function ( data, type, row, meta ) {

                        return type === 'display'  ?
                            ' <button class="view_button btn btn-outline-dark btn-sm" name="view_button" ' +
                            'data-uid="'+row['uid'] +'" ' +
                            'data-date="'+row['date'] +'" ' +
                            'data-name="'+row['emp_name_with_initial'] +'" ' +
                            '><i class="fas fa-eye"></i></button> ' +
                            ' '
                            : data;
                    }},
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
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