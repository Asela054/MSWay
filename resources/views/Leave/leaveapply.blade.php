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
                         <span>Leave Apply</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Options</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                         <div class="col-md-12">
                             <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record"
                                 id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave
                             </button>
                         </div><br><br>

                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="divicestable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
                                    <th>DESIGNATION</th>
                                    <th>LEAVE TYPE</th>
                                    <th>LEAVE TYPE *</th>
                                     <th>LEAVE FROM</th>
                                    <th>LEAVE TO</th>
                                    <th class="nowrap">REASON</th>
                                    <th>COVERING PERSON</th>
                                    <th>STATUS</th>
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
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Leave Type</label>
                                        <select name="leavetype" id="leavetype"
                                                class="form-control form-control-sm">
                                            <option value="">Select</option>
                                            @foreach($leavetype as $leavetypes)
                                                <option value="{{$leavetypes->id}}">{{$leavetypes->leave_type}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-12">
                                      <label class="small font-weight-bolder text-dark">Select Employee</label>
                                        <select name="employee" id="employee_f" class="form-control form-control-sm" readonly>
                                            <option value="">Select Employee</option>
                                            @foreach($leaveemployees as $leaveemployee)
                                            <option value="{{ $leaveemployee->emp_id }}">
                                                {{ $leaveemployee->emp_name_with_initial }}
                                                {{ $leaveemployee->calling_name ? ' - ' . $leaveemployee->calling_name : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-12">
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
                                                <tr>
                                                    <td> <span>Medical</span> </td>
                                                    <td> <span id="med_total"></span> </td>
                                                    <td> <span id="med_taken"></span> </td>
                                                    <td> <span id="med_available"></span> </td>
                                                </tr>
                                                <tr>
                                                    <td> <span id="weekly_title"></span> </td>
                                                    <td> <span id="weekly_total"></span> </td>
                                                    <td> <span id="weekly_taken"></span> </td>
                                                    <td> <span id="weekly_available"></span> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <span id="leave_msg"></span>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Covering Employee</label>
                                        <select name="coveringemployee" id="coveringemployee"
                                            class="form-control form-control-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">From</label>
                                        <input type="date" name="fromdate" id="fromdate"
                                            class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">To</label>
                                        <input type="date" name="todate" id="todate"
                                            class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">Half Day/ Short <span
                                                id="half_short_span"></span> </label>
                                        <select name="half_short" id="half_short" class="form-control form-control-sm">
                                            <option value="0.00">Select</option>
                                            <option value="0.25">Short Leave</option>
                                            <option value="0.5">Half Day</option>
                                            <option value="1.00">Full Day</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">No of Days</label>
                                        <input type="number" step="0.01" name="no_of_days" id="no_of_days"
                                            class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="form-row mb-1" id="short_leave_time_range" style="display: none;">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">From Time</label>
                                        <input type="time" name="from_time" id="from_time" class="form-control form-control-sm"/>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">To Time</label>
                                        <input type="time" name="to_time" id="to_time" class="form-control form-control-sm"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Reason</label>
                                        <input type="text" name="reson" id="reson"
                                            class="form-control form-control-sm" />
                                    </div>
                                </div>
                                 <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Approve Person Level 1</label>
                                        <select name="approveby" id="approveby" class="form-control form-control-sm">
                                            <option value="">Select</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->emp_id}}">{{$employee->emp_name_with_initial}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                 </div>
                                 <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Approve Person Level 2</label>
                                        <select name="approveby2" id="approveby2" class="form-control form-control-sm">
                                            <option value="">Select</option>
                                            @foreach($employees as $employee2)
                                            <option value="{{$employee2->emp_id}}">{{$employee2->emp_name_with_initial}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                 </div>

                                <div class="form-group d-none">
                                    <label class="small font-weight-bolder text-dark">Email Body</label>
                                    <textarea id="emailBody" class="form-control" rows="10"></textarea>
                                </div>

                                <div class="form-group mt-3">
                                    <input type="submit" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4" value="Add"/>
                                </div>
                                <input type="hidden" name="companyemail" id="companyemail"/>
                                <input type="hidden" name="employeeemail" id="employeeemail"/>
                                <input type="hidden" name="coveringemail" id="coveringemail"/>
                                <input type="hidden" name="approveemail" id="approveemail"/>
                                <input type="hidden" name="approveemail2" id="approveemail2"/>
                                <input type="hidden" name="companyname" id="companyname"/>
                                <input type="hidden" name="departmentheademail" id="departmentheademail"/>

                                <input type="hidden" name="action" id="action" value="Add"/>
                                <input type="hidden" name="hidden_id" id="hidden_id"/>
                                <input type="hidden" name="request_id" id="request_id"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


     <div class="modal fade" id="formModalleaverequests" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
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
                           <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="divicestable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
                                    <th>REQUEST LEAVE</th>
                                    <th>LEAVE FROM</th>
                                    <th>LEAVE TO</th>
                                    <th>REASON</th>
                                    <th class="text-right">ACTION</th>
                                </tr>
                                </thead>
                               <tbody id="requestbody">
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
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
            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#leavemaster').addClass('navbtnactive');

            let company_f = $('#company');
            let department_f = $('#department');
            let employee_f = $('#employee');
            let location_f = $('#location');

            getleaverequests();

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
                            page: params.page || 1,
                            company: company_f.val(),
                            department: department_f.val(),
                            location: location_f.val()
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
                        }
                    },
                    cache: true
                }
            });

            let c_employee = $('#coveringemployee');
            c_employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                parent: '#formModal',
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
                $('#divicestable').DataTable({
                        "destroy": true,
                        "processing": true,
                        "serverSide": true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Leave Details',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'Leave Details', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Leave Details',
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
                    ajax: {
                         url: scripturl + '/leave_apply_list.php',
                         type: 'POST',
                         data : 
                            {department :department, 
                            employee :employee, 
                            location: location,
                            from_date: from_date,
                            to_date: to_date},
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'employee_display', name: 'employee_display' },
                        { data: 'dep_name', name: 'emp_name' },
                        { data: 'title', name: 'title' },
                        { data: 'leave_type', name: 'leave_type' },
                        {
                             data: 'no_of_days',
                            name: 'no_of_days',
                            render: function(data, type, row) {
                                var value = parseFloat(data);
                                
                                if (value === 1.00) {
                                    return "Full Day";
                                } else if (value === 0.50) {
                                    return "Half Day";
                                } else if (value === 0.25) {
                                    return "Short Leave";
                                } else {
                                    return "Unknown";
                                }
                            }
                        },
                        { data: 'leave_from', name: 'leave_from' },
                        { data: 'leave_to', name: 'leave_to' },
                        { 
                            data: 'reson', 
                            name: 'reson',
                            render: function(data, type, row) {
                                if (type === 'display' && data && data.length > 30) {
                                    return data.substr(0, 30) + '...';
                                }
                                return data;
                            }
                        },
                        { data: 'covering_emp', name: 'covering_emp' },
                        { data: 'status', name: 'status' },
                        {
                            data: 'id',
                            name: 'action',
                            className: 'text-right',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                var buttons = '';
                               
                                    buttons += '<button type="button" id="'+ row.id +'" class="pdf-export btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Export PDF"><i class="fas fa-file-pdf"></i></button>';

                                    buttons += '<button name="edit" id="'+ row.id +'"class="edit btn btn-primary btn-sm" style="margin:1px;" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                               
                                    buttons += '<button type="submit" name="delete" id="'+ row.id +'"class="delete btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                                return buttons;
                            }
                        },
                         { data: "emp_name", 
                            name: "emp_name", 
                            visible: false
                            },
                            {   data: "calling_name",
                                name: "calling_name", 
                                visible: false
                            }

                    ],
                    "bDestroy": true,
                    "order": [
                        [5, "desc"]
                    ],
                     drawCallback: function(settings) {
                                $('[data-toggle="tooltip"]').tooltip();
                            }
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
                closeOffcanvasSmoothly();
            });

            $(document).on('change', '#fromdate', function () {
                show_no_of_days();
            });

            $(document).on('change', '#todate', function () {
                show_no_of_days();
            });

            $(document).on('change', '#half_short', function () {
                show_no_of_days();
            });

            function treatAsUTC(date) {
                var result = new Date(date);
                result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
                return result;
            }

            function daysBetween(startDate, endDate) {
                var millisecondsPerDay = 24 * 60 * 60 * 1000;
                return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
            }
            
        });

        $('#leavetype').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee_f').val();
            var status = $('#employee_f option:selected').data('id');
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();

            if (leavetype != '' && emp_id != '') {
                $.ajax({
                    url: "getEmployeeLeaveStatus",
                    method: "POST",
                    data: {status: status, emp_id: emp_id, leavetype: leavetype, _token: _token, fromdate:fromdate, todate:todate },
                    success: function (data) {

                        $('#leave_msg').html('');

                         $('#annual_total').html(data.total_no_of_annual_leaves);
                         $('#annual_taken').html(data.total_taken_annual_leaves);
                         $('#annual_available').html(data.available_no_of_annual_leaves);

                        $('#casual_total').html(data.total_no_of_casual_leaves);
                        $('#casual_taken').html(data.total_taken_casual_leaves);
                        $('#casual_available').html(data.available_no_of_casual_leaves);

                        $('#med_total').html(data.total_no_of_med_leaves);
                        $('#med_taken').html(data.total_taken_med_leaves);
                        $('#med_available').html(data.available_no_of_med_leaves);

                        $('#weekly_total').html(data.total_no_of_weekly_leaves);
                        $('#weekly_taken').html(data.total_taken_weekly_leaves);
                        $('#weekly_available').html(data.available_no_of_weekly_leaves);

                        $('#weekly_title').html(data.other_leaves_title);

                    }
                });
            }

        });

        // Get employee Email address
        $('#employee_f').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#employee_f').val();

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
                    success: function (data) {

                   $('#companyemail').val(data.result.company_email);
                    $('#companyname').val(data.result.company_name);
                    $('#employeeemail').val(data.result.employee_email);
                     $('#departmentheademail').val(data.result.department_head_email);
                    }
                });
            }

        });

          // Get covering employee Email address
        $('#coveringemployee').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#coveringemployee').val();

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
                    success: function (data) {
                    $('#coveringemail').val(data.result.employee_email);
                    }
                });
            }

        });


          // Get approve person Email address
        $('#approveby').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#approveby').val();

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
                    success: function (data) {
                    $('#approveemail').val(data.result.employee_email);
                    }
                });
            }
        });

           // Get approve person Email address
        $('#approveby2').change(function () {
            var _token = $('input[name="_token"]').val();
            var emp_id = $('#approveby2').val();

            if (emp_id != '') {
                $.ajax({
                    url: "getEmployeeCategory",
                    method: "POST",
                    dataType: 'json',
                    data: { emp_id: emp_id, _token: _token},
                    success: function (data) {
                    $('#approveemail2').val(data.result.employee_email);
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
                $('#formModalleaverequests').modal('show');
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';
                var $btn = $('#action_button');
                 $btn.prop('disabled', true);

                 generateEmailBody();
                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('addLeaveApply') }}";
                }

                if ($('#action').val() == 'Edit') {
                    action_url = "{{ url('LeaveApply/update') }}";
                }

                // Collect table data as array
                    var leaveBalanceData = [];
                    
                    // Get all rows from the table body
                    $('table.table tbody tr').each(function() {
                        var row = $(this);
                        var leaveType = row.find('td:first span').text().trim();
                        var total = row.find('td:nth-child(2) span').text().trim();
                        var taken = row.find('td:nth-child(3) span').text().trim();
                        var available = row.find('td:nth-child(4) span').text().trim();
                        
                        leaveBalanceData.push({
                            leave_type: leaveType,
                            total: total,
                            taken: taken,
                            available: available
                        });
                    });

                    // Get form data
                    var formData = $(this).serializeArray();
                    
                    // Add table data to form data
                    formData.push({
                        name: 'leave_balance_data',
                        value: JSON.stringify(leaveBalanceData)
                    });

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (data) {
                           if (data.errors) {
                            const combinedErrors = data.errors.join('<br><br>');
                            $btn.prop('disabled', false); 

                        Swal.fire({
                            icon: 'error',
                            title: 'Leave Balance Errors',
                            html: combinedErrors,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        }
                        
                       if (data.success) {
                           const emailBody = generateEmailBody();

                           var emailData = {
                               'inquire_now': 'HR Department - ' + $('#companyname').val(),
                               'replyto': [
                                   $('#employeeemail').val(),
                                   $('#companyemail').val(),
                                   $('#coveringemail').val(),
                                   $('#approveemail').val(),
                                   $('#departmentheademail').val()
                               ].filter(email => email).join(';'),
                               'contsubj': 'Leave Application - ' + $('#employee_f option:selected').text(),
                               'contbody': emailBody
                           };

                           // Create a temporary iframe
                           var iframe = document.createElement('iframe');
                           iframe.name = 'emailIframe';
                           iframe.style.display = 'none';

                           // Create the form
                           var form = document.createElement('form');
                           form.target = 'emailIframe';
                           form.method = 'POST';
                           form.action = 'https://aws.erav.lk/Temp/bf360/eravawsmail.php';

                           // Add form inputs
                           Object.keys(emailData).forEach(function (key) {
                               var input = document.createElement('input');
                               input.type = 'hidden';
                               input.name = key;
                               input.value = emailData[key];
                               form.appendChild(input);
                           });

                           // Add to document and submit
                           document.body.appendChild(iframe);
                           document.body.appendChild(form);
                           form.submit();

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
                               $('#formTitle')[0].reset();
                               actionreload(actionJSON);
                           }

                       }
                        $('#form_result').html(html);
                    }
                });
            });


            $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "LeaveApply/" + id + "/edit",
                        dataType: "json",
                        success: function (data) {
                            $('#leavetype').val(data.result.leave_type);

                            let empOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.employee.emp_name_with_initial);
                            $('#employee_f').append(empOption).trigger('change');

                            let coveringemployeeOption = $("<option selected></option>").val(data.result.emp_covering).text(data.result.covering_employee.emp_name_with_initial);
                            $('#coveringemployee').append(coveringemployeeOption).trigger('change');

                            let approvebyOption = $("<option selected></option>").val(data.result.leave_approv_person).text(data.result.approve_by.emp_name_with_initial);
                            $('#approveby').append(approvebyOption).trigger('change');

                            $('#employee_f').val(data.result.emp_id);
                            $('#fromdate').val(data.result.leave_from);
                            $('#todate').val(data.result.leave_to);
                            $('#half_short').val(data.result.half_short).trigger('change');
                            $('#no_of_days').val(data.result.no_of_days);
                            $('#reson').val(data.result.reson);
                            $('#comment').val(data.result.comment);
                            $('#coveringemployee').val(data.result.emp_covering);
                            $('#approveby').val(data.result.leave_approv_person);
                            $('#approveby2').val(data.result.leave_approv_person_2);
                            $('#available_leave').val(data.result.total_leave);
                            $('#assign_leave').val(data.result.assigned_leave);
                            $('#leavecat').val(data.result.leave_category);
                            $('#hidden_id').val(id);
                            $('.modal-title').text('Edit Leave');
                            $('#action_button').val('Edit');
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
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
                    url: "LeaveApply/destroy/" + user_id,
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
        });

        $(document).on('click', '.addrequest', function () {
            var id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{!! route("leaverequestedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#leavetype').val(data.result.leave_type);
                    $('#employee_f').val(data.result.emp_id).trigger('change');
                    $('#fromdate').val(data.result.from_date);
                    $('#todate').val(data.result.to_date);
                    $('#reson').val(data.result.reason);
                    $('#request_id').val(id);
                    $('#half_short').val(data.result.leave_category).trigger('change');
                    $('#from_time').val(data.result.from_time);
                    $('#to_time').val(data.result.to_time);
                    $('#coveringemployee').val(data.result.covering_emp).trigger('change');

                    let coveringemployeeOption = $("<option selected></option>").val(data.result.covering_emp).text(data.result.covering_emp_name);
                    $('#coveringemployee').append(coveringemployeeOption).trigger('change');

                    var fromdate = data.result.from_date;
                    var todate = data.result.to_date;
                    var _token = $('input[name="_token"]').val();
                    var leavetype = $('#leavetype').val();
                    var emp_id = $('#employee_f').val();
                    var status = $('#employee_f option:selected').data('id');



                    
                    if (leavetype != '' && emp_id != ''&& fromdate != ''&& todate != '') {
                        $.ajax({
                            url: "getEmployeeLeaveStatus",
                            method: "POST",
                            data: {status: status, 
                                   emp_id: emp_id, 
                                   leavetype: leavetype, 
                                   _token: _token,
                                   fromdate:fromdate,
                                   todate:todate },
                            success: function (data) {

                                $('#leave_msg').html('');

                                $('#annual_total').html(data.total_no_of_annual_leaves);
                                $('#annual_taken').html(data.total_taken_annual_leaves);
                                $('#annual_available').html(data.available_no_of_annual_leaves);

                                $('#casual_total').html(data.total_no_of_casual_leaves);
                                $('#casual_taken').html(data.total_taken_casual_leaves);
                                $('#casual_available').html(data.available_no_of_casual_leaves);

                                $('#med_total').html(data.total_no_of_med_leaves);
                                $('#med_taken').html(data.total_taken_med_leaves);
                                $('#med_available').html(data.available_no_of_med_leaves);

                                $('#weekly_total').html(data.total_no_of_weekly_leaves);
                                $('#weekly_taken').html(data.total_taken_weekly_leaves);
                                $('#weekly_available').html(data.available_no_of_weekly_leaves);

                                $('#weekly_title').html(data.other_leaves_title);

                            }
                        });
                    }

                    show_no_of_days();
                    loadLastApprovers(data.result.emp_id);

                    $('#formModal').modal('show');
                     $('#formModalleaverequests').modal('hide');
                }
            })
        });

        $('#half_short').on('change', function() {
            const timeRangeDiv = document.getElementById('short_leave_time_range');
            const fromTimeInput = document.getElementById('from_time');
            const toTimeInput = document.getElementById('to_time');
            
            if (this.value === '0.25') {
                timeRangeDiv.style.display = 'flex';
                fromTimeInput.required = true;
                toTimeInput.required = true;

                if ($('#from_time').val()) {
                    fromTimeInput.value = $('#from_time').val();
                }
                if ($('#to_time').val()) {
                    toTimeInput.value = $('#to_time').val();
                }
            } else {
                timeRangeDiv.style.display = 'none';
                fromTimeInput.required = false;
                toTimeInput.required = false;
                fromTimeInput.value = '';
                toTimeInput.value = '';
            }
        });

        $(document).on('click', '.pdf-export', function () {
            var id    = $(this).attr('id');
            var row   = $(this).closest('table').DataTable().row($(this).closest('tr')).data();
            var token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: 'LeaveApply/' + id + '/edit',
                dataType: 'json',
                success: function (data) {
                    var d = data.result;

                    // 1) company name (and other employee info) from getEmployeeCategory
                    $.ajax({
                        url: 'getEmployeeCategory',
                        method: 'POST',
                        dataType: 'json',
                        data: { emp_id: d.emp_id, _token: token },
                        success: function (cat) {
                            if (cat && cat.result) {
                                d.company_name = cat.result.company_name;
                            }
                        },
                        complete: function () {

                            // 2) leave balance
                            $.ajax({
                                url: 'getEmployeeLeaveStatus',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    emp_id: d.emp_id, leavetype: d.leave_type,
                                    fromdate: d.leave_from, todate: d.leave_to,
                                    _token: token
                                },
                                success: function (bal) { generateLeaveApplyPdf(d, row, bal); },
                                error: function ()      { generateLeaveApplyPdf(d, row, {}); }
                            });

                        }
                    });
                }
            });
        });




    function getleaverequests() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("employeeleaverequest") !!}',
            type: 'POST',
            dataType: "json",
            success: function (data) {
                var reuestlist = data.result;
                $("#requestbody").html(reuestlist);
            }
        });

    }
      
    function generateEmailBody() {
            var employeeName    = $('#employee_f option:selected').text();
            var employeeId      = $('#employee_f').val();
            var leaveType       = $('#leavetype option:selected').text();
            var fromDate        = $('#fromdate').val();
            var toDate          = $('#todate').val();
            var noOfDays        = $('#no_of_days').val();
            var reason          = $('#reason').val() || $('#reson').val();   // supports both ids
            var coveringEmp     = $('#coveringemployee option:selected').text();
            var approvingPerson = $('#approveby option:selected').text();
            var approvingPerson2 = $('#approveby2 option:selected').text();
            var halfShort       = $('#half_short option:selected').text();

            if (halfShort === 'Select') { halfShort = ''; }

            var rowsData = [
                ['Employee', employeeName],
                ['Employee ID', employeeName ? (employeeId || 'N/A') : ''],
                ['Leave Type', leaveType],
                ['Leave Duration', halfShort],
                ['From Date', fromDate],
                ['To Date', toDate],
                ['Number of Days', noOfDays],
                ['Reason', reason],
                ['Covering Employee', coveringEmp],
                ['Approving Person', approvingPerson],
                ['Approving Person 2', approvingPerson2]
            ];

            var body = '<p>Dear Sir/Madam,</p>';
            body += '<p>A leave application has been submitted with the following details:</p>';
            body += '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:14px;">';
            for (var i = 0; i < rowsData.length; i++) {
                if (!rowsData[i][1]) { continue; }
                body += '<tr>' +
                    '<td style="border:1px solid #ccc;background:#f5f5f5;"><b>' + rowsData[i][0] + '</b></td>' +
                    '<td style="border:1px solid #ccc;">' + escHtml(rowsData[i][1]) + '</td>' +
                    '</tr>';
            }
            body += '</table>';
            body += '<p>Please review this request in the HR system.</p>';
            body += '<p>Regards,<br>' + escHtml(employeeName || 'Employee') + '</p>';

            $('#emailBody').val(body);
            return body;
    }

    function show_no_of_days() {
        let from_date = $('#fromdate').val();
        let to_date = $('#todate').val();
        let half_short = $('#half_short').val() || 0;
        let empid = $('#employee_f').val();

        if (from_date && to_date) {
            $.ajax({
                url: '{!! route("calculate-working-days") !!}',
                type: 'POST',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    half_short: half_short,
                    empid: empid,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#no_of_days').val(response.working_days);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    }

    function escHtml(s) {
     return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function loadLastApprovers(empId) {
        if (!empId) { return; }

        $.ajax({
            url: '{!! route("getLastLeaveApprovers") !!}',
            type: 'POST',
            dataType: 'json',
            data: {
                emp_id: empId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (!res.found) { return; }

                if (res.leave_approv_person) {
                    $('#approveby').val(res.leave_approv_person).trigger('change');
                }
                if (res.leave_approv_person_2) {
                    $('#approveby2').val(res.leave_approv_person_2).trigger('change');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }
    
    function pdfParts(str) {
        if (!str) return ['', '', ''];
        return String(str).substring(0, 10).split('-');
    }
    
    function pdfAddDays(str, n) {
        var d = new Date(String(str).substring(0, 10) + 'T00:00:00');
        d.setDate(d.getDate() + n);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var dd = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + dd;
    }
    
    function pdfOptText(selector, val) {
        if (!val) return '';
        return $.trim($(selector + ' option[value="' + val + '"]').first().text());
    }
    
    function pdfHalfShortText(v) {
        var n = parseFloat(v);
        if (n === 1) return 'Full Day';
        if (n === 0.5) return 'Half Day';
        if (n === 0.25) return 'Short Leave';
        return '';
    }
 

    function generateLeaveApplyPdf(d, row, bal) {
        bal = bal || {};
        var jsPDFLib = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
        var doc = new jsPDFLib({ unit: 'mm', format: 'a4' });
    
        var THIN = 0.2, THICK = 0.7;
        var X0 = 10, X1 = 200;
    
        // ---------- drawing helpers ----------
        function box(x, y, w, h) { doc.setLineWidth(THIN); doc.rect(x, y, w, h); }
        function thickLine(x1, y1, x2, y2) { doc.setLineWidth(THICK); doc.line(x1, y1, x2, y2); }
        function thickRect(x, y, w, h) { doc.setLineWidth(THICK); doc.rect(x, y, w, h); }
    
        function fitSize(t, maxW, size, bold, minSize) {
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.setFontSize(size);
            while (size > minSize && doc.getTextWidth(t) > maxW) {
                size -= 0.5;
                doc.setFontSize(size);
            }
            return size;
        }
        function label(t, x, y, h) {
            t = String(t || '');
            fitSize(t, 31, 9, true, 6);
            doc.text(t, x + 2, y + h / 2, { baseline: 'middle' });
        }
        function cellText(t, x, y, w, h, size, bold) {
            t = String(t === null || t === undefined ? '' : t);
            var maxW = w - 3;
            var s = fitSize(t, maxW, size || 9, bold, 6);
            if (doc.getTextWidth(t) > maxW) {
                var lines = doc.splitTextToSize(t, maxW);
                var lh = s * 0.3528 * 1.15;
                var startY = y + h / 2 - ((lines.length - 1) * lh) / 2;
                doc.text(lines, x + w / 2, startY, { align: 'center', baseline: 'middle' });
                return;
            }
            doc.text(t, x + w / 2, y + h / 2, { align: 'center', baseline: 'middle' });
        }
        function center(t, x, y, w, bold, size) {
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.setFontSize(size || 9);
            doc.text(t, x + w / 2, y, { align: 'center' });
        }
        function tick(cx, cy) {
            doc.setLineWidth(0.6);
            doc.line(cx - 2, cy, cx - 0.5, cy + 1.5);
            doc.line(cx - 0.5, cy + 1.5, cx + 2.5, cy - 2);
        }
    
        // ---------- data ----------
        var emp = d.employee || {};
        var empId     = d.emp_id || row.emp_id || '';
        var empName   = emp.emp_name_with_initial || row.employee_display || '';
        var dept      = row.dep_name || d.dep_name || '';
        var empNo     = d.emp_no || empId;
        var desig     = d.designation || emp.emp_designation || '';
        var section   = d.section || '';
        var location_ = d.location || '';
        var reqDate   = d.created_at ? d.created_at : new Date().toISOString();
        var days      = (d.no_of_days !== undefined && d.no_of_days !== null) ? d.no_of_days : row.no_of_days;
        days          = (days === undefined || days === null) ? '' : parseFloat(days);
        var fromDate  = d.leave_from || row.leave_from || '';
        var toDate    = d.leave_to || row.leave_to || '';
        var resume    = toDate ? pdfAddDays(toDate, 1) : '';
        var companyName = d.company_name || 'Erav Technologies (Pvt) Ltd';
    
        var coverName = (d.covering_employee && d.covering_employee.emp_name_with_initial) || row.covering_emp || pdfOptText('#coveringemployee', d.emp_covering);
        var app1Name  = (d.approve_by && d.approve_by.emp_name_with_initial) || pdfOptText('#approveby', d.leave_approv_person);
        var app2Name  = (d.approve_by_2 && d.approve_by_2.emp_name_with_initial) || pdfOptText('#approveby2', d.leave_approv_person_2);
        var halfShort = pdfHalfShortText(d.half_short);
        var leaveTypeName = row.leave_type || '';
        var statusText = row.status || '';
    
        // ---------- header ----------
        center(companyName, 0, 16, 210, true, 14);
        center('Leave Application Form', 0, 23, 210, true, 11);
        center('Office Staff', 0, 28, 210, true, 9);
    
        // ---------- top table ----------
        var y = 32, h = 9;
        var rows = [
            ['Name:', empName, 'Department:', dept],
            ['EMP No:', empNo, 'Section:', section],
            ['Designation:', desig, 'Location:', location_]
        ];
        for (var i = 0; i < rows.length; i++) {
            var ry = y + i * h;
            box(X0, ry, 35, h);  label(rows[i][0], X0, ry, h);
            box(45, ry, 90, h);  cellText(rows[i][1], 45, ry, 90, h);
            box(135, ry, 25, h); label(rows[i][2], 135, ry, h);
            box(160, ry, 40, h); cellText(rows[i][3], 160, ry, 40, h);
        }
    
        // ---------- left block ----------
        var ly = 59;
        box(X0, ly, 35, h); label('No of Leave Days:', X0, ly, h);
        box(45, ly, 35, h); cellText(days, 45, ly, 35, h);
    
        function dateRow(text, dateStr, yy) {
            var p = pdfParts(dateStr);
            box(X0, yy, 35, h); label(text, X0, yy, h);
            box(45, yy, 15, h); cellText(p[0], 45, yy, 15, h);
            box(60, yy, 10, h); cellText(p[1], 60, yy, 10, h);
            box(70, yy, 10, h); cellText(p[2], 70, yy, 10, h);
        }
        dateRow('Leave Request Date:', reqDate, 68);
        dateRow('Leave Start Date:', fromDate, 77);
        dateRow('Date of Resuming Date:', resume, 86);
    
        box(X0, 95, 35, 30); label('Reason for Leave:', X0, 95, 30);
        box(45, 95, 35, 30);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        var reasonLines = doc.splitTextToSize(d.reson || '', 31);
        var lineH = 3.5;
        var startY = 95 + 15 - ((reasonLines.length - 1) * lineH) / 2;
        doc.text(reasonLines, 45 + 17.5, startY, { align: 'center', baseline: 'middle' });
    
        // ---------- right block (signatures) ----------
        box(80, 59, 120, 27);
        box(80, 86, 120, 39);
        doc.setFontSize(8); doc.setFont('helvetica', 'normal');
        doc.text('.............................', 110, 78, { align: 'center' });
        center('Applicant', 80, 83, 60, false, 8);
        doc.text('...............................', 170, 78, { align: 'center' });
        center('Section Head', 140, 83, 60, false, 8);
        doc.text('.............................', 110, 118, { align: 'center' });
        center('Head of Department', 80, 123, 60, false, 8);
        doc.text('...............................', 170, 118, { align: 'center' });
        center('Approved by CEO', 140, 123, 60, false, 8);
    
        // ---------- HR department ----------
        box(X0, 125, 190, 7);
        center('HR Department', X0, 129.5, 190, true, 9);
    
        var types = ['AL', 'CL', 'Sick', 'ML', 'Other', 'No Pay', ''];
        var CW = 11;
        box(X0, 132, 35, 16); cellText('Leave Type', X0, 132, 35, 16, 9, true);
        for (var t = 0; t < types.length; t++) {
            var tx = 45 + t * CW;
            box(tx, 132, CW, 7); cellText(types[t], tx, 132, CW, 7, 8, true);
            box(tx, 139, CW, 9);
        }
        var cmX = 45 + types.length * CW;
        box(cmX, 132, X1 - cmX, 16);
        doc.setTextColor(150);
        cellText('More Comments', cmX, 132, X1 - cmX, 16, 8, false);
        doc.setTextColor(0);
    
        // tick leave type
        var lt = (leaveTypeName || '').toLowerCase();
        var idx = 4;
        if (lt.indexOf('annual') > -1) idx = 0;
        else if (lt.indexOf('casual') > -1) idx = 1;
        else if (lt.indexOf('sick') > -1) idx = 2;
        else if (lt.indexOf('medical') > -1) idx = 3;
        else if (lt.indexOf('no pay') > -1 || lt.indexOf('nopay') > -1) idx = 5;
        tick(45 + idx * CW + CW / 2, 143.5);
    
        // ---------- bottom box ----------
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        doc.text('...............................', 45, 160, { align: 'center' });
        center('HR Manager', X0, 168, 70, false, 8);
        center('Leave', 95, 159, 20, true, 10);
        center('Updated By', 95, 165, 20, true, 10);
    
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        doc.text('...............................', 165, 160, { align: 'center' });
        center('Payroll Officer', 130, 168, 70, false, 8);
    
        // =================================================================
        // EXTRA SECTION (Leave Apply additional data)
        // =================================================================
        var ey = 178;
        box(X0, ey, 190, 7);
        center('Leave Application Details', X0, ey + 4.5, 190, true, 9);
        ey += 7;
    
        var extra = [
            ['Leave Type:',       leaveTypeName, 'Duration:',        halfShort],
            ['No of Days:',       days,          'Status:',          statusText],
            ['Covering Employee:', coverName,    'Leave Period:',    fromDate + ' to ' + toDate],
            ['Approver Level 1:', app1Name,      'Approver Level 2:', app2Name]
        ];
        for (var e = 0; e < extra.length; e++) {
            var eY = ey + e * h;
            box(X0, eY, 35, h);  label(extra[e][0], X0, eY, h);
            box(45, eY, 60, h);  cellText(extra[e][1], 45, eY, 60, h, 9, false);
            box(105, eY, 35, h); label(extra[e][2], 105, eY, h);
            box(140, eY, 60, h); cellText(extra[e][3], 140, eY, 60, h, 9, false);
        }
        ey += extra.length * h;
    
        // from / to time (short leave only)
        if (d.from_time || d.to_time) {
            box(X0, ey, 35, h);  label('Time:', X0, ey, h);
            box(45, ey, 155, h); cellText((d.from_time || '') + ' - ' + (d.to_time || ''), 45, ey, 155, h, 9, false);
            ey += h;
        }
    
        // comment
        if (d.comment) {
            box(X0, ey, 35, h);  label('Comment:', X0, ey, h);
            box(45, ey, 155, h); cellText(d.comment, 45, ey, 155, h, 8, false);
            ey += h;
        }
    
        // ---------- leave balance ----------
        ey += 3;
        box(X0, ey, 190, 7);
        center('Leave Balance', X0, ey + 4.5, 190, true, 9);
        ey += 7;
    
        var bh = 7;
        var heads = ['Leave Type', 'Total', 'Taken', 'Available'];
        var bw = [70, 40, 40, 40];
        var bx = X0;
        for (var c = 0; c < heads.length; c++) {
            box(bx, ey, bw[c], bh);
            cellText(heads[c], bx, ey, bw[c], bh, 8, true);
            bx += bw[c];
        }
        ey += bh;
    
        var balRows = [
            ['Annual', bal.total_no_of_annual_leaves, bal.total_taken_annual_leaves, bal.available_no_of_annual_leaves],
            ['Casual', bal.total_no_of_casual_leaves, bal.total_taken_casual_leaves, bal.available_no_of_casual_leaves],
            ['Medical', bal.total_no_of_med_leaves, bal.total_taken_med_leaves, bal.available_no_of_med_leaves],
            [bal.other_leaves_title || 'Other', bal.total_no_of_weekly_leaves, bal.total_taken_weekly_leaves, bal.available_no_of_weekly_leaves]
        ];
        for (var r = 0; r < balRows.length; r++) {
            bx = X0;
            for (var k = 0; k < 4; k++) {
                box(bx, ey, bw[k], bh);
                cellText(balRows[r][k], bx, ey, bw[k], bh, 8, false);
                bx += bw[k];
            }
            ey += bh;
        }
        var extraEnd = ey;
    
        // ---------- THICK main lines (drawn last) ----------
        thickRect(X0, 32, 190, 27);
        thickLine(135, 32, 135, 59);
        thickLine(X0, 59, X1, 59);
        thickLine(80, 59, 80, 125);
        thickLine(X1, 59, X1, 125);
        thickLine(80, 86, X1, 86);
        thickLine(X0, 59, X0, 125);
        thickLine(X0, 125, X1, 125);
        thickRect(X0, 125, 190, 7);
        thickRect(X0, 152, 190, 20);
        thickLine(X0, 132, X0, 148);
        thickLine(X1, 132, X1, 148);
        thickLine(X0, 148, X1, 148);
        thickRect(X0, 178, 190, extraEnd - 178);   // outer border of extra section
    
        doc.save('Leave_Application_' + (empNo || empId) + '_' + fromDate + '.pdf');
    }


    </script>

@endsection