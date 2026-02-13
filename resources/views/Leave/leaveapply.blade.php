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
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-6">
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
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">Select Employee</label>
                                        <select name="employee" id="employee_f" class="form-control form-control-sm">
                                            <option value="">Select</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-6">
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
                                                    <td> <span>Weekly</span> </td>
                                                    <td> <span id="weekly_total"></span> </td>
                                                    <td> <span id="weekly_taken"></span> </td>
                                                    <td> <span id="weekly_available"></span> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <span id="leave_msg"></span>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <table class="table table-sm small">
                                            <thead>
                                                <tr>
                                                    <th>From Date</th>
                                                    <th>To Date</th>
                                                    <th>Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="requestbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">Covering Employee</label>
                                        <select name="coveringemployee" id="coveringemployee"
                                            class="form-control form-control-sm">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-sm-6 col-md-3">
                                        <label class="small font-weight-bolder text-dark">From</label>
                                        <input type="date" name="fromdate" id="fromdate"
                                            class="form-control form-control-sm" placeholder="YYYY-MM-DD" />
                                    </div>
                                    <div class="col-sm-6 col-md-3">
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
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">Reason</label>
                                        <input type="text" name="reson" id="reson"
                                            class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder text-dark">Approve Person</label>
                                        <select name="approveby" id="approveby" class="form-control form-control-sm">
                                            <option value="">Select</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->emp_id}}">{{$employee->emp_name_with_initial}}
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
                                <input type="hidden" name="companyname" id="companyname"/>

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
                            department: department_f.val()
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

            let employee = $('#employee_f');
            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                parent: '#formModal',
                ajax: {
                    url: '{{url("employee_list_leaverequest")}}',
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
                        { data: 'leave_type', name: 'leave_type' },
                        { 
                            data: 'half_short', name: 'half_short', render: function(data, type, row) {
                                if (data == 1) {
                                    return "Full Day";
                                } else if (data == 0.50) {
                                    return "Half Day";
                                } else if (data == 0.25) {
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
                        success: function(response) {
                            $('#no_of_days').val(response.working_days);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            }

             // Bind the function to all relevant fields
            $('#approveby').change(function() {
                generateEmailBody();
            });
            
        });

        $('#employee_f').change(function () {
            var _token = $('input[name="_token"]').val();
            var leavetype = $('#leavetype').val();
            var emp_id = $('#employee_f').val();
            var status = $('#employee_f option:selected').data('id');

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

                        $('#med_total').html(data.total_no_of_med_leaves);
                        $('#med_taken').html(data.total_taken_med_leaves);
                        $('#med_available').html(data.available_no_of_med_leaves);

                        $('#weekly_total').html(data.total_no_of_weekly_leaves);
                        $('#weekly_taken').html(data.total_taken_weekly_leaves);
                        $('#weekly_available').html(data.available_no_of_weekly_leaves);

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
                    }
                });

                getleaverequests(emp_id);
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
                                   $('#approveemail').val()
                               ].filter(email => email).join(';'),
                               'contsubj': 'Leave Application - ' + $('#employee option:selected').text(),
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
                            $('#half_short').val(data.result.half_short);
                            $('#no_of_days').val(data.result.no_of_days);
                            $('#reson').val(data.result.reson);
                            $('#comment').val(data.result.comment);
                            $('#coveringemployee').val(data.result.emp_covering);
                            $('#approveby').val(data.result.leave_approv_person);
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
                    $('#fromdate').val(data.result.from_date);
                    $('#todate').val(data.result.to_date);
                    $('#half_short').val(data.result.leave_category);
                    $('#reson').val(data.result.reason);
                    $('#request_id').val(id);
                }
            })
        });



        function getleaverequests(employee) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeleaverequest") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    emp_id: employee
                },
                success: function (data) {
                    var reuestlist = data.result;
                    $("#requestbody").html(reuestlist);
                }
            });

        }
      
    function generateEmailBody() {
            let body = "LEAVE APPLICATION DETAILS<br>";
            body += "=========================<br><br>";
            
            // Employee details
            const employeeName = $('#employee option:selected').text();
            const employeeId = $('#employee').val();
            if (employeeName) {
                body += "EMPLOYEE: " + employeeName + "<br>";
                body += "EMPLOYEE ID: " + (employeeId || 'N/A') + "<br>";
            }
            
            // Leave type
            const leaveType = $('#leavetype option:selected').text();
            if (leaveType) {
                body += "LEAVE TYPE: " + leaveType + "<br>";
            }
            
            // Dates
            const fromDate = $('#fromdate').val();
            const toDate = $('#todate').val();
            if (fromDate) {
                body += "FROM DATE: " + fromDate + "<br>";
            }
            if (toDate) {
                body += "TO DATE: " + toDate + "<br>";
            }
            
            // Days
            const noOfDays = $('#no_of_days').val();
            if (noOfDays) {
                body += "NUMBER OF DAYS: " + noOfDays + "<br>";
            }
            
            // Reason
            const reason = $('#reson').val();
            if (reason) {
                body += "REASON:" + reason + "<br>";
            }
            
            // Covering employee
            const coveringEmployee = $('#coveringemployee option:selected').text();
            if (coveringEmployee) {
                body += "COVERING EMPLOYEE:" + coveringEmployee + "<br>";
            }
            
            // Approving person
            const approvingPerson = $('#approveby option:selected').text();
            if (approvingPerson) {
                body += "APPROVING PERSON:" + approvingPerson + "<br>";
            }
            
            // Half/Short leave type
            const halfShort = $('#half_short option:selected').text();
            if (halfShort && halfShort !== "Select") {
                body += "LEAVE DURATION:" + halfShort + "<br>";
            }
            
            // Add closing signature
            body += "<br>Regards,<br>";
            body += employeeName || "Employee";
            
            $('#emailBody').val(body);
            return body;
    }
    </script>

@endsection