@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="far fa-calendar-times"></i></div>
                    <span>Attendance Sync</span>
                </h1>
            </div>
        </div>
    </div>      
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <form class="form" method="POST">
                            {{ csrf_field() }}
                            <div class="form-row mb-1">
                                <div class="col-2">
                                    <select name="device" id="device" class="form-control form-control-sm" required>
                                        <option value="">Location</option>
                                        @foreach($device as $devices)
                                        <option data-fname="{{$devices->name}}" value="{{$devices->ip}}">{{$devices->name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <button type="button" name="getdata" id="getdata" class="btn btn-outline-primary btn-sm getdata px-3"><i class="fas fa-search mr-2"></i>Getdata</button>
                                </div>                                
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                        <h3 class="mt-2">Incomplete Data</h3>
                        <div id="msg"></div>
                    </div>

                    <div class="col-sm-12">
                        <form class="form-horizontal" id="formFilter">
                            <div class="form-row mb-1">
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Company</label>
                                    <select name="company" id="company" class="form-control form-control-sm">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small font-weight-bold text-dark">Department</label>
                                    <select name="department" id="department" class="form-control form-control-sm">
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

                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="attendtable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee ID</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Location</th>
                                    <th>Department</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Attendent Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="message"></div>
                            <table id='attendTable' class="table table-bordered table-hover" width="100%"
                                cellspacing="0">
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
                            <h4 class="font-weight-normal">If you need to download data, please confirm?</h4>
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
    $('#attendance_sync_link').addClass('active');

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
            url: '{{url("employee_list_from_attendance_sel2")}}',
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

    function load_dt(department, employee, location, from_date, to_date){
        $('#attendtable').DataTable({
            "columnDefs": [ {
                "targets": -1,
                "orderable": false
            } ],
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        // columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Print',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        //columns: 'th:not(:last-child)'
                    }
                }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/attendance_list_ajax')}}",
                "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
            },
            columns: [
                { data: 'at_id' },
                { data: 'uid' },
                { data: 'date'},
                { data: 'emp_name_with_initial' },
                { data: 'firsttimestamp' ,
                    render : function ( data, type, row, meta ) {
                        if(row['btn_in']){
                            return type === 'display'  ?
                                ' <button class="btn btn-outline-default btn-sm edit_button text-primary" ' +
                                'uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="in time" ' +
                                'data-name="'+row['emp_name_with_initial'] +'" '  +
                                '>In Time</button> '
                                : data;
                        }else{
                            return type === 'display'  ?
                                row['firsttimestamp']:data;
                        }
                    }
                },
                { data: 'lasttimestamp' ,
                    render : function ( data, type, row, meta ) {
                        if(row['btn_out']){
                            return type === 'display'  ?
                                ' <button class="btn btn-outline-default btn-sm edit_button text-primary" ' +
                                'uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="out time" ' +
                                'data-name="'+row['emp_name_with_initial'] +'" '  +
                                '>Out Time</button> '
                                : data;
                        }else{
                            return type === 'display'  ?
                                row['lasttimestamp']:data;
                        }
                    }
                },
                { data: 'location' },
                { data: 'dep_name' },
                { data: 'uid',
                    render : function ( data, type, row, meta ) {
                        if(row['btn_delete']){
                            return type === 'display'  ?
                                ' <button class="btn btn-outline-danger btn-sm delete_button" ' +
                                'data-uid="'+row['uid'] +'" ' +
                                'data-date="'+row['date_row'] +'" ' +
                                'data-type="delete" ' +
                                '> <i class="fa fa-trash"> </i> </button> '
                                : data;
                        }else{
                            return '';
                        }
                    }
                }
            ],
            "bDestroy": true,
            "order": [
                [2, "desc"]
            ]
        });
    }

    load_dt('', '', '', '', '');

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
    });


    $("#form_datetime").datetimepicker({
        format: "yyyy-mm-dd hh:ii"
    });

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
                $('.modal-title').text('Add Attendent');
                $('#AttendviewModal').modal('show');
                var htmlhead = '';
                htmlhead += '<tr><td>Emp ID :' + id + '</td><td >Name :' + emp_name_with_initial + '</td><td></td></tr>';
                htmlhead += '<tr><th></th><th>Timestamp</th><th>Action</th>';
                var html = '';

                html += '<tr>';
                html += '<td id="aduserid"> <span style="display: none;">' + id + '</span></td>';
                html += '<td ><input size="16" id="formdate" type="date" ><input size="16" id="formtime" type="time" ></td>';
                html += '<td><button type="button" class="btn btn-success btn-xs" id="add">Add</button></td></tr>';
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



                    html += '<td  class="timestamp" data-timestamp="timestamp" data-id="' + data[count].id + '">' + data[count].timestamp + '</td>';

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

    $(document).on('click', '.getdata', function () {

        var device = $('#device').val();
        if (device != '') {
            $('#getdataModal').modal('show');


        } else {
            alert('Select Location');
        }

    });

    $('#comfirm_button').click(function () {

        let btn = $(this);
        btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Processing...');

        var device = $('#device').val();

        $.ajax({
            url: "{{ route('Attendance.getdevicedata') }}",
            type: 'POST',
            data: {
                device: device,
                _token: '{{csrf_token()}}',
            },
            success: function(res) {
                var html = '';
                if (res.errors) {
                    html = '<div class="alert alert-danger">Error Occurred</div>';
                    $('#comfirm_users').text('confirm');
                }
                if (res.status) {
                    html = '<div class="alert alert-success">Success</div>';
                    load_dt('', '', '', '', '');
                    $('#getdataModal').modal('hide');
                    btn.html('Confirm');
                }
                $('#confirm_result').html(html);
            },
            error: function(data) {
                alert(data);
            }
        });

    });

    //documents .delete_button click event
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
                   $('#msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                }
            });
        }
    });

});
</script>

@endsection