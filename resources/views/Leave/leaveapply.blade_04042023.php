@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Leave Apply</span>
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
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
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
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right"
                                    name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave
                            </button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <table class="table table-striped table-bordered table-sm small" id="divicestable">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Leave Type</th>
                                    <th>Leave Type *</th>
                                    <th>Leave From</th>
                                    <th>Leave To</th>
                                    <th>Reason</th>
                                    <th>Covering Person</th>
                                    <th>Status</th>
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
        <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formTitle" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Leave Type</label>
                                            <select name="leavetype" id="leavetype"
                                                    class="form-control form-control-sm">
                                                <option value="">Select</option>
                                                @foreach($leavetype as $leavetypes)
                                                    <option value="{{$leavetypes->id}}">{{$leavetypes->leave_type}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Select Employee</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm">
                                                <option value="">Select</option>
                                                @foreach($employee as $employees)
                                                    <option data-id="{{$employees->emp_status}}"
                                                            value="{{$employees->emp_id}}">{{$employees->emp_name_with_initial.' - '.$employees->emp_etfno}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <table class="table table-sm small">
                                                <thead>
                                                    <tr>
                                                        <th>Leave Type</th>
                                                        <th>Total</th>
                                                        <th>Taken</th>
                                                        <th>Available</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td> <span> Annual </span> </td>
                                                        <td> <span id="annual_total"></span> </td>
                                                        <td> <span id="annual_taken"></span> </td>
                                                        <td> <span id="annual_available"></span> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> <span> Casual </span> </td>
                                                        <td> <span id="casual_total"></span> </td>
                                                        <td> <span id="casual_taken"></span> </td>
                                                        <td> <span id="casual_available"></span> </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <span id="leave_msg"></span>
                                        </div>

                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Covering Employee</label>
                                            <select name="coveringemployee" id="coveringemployee"
                                                    class="form-control form-control-sm">
                                                <option value="">Select</option>
                                                @foreach($employee as $employees)
                                                    <option value="{{$employees->emp_id}}">
                                                        {{$employees->emp_name_with_initial.' - '.$employees->emp_etfno}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">From</label>
                                            <input type="date" name="fromdate" id="fromdate"
                                                   class="form-control form-control-sm" placeholder="YYYY-MM-DD"/>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">To</label>
                                            <input type="date" name="todate" id="todate"
                                                   class="form-control form-control-sm" placeholder="YYYY-MM-DD"/>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Half Day/ Short</label>
                                            <select name="half_short" id="half_short"
                                                    class="form-control form-control-sm">
                                                <option value="0.00">Select</option>
                                                <option value="0.25">Short Leave</option>
                                                <option value="0.5">Half Day</option>
                                                <option value="1.00">Full Day</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Reason</label>
                                            <input type="text" name="reson" id="reson"
                                                   class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Approve Person</label>
                                            <select name="approveby" id="approveby"
                                                    class="form-control form-control-sm">
                                                <option value="">Select</option>
                                                @foreach($employee as $employees)
                                                    <option value="{{$employees->id}}">{{$employees->emp_name_with_initial.' - '.$employees->emp_etfno}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">

                                        <input type="submit" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4" value="Add"/>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add"/>
                                    <input type="hidden" name="hidden_id" id="hidden_id"/>

                                </form>
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
        <!-- Modal Area End -->
    </main>

@endsection


@section('script')

    <script>
        $(document).ready(function () {

            $('#leaves_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#leave_collapse').addClass('show');
            $('#leave_apply_link').addClass('active');

            let company_f = $('#company_f');
            let department_f = $('#department_f');
            let employee_f = $('#employee_f');
            let location_f = $('#location_f');

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
                            company: company_f.val()
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
                            page: params.page || 1
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
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

            function load_dt(department, employee, location, from_date, to_date){
                $('#divicestable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            className: 'btn btn-default',
                            exportOptions: {
                                columns: 'th:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Print',
                            className: 'btn btn-default',
                            exportOptions: {
                                columns: 'th:not(:last-child)'
                            }
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{!! route('leave_list_dt') !!}",
                        "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'emp_name', name: 'emp_name' },
                        { data: 'dep_name', name: 'emp_name' },
                        { data: 'leave_type', name: 'leave_type' },
                        { data: 'half_or_short', name: 'half_or_short' },
                        { data: 'leave_from', name: 'leave_from' },
                        { data: 'leave_to', name: 'leave_to' },
                        { data: 'reson', name: 'reson' },
                        { data: 'covering_emp', name: 'covering_emp' },
                        { data: 'status', name: 'status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    "bDestroy": true,
                    "order": [
                        [3, "desc"]
                    ]
                });
            }

            load_dt('', '', '', '', '');

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department_f').val();
                let employee = $('#employee_f').val();
                let location = $('#location_f').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                load_dt(department, employee, location, from_date, to_date);
            });
        });

        $('#employee').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee').val();
            var status = $('#employee option:selected').data('id');

            if (leavetype != '' && emp_id != '') {
                $.ajax({
                    url: "getEmployeeLeaveStatus",
                    method: "POST",
                    data: {status: status, emp_id: emp_id, leavetype: leavetype, _token: _token},
                    success: function (data) {

                        $('#leave_msg').html('');

                         $('#annual_total').html(data.total_no_of_annual_leaves);
                         $('#annual_taken').html(data.total_taken_annual_leaves);
                         $('#annual_available').html(data.available_no_of_annual_leaves);

                        $('#casual_total').html(data.total_no_of_casual_leaves);
                        $('#casual_taken').html(data.total_taken_casual_leaves);
                        $('#casual_available').html(data.available_no_of_casual_leaves);

                        let msg = '' +
                            '<div class="alert alert-warning text-sm" style="padding: 3px;"> ' +
                                data.leave_msg +
                            '</div>'

                        if(data.leave_msg != ''){
                            $('#leave_msg').html(msg);
                        }

                    }
                });
            }

        });

        $('#todate').change(function () {

            var assign_leave = $('#assign_leave').val();


            var todate = $('#fromdate').val();
            var fromdate = $('#todate').val();
            var date1 = new Date(todate);
            var date2 = new Date(fromdate);
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10);

            var leaveavailable = $('#available_leave').val();
            var assign_leave = $('#assign_leave').val();

            if (leaveavailable != '') {
                $('#available_leave').val(leaveavailable);
            } else {
                $('#available_leave').val(assign_leave);
            }


            if (leaveavailable <= diffDays) {
                $('#message').html("<div class='alert alert-danger'>You Cant Apply, You Have " + assign_leave + " Days Only</div>");
            } else {
                $('#message').html("");

            }


        });

        $(document).ready(function () {
            $('#create_record').click(function () {
                $('.modal-title').text('Apply Leave');
                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');

                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';


                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('addLeaveApply') }}";
                }


                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('LeaveApply.update') }}";
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
                            $('#formTitle')[0].reset();
                            $('#divicestable').DataTable().ajax.reload();
                            setTimeout(function() { $('#formModal').modal('hide'); }, 1000);
                        }
                        $('#form_result').html(html);
                    }
                });
            });


            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "LeaveApply/" + id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#leavetype').val(data.result.leave_type);

                        $('#employee').val(data.result.emp_id);
                        $('#fromdate').val(data.result.leave_from);
                        $('#todate').val(data.result.leave_to);
                        $('#half_short').val(data.result.half_short);
                        $('#reson').val(data.result.reson);
                        $('#comment').val(data.result.comment);
                        $('#coveringemployee').val(data.result.emp_covering);
                        $('#approveby').val(data.result.leave_approv_person);
                        $('#available_leave').val(data.result.total_leave);
                        $('#assign_leave').val(data.result.assigned_leave);
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Leave');
                        $('#action_button').val('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
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
                    url: "LeaveApply/destroy/" + user_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#divicestable').DataTable().ajax.reload();
                            alert('Data Deleted');
                        }, 2000);
                        location.reload();
                    }
                })
            });

        });
    </script>

@endsection