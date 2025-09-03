@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar"></i></div>
                        <span>Attendance Monthly Summary</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-4">

            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                    <div class="msg"></div>
                    <div class="row">

                        <div class="col-12 mb-1" id="working_wd_table">

                            <h5>Working Work days breakdown</h5>
                            <label class="mr-4">
                                <badge class="badge badge-pill " style="background-color: #DEF7ED"> &nbsp;</badge>
                                : Full Days
                            </label>
                            <label class="mr-4">
                                <badge class="badge badge-pill " style="background-color: #FDE5D0"> &nbsp;</badge>
                                : Half Days
                            </label>
                            <label class="mr-4">
                                <badge class="badge badge-pill " style="background-color: #EEB7B8"> &nbsp;</badge>
                                : Leave Days
                            </label>
                            <div class="table-responsive table-bordered">
                                <table class="table table-striped" id="working_tbl">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Working Hours</th>
                                        <th>No of Days</th>
                                        <th>Comment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <button type="button" id="work_day_save_btn" class="btn btn-success btn-sm float-right mb-2 mt-2 mr-2">Save Changes</button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="msg"></div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Employee ID</label>
                                    <input type="text" id="emp_id" value="{{$employee[0]->emp_id}}"
                                           class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">EPF No</label>
                                    <input type="text" id="emp_etfno" value="{{$employee[0]->emp_etfno}}"
                                           class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Employee Name</label>
                                    <input type="text" id="emp_name_with_initial"
                                           value="{{$employee[0]->emp_name_with_initial}} "
                                           class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Working Work Days</label>
                                    <input type="text" id="working_week_days"
                                           value=""
                                           class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">All Work Days</label>
                                    <input type="text" id="workdays" value="" class="form-control form-control-sm"
                                           disabled>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Leave Days</label>
                                    <input type="text" id="leavedate" value="" class="form-control form-control-sm"
                                           disabled>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Nopay Days</label>
                                    <input type="text" id="nopay" value="" class="form-control form-control-sm"
                                           disabled>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Normal OT Hours</label>
                                    <input type="text" id="ot" class="form-control form-control-sm" disabled>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Double OT Hours</label>
                                    <input type="text" id="dot" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Work Month</label>
                                    <input type="month" id="month" name="month" class="form-control form-control-sm"
                                           placeholder="yyyy-mm"
                                           value="{{$month}}"
                                           required
                                    >
                                </div>
                                <div class="col">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-primary mt-2 float-right"
                                            id="btn_search">Search
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="col-6">
                            <h5>Attendances</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small"
                                       id="attendtable">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Work Hours</th>
                                        <th>OT Time</th>
                                        <th>D/OT Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-3"
                                            id="approvel">Approvel
                                    </button>
                                </div>
                            </div>
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
                                <table id='attendTable' class="table table-striped table-bordered table-sm small">
                                    <thead>
                                    </tr>
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
                        <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK
                        </button>
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
                                <h4 class="font-weight-normal">Please check the devices connection and comfirm?</h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" name="comfirm_button" id="comfirm_button"
                                class="btn btn-danger px-3 btn-sm">Confirm
                        </button>
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
            var otcount = $('#otcount').text();
            var dotcount = $('#dotcount').text();
            $('#ot').val(otcount);
            $('#dot').val(dotcount);

            $(document).on('click', '#approvel', function () {
                var _token = $('input[name="_token"]').val();
                var emp_id = $('#emp_id').val();
                var emp_etfno = $('#emp_etfno').val();
                var emp_name_with_initial = $('#emp_name_with_initial').val();
                var month = $('#month').val();
                var workdays = $('#workdays').val();
                var working_week_days = $('#working_week_days').val();
                var leavedate = $('#leavedate').val();
                var nopay = $('#nopay').val();

                var not = $('#ot').val();
                var ndot = $('#dot').val();
                var ot = not.replace(':', '.');
                var dot = ndot.replace(':', '.');

                let emp_auto_id = '{{$employee[0]->id}}';

                if (emp_id != '') {
                    $.ajax({
                        //url: "AttendentAprovel",
                        url: "{{ route('AttendentAprovel') }}",
                        method: "POST",
                        data: {
                            emp_id: emp_id,
                            emp_etfno: emp_etfno,
                            emp_name_with_initial: emp_name_with_initial,
                            month: month,
                            workdays: workdays,
                            working_week_days: working_week_days,
                            leavedate: leavedate,
                            nopay: nopay,
                            ot: ot,
                            dot: dot,
                            emp_auto_id: emp_auto_id,
                            _token: _token
                        },
                        success: function (data) {
                            $('.msg').html('<div class="alert alert-success">' + data.msg + '</div>');
                        }
                    });
                }

            });

            //btn_search click
            $(document).on('click', '#btn_search', function () {

                get_attendances();

            });

            get_attendances();

            function get_attendances() {

                var _token = $('input[name="_token"]').val();
                var emp_id = $('#emp_id').val();
                var month = $('#month').val();

                if (emp_id != '' && month != '') {
                    $.ajax({
                        url: "{{ route('get_attendance_monthly_summery_by_emp_id') }}",
                        method: "POST",
                        dataType: "json",
                        data: {
                            emp_id: emp_id,
                            month: month,
                            _token: _token
                        },
                        success: function (res) {
                            $('#working_week_days').val(res.working_week_days_confirmed);
                            $('#workdays').val(res.work_days);
                            $('#leavedate').val(res.leave_days);
                            $('#nopay').val(res.no_pay_days);
                            $('#ot').val(res.normal_ot_hours);
                            $('#dot').val(res.double_ot_hours);

                            let attendances = res.attendances;

                            $('#attendtable tbody').empty();
                            $.each(attendances, function (key, value) {
                                let tr =
                                    '<tr>' +
                                    '<td>' + value.date + '</td>' +
                                    '<td>' + value.day_name + '</td>' +
                                    '<td>' + value.first_timestamp + '</td>' +
                                    '<td>' + value.last_timestamp + '</td>' +
                                    '<td>' + value.work_hours + '</td>' +
                                    '<td>' + value.normal_rate_otwork_hrs + '</td>' +
                                    '<td>' + value.double_rate_otwork_hrs + '</td>' +
                                    '</tr>';
                                $('#attendtable').append(tr);
                            });

                            let working_work_days_breakdown = res.working_work_days_breakdown;

                            $('#working_tbl tbody').empty();
                            $.each(working_work_days_breakdown, function (key, value) {

                                let date_input = '<input type="date" class="form-control form-control-sm date_input" value="' + value.date + '" /> ';
                                let from_input = '<input type="datetime-local" class="form-control form-control-sm from_input" value="' + value.from + '" /> ';
                                let to_input = '<input type="datetime-local" class="form-control form-control-sm to_input" value="' + value.to + '" /> ';
                                let hours_input = '<input type="number" step="0.01" class="form-control form-control-sm hours_input" value="' + value.hours + '" /> ';
                                let work_days_input = '<input type="number" step="0.1" class="form-control form-control-sm work_days_input" value="' + value.work_day + '" /> ';

                                let is_no_pay_leave = '';

                                if (value.is_no_pay_leave == true) {
                                    is_no_pay_leave = 'No Pay Leave';
                                }

                                let comment_input = '<textarea class="form-control form-control-sm comment_input">' + is_no_pay_leave + '</textarea> ';

                                let tr = '';
                                if (value.work_day == 1) {
                                    tr += '<tr style="background-color: #DEF7ED ">';
                                } else {
                                    tr += '<tr style="background-color: #FDE5D0 ">';
                                }
                                tr +=
                                    '<td>' + date_input + '</td>' +
                                    '<td>' + value.day_name + '</td>' +
                                    '<td>' + from_input + '</td>' +
                                    '<td>' + to_input + '</td>' +
                                    '<td>' + hours_input + '</td>' +
                                    '<td>' + work_days_input + '</td>' +
                                    '<td>' + comment_input + '</td>' +
                                    '</tr>';
                                $('#working_tbl').append(tr);
                            });

                            let leave_deductions = res.leave_deductions;
                            $.each(leave_deductions, function (key, value) {
                                let tr =
                                    '<tr style="background-color: #EEB7B8 ">' +
                                    '<td> ' + value.date + ' </td>' +
                                    '<td> ' + value.day_name + ' </td>' +
                                    '<td> </td>' +
                                    '<td> </td>' +
                                    '<td> </td>' +
                                    '<td>(-) ' + value.no_of_days + '</td>' +
                                    '</tr>';
                                $('#working_tbl').append(tr);
                            });


                            let tr =
                                '<tr>' +
                                '<td> </td>' +
                                '<td> </td>' +
                                '<td> </td>' +
                                '<td> </td>' +
                                '<td> </td>' +
                                '<th> <input type="number" step="0.1" class="total_days form-control form-control-sm" value="'+res.work_days+'" /> </th>' +
                                '</tr>';
                            $('#working_tbl').append(tr);

                        }
                    });
                }

            }

            $(document).on('change', '.work_days_input', function () {

                let total_days = 0;

                $('#working_tbl > tbody  > tr').each(function(index, e) {

                    let val = $(this).find("td:nth-child(6)").find('.work_days_input').val();

                    if(val != undefined){
                        val = parseFloat(val)
                        total_days = total_days + val;
                    }

                });

                $('.total_days').val(total_days);

            });

            $(document).on('click', '#work_day_save_btn', function (e) {
                e.preventDefault();
                let save_btn = $(this);
                let r = confirm("Confirm?");

                if (r == true) {
                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );

                    let work_hr_data = [];

                    $('#working_tbl > tbody  > tr').each(function(index, ee) {

                        let date = $(this).find("td:nth-child(1)").find('.date_input').val();
                        let from = $(this).find("td:nth-child(3)").find('.from_input').val();
                        let to = $(this).find("td:nth-child(4)").find('.to_input').val();
                        let hours = $(this).find("td:nth-child(5)").find('.hours_input').val();
                        let work_days = $(this).find("td:nth-child(6)").find('.work_days_input').val();
                        let comment = $(this).find("td:nth-child(7)").find('.comment_input').val();

                        if(work_days != undefined){
                            let a = {}
                            a["date"] = date;
                            a["from"] = from;
                            a["to"] = to;
                            a["hours"] = hours;
                            a["work_days"] = work_days;
                            a["comment"] = comment;

                            work_hr_data.push(a)
                        }

                    });

                    $.ajax({
                        url: "{{route('work_hours_save')}}",
                        method: "POST",
                        data: {
                            'selected_cb': work_hr_data,
                            'emp_id': $('#emp_id').val(),
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                $('.message').html("<div class='alert alert-success'>"+data.msg+"</div>");
                                work_hr_data = [];
                            }else{
                                $('.message').html("<div class='alert alert-danger'>"+data.msg+"</div>");
                            }
                            save_btn.prop("disabled", false);
                            save_btn.html('Save Changes' );
                        }
                    });

                }

            });

        });
    </script>

@endsection