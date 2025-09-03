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
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="msg"></div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Employee ID</label>
                                <input type="text" id="emp_id" value="{{$employee[0]->emp_id}}" class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">EPF No</label>
                                <input type="text" id="emp_etfno" value="{{$employee[0]->emp_etfno}}" class="form-control form-control-sm" disabled>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Employee Name</label>
                                <input type="text" id="emp_name_with_initial" value="{{$employee[0]->emp_name_with_initial}} " class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Work Days</label>
                                <input type="text" id="workdays" value="" class="form-control form-control-sm" disabled>
                            </div>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Leave Days</label>
                                <input type="text" id="leavedate" value="" class="form-control form-control-sm" disabled>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Nopay Days</label>
                                <input type="text" id="nopay" value="" class="form-control form-control-sm" disabled>
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
                                       value="{{date('Y-m')}}"
                                       required
                                >
                            </div>
                            <div class="col">
                                <br>
                                <button type="button" class="btn btn-sm btn-primary mt-2 float-right" id="btn_search">Search</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
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
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-3" id="approvel">Approvel</button>
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
                            <h4 class="font-weight-normal">Please check the devices connection and comfirm?</h4>
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
$(document).ready(function(){
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
        var leavedate = $('#leavedate').val();
        var nopay = $('#nopay').val();

        var not = $('#ot').val();
        var ndot = $('#dot').val();
        var ot = not.replace(':', '.');
        var dot = ndot.replace(':', '.');

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
                    leavedate: leavedate,
                    nopay: nopay,
                    ot: ot,
                    dot: dot,
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

    function get_attendances(){

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
                    $('#workdays').val(res.work_days);
                    $('#leavedate').val(res.leave_days);
                    $('#nopay').val(res.no_pay_days);
                    $('#ot').val(res.normal_ot_hours);
                    $('#dot').val(res.double_ot_hours);

                    let attendances = res.attendances;

                    //each attendances
                    //empty attendtable
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

                }
            });
        }

    }

});
</script>

@endsection