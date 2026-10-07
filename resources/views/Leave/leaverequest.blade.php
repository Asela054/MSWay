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
                        <span>Leave Request</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Options</button>
                                </div>

                            
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-sm fa-pull-right px-3" 
                                    name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Leave Request
                            </button><br><br>

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
                                    <th>APPROVE STATUS</th>
                                    <th>LEAVE TYPE</th>
                                    <th>APPROVED LEAVE</th>
                                    <th>LEAVE APPROVE STATUS</th>
                                    <th class="text-right">ACTION</th>
                                    <th class="d-none">empname</th>
                                    <th class="d-none">empname</th>
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
                        <h5 class="modal-title" id="staticBackdropLabel">Add Leave Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
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
                                            <label class="small font-weight-bolder text-dark">Select Employee*</label>
                                            <select name="employee_f" id="employee_f" class="form-control form-control-sm" required>
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>
                                     
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bolder text-dark">From*</label>
                                            <input type="date" name="fromdate" id="fromdate"
                                                   class="form-control form-control-sm" placeholder="YYYY-MM-DD" required/>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label class="small font-weight-bolder text-dark">To*</label>
                                            <input type="date" name="todate" id="todate"
                                                   class="form-control form-control-sm" placeholder="YYYY-MM-DD" required/>
                                        </div>
                                    </div>

                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bolder text-dark">Half Day/ Short* <span id="half_short_span"></span> </label>
                                            <select name="half_short" id="half_short" class="form-control form-control-sm" required>
                                                <option value="0.00">Select</option>
                                                <option value="0.25">Short Leave</option>
                                                <option value="0.5">Half Day</option>
                                                <option value="1.00">Full Day</option>
                                            </select>
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
                                    <div class="form-row mb-1">
                                        <div class="col-sm-12 col-md-12">
                                            <label class="small font-weight-bolder text-dark">Reason</label>
                                            <input type="text" name="reason" id="reason" class="form-control form-control-sm"/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mt-3">
                                        <input type="submit" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-3" value="Add"/>
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
            let employee = $('#employee');
            let location = $('#location');


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
            employee.select2({
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
                            page: params.page || 1,
                             company: company_f.val(),
                             department: department_f.val(),
                             location: location.val()
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
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company_f.val()
                        }
                    },
                    cache: true
                }
            });

            let employee_f = $('#employee_f');
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

            function load_dt(department, employee, from_date, to_date){
                $('#divicestable').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        buttons: [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'Leave Request Details',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'Leave Request Details', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Leave Request Details',
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
                         url: scripturl + '/leave_request_list.php',
                        type: 'POST',
                        data : {'department':department, 'employee':employee, 'from_date': from_date, 'to_date': to_date},
                    },
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'employee_display', name: 'employee_display' },
                        { data: 'dep_name', name: 'dep_name' },
                        { 
                            data: 'leave_category', name: 'leave_category', render: function(data, type, row) {
                                if (data == 1) {
                                    return "Full Day";
                                } else if (data == 0.50) {
                                    return "Half Day";
                                } else if (data == 0.25) {
                                    return "Short Leave";
                                } else {
                                    return "";
                                }
                            }
                        },
                        { data: 'from_date', name: 'from_date' },
                        { data: 'to_date', name: 'to_date' },
                        { data: 'reason', name: 'reason'},
                        { 
                            data: 'approvestatus', name: 'approvestatus', render: function(data, type, row) {
                                if (data == 0) {
                                    return "Not Approved";
                                } else if (data == 2) {
                                    return "Rejected";
                                } else {
                                    return "Approved";
                                }
                            }
                        },
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
                                    return "";
                                }
                            }
                        },
                        { data: 'leave_status', name: 'leave_status' },
                         {
                        data: 'id',
                        name: 'action',
                        className: 'text-right',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var buttons = '';


                            if (row.approvestatus == 0 ) {
                                buttons += '<button type="submit" name="approve" id="'+row.id+'" class="approve btn btn-warning btn-sm" style="margin:1px;" data-toggle="tooltip" title="Approve" ><i class="fas fa-check"></i></button>';
                            }

                            if (row.approvestatus != 0 && row.approvestatus != 2) {
                                    buttons += '<button type="button" id="'+row.id+'" class="pdf-export btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Export PDF"><i class="fas fa-file-pdf"></i></button>';
                                }

                            if(row.approvestatus != 2){
                                buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm" style="margin:1px;" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                                buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Remove" ><i class="far fa-trash-alt"></i></button>';
                            }

                            return buttons;
                        }
                    }, 
                    { data: "emp_name_with_initial", 
                      name: "emp_name_with_initial", 
                      visible: false
                    },
                    {   data: "calling_name",
                        name: "calling_name", 
                        visible: false
                    }
                    ],
                    order: [[0, "desc"]],
                     drawCallback: function(settings) {
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                });
            }

            load_dt('', '', '', '');

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                load_dt(department, employee, from_date, to_date);

                closeOffcanvasSmoothly();

            });


            // calculate leave balance when employee selected
              $('#employee_f').change(function () {
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
                          data: {
                              status: status,
                              emp_id: emp_id,
                              leavetype: leavetype,
                              _token: _token,
                              fromdate: fromdate,
                              todate: todate
                          },
                          success: function (data) {
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

            // calculate leave balance when date range is selected
            $('#todate').change(function () {
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
                        data: {
                            status: status,
                            emp_id: emp_id,
                            leavetype: leavetype,
                            _token: _token,
                            fromdate: fromdate,
                            todate: todate
                        },
                        success: function (data) {
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

        });



        $(document).ready(function () {
            $('#create_record').click(function () {
                $('.modal-title').text('Add Leave Request');
                $('#action_button').val('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('leaverequestinsert') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('leaverequestupdate') }}";
                }


                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (data) {
                       if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: data.errors,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
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
                });
            });


            $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
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
                            let empOption = $("<option selected></option>").val(data.result.emp_id).text(data.result.emp_name);
                            $('#employee_f').append(empOption).trigger('change');
                            $('#employee_f').val(data.result.emp_id);
                            $('#fromdate').val(data.result.from_date);
                            $('#todate').val(data.result.to_date);
                            $('#half_short').val(data.result.leave_category);
                            $('#reason').val(data.result.reason);
                            $('#leavetype').val(data.result.leave_type);
                            $('#from_time').val(data.result.from_time);
                            $('#to_time').val(data.result.to_time);

                           setTimeout(function() {
                               document.getElementById('half_short').dispatchEvent(new Event('change'));
                                $('#employee_f').trigger('change');
                            }, 100);


                            $('#hidden_id').val(id);
                            $('.modal-title').text('Edit Leave Request');
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
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                        })
                    $.ajax({
                        url: '{!! route("leaverequestdelete") !!}',
                            type: 'POST',
                            dataType: "json",
                            data: {id: user_id },
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

            
            $(document).on('click', '.approve',async function () {
               var r = await Otherconfirmation("You want to Approve this ? ");
                if (r == true) {
                       user_id = $(this).attr('id');
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                            })
                        $.ajax({
                            url: '{!! route("leaverequestapprove") !!}',
                                type: 'POST',
                                dataType: "json",
                                data: {id: user_id },
                            beforeSend: function () {
                                $('#approve_button').text('Approving...');
                            },
                            success: function (data) {
                               
                                 if (data.errors) {
                                        const actionObj = {
                                            icon: 'fas fa-warning',
                                            title: '',
                                            message: 'Record Error',
                                            url: '',
                                            target: '_blank',
                                            type: 'danger'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        action(actionJSON);
                                    }
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
                                        actionreload(actionJSON);
                                    }
                            }
                        })
                }

            });

        });

        document.getElementById('half_short').addEventListener('change', function() {
            const timeRangeDiv = document.getElementById('short_leave_time_range');
            const fromTimeInput = document.getElementById('from_time');
            const toTimeInput = document.getElementById('to_time');
            
            if (this.value === '0.25') { // Short Leave selected
                timeRangeDiv.style.display = 'flex';
                fromTimeInput.required = true;
                toTimeInput.required = true;
            } else {
                timeRangeDiv.style.display = 'none';
                fromTimeInput.required = false;
                toTimeInput.required = false;
            
                fromTimeInput.value = '';
                toTimeInput.value = '';
            }
        });

        // ---------- helpers ----------
    function pdfParts(str) {
        if (!str) return ['', '', ''];
        return str.substring(0, 10).split('-');
    }

    function pdfAddDays(str, n) {
        var d = new Date(str.substring(0, 10) + 'T00:00:00');
        d.setDate(d.getDate() + n);
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var dd = ('0' + d.getDate()).slice(-2);
        return d.getFullYear() + '-' + m + '-' + dd;
    }

    function pdfLeaveDays(from, to, category) {
        var f = new Date(from.substring(0, 10) + 'T00:00:00');
        var t = new Date(to.substring(0, 10) + 'T00:00:00');
        var diff = Math.round((t - f) / 86400000) + 1;
        return diff * parseFloat(category || 1);
    }

    // ---------- main generator ----------
    function generateLeavePdf(d, row) {
        var jsPDFLib = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
        var doc = new jsPDFLib({ unit: 'mm', format: 'a4' });

        var THIN = 0.2, THICK = 0.7;
        var X0 = 10, X1 = 200;

        // ---------- drawing helpers ----------
        function box(x, y, w, h) {            // thin cell border
            doc.setLineWidth(THIN);
            doc.rect(x, y, w, h);
        }
        function thickLine(x1, y1, x2, y2) {  // thick main line
            doc.setLineWidth(THICK);
            doc.line(x1, y1, x2, y2);
        }
        function thickRect(x, y, w, h) {
            doc.setLineWidth(THICK);
            doc.rect(x, y, w, h);
        }
        function fitSize(t, maxW, size, bold, minSize) {
            doc.setFont('helvetica', bold ? 'bold' : 'normal');
            doc.setFontSize(size);
            while (size > minSize && doc.getTextWidth(t) > maxW) {
                size -= 0.5;
                doc.setFontSize(size);
            }
            return size;
        }

        function label(t, x, y, h) {          // left aligned, shrinks to fit
            t = String(t || '');
            fitSize(t, 31, 9, true, 6);
            doc.text(t, x + 2, y + h / 2, { baseline: 'middle' });
        }

        function cellText(t, x, y, w, h, size, bold) {   // centered, shrink then wrap
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
        function center(t, x, y, w, bold, size) {        // centered horizontally at y
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
        var empName   = d.emp_name || row.employee_display || '';
        var dept      = d.dep_name || row.dep_name || '';
        var empNo     = d.emp_no || d.emp_id || '';
        var desig     = d.designation || '';
        var section   = d.section || '';
        var location_ = d.location || '';
        var reqDate   = d.created_at ? d.created_at : new Date().toISOString();
        var days      = pdfLeaveDays(d.from_date, d.to_date, d.leave_category);
        var resume    = pdfAddDays(d.to_date, 1);

        // ---------- header ----------
        // doc.addImage(LOGO_BASE64, 'PNG', 12, 8, 30, 18); // optional logo
        center('AgroVentures Plantations (Pvt) Ltd', 0, 16, 210, true, 14);
        center('Leave Application Form', 0, 23, 210, true, 11);
        center('Office Staff', 0, 28, 210, true, 9);

        // ---------- top table (thin cells) ----------
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

        // ---------- left block (thin cells) ----------
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
        dateRow('Leave Start Date:', d.from_date, 77);
        dateRow('Date of Resuming Date:', resume, 86);

        box(X0, 95, 35, 30); label('Reason for Leave:', X0, 95, 30);
        box(45, 95, 35, 30);
        // reason: wrapped + centered inside the cell
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);
        var reasonLines = doc.splitTextToSize(d.reason || '', 31);
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
        var CW = 11; // type column width
        box(X0, 132, 35, 16); cellText('Leave Type', X0, 132, 35, 16, 9, true);
        for (var t = 0; t < types.length; t++) {
            var tx = 45 + t * CW;
            box(tx, 132, CW, 7); cellText(types[t], tx, 132, CW, 7, 8, true);
            box(tx, 139, CW, 9);
        }
        var cmX = 45 + types.length * CW;      // More Comments start (122)
        box(cmX, 132, X1 - cmX, 16);
        doc.setTextColor(150);
        cellText('More Comments', cmX, 132, X1 - cmX, 16, 8, false);
        doc.setTextColor(0);

        // tick leave type
        var lt = (row.leave_type || '').toLowerCase();
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

        doc.setFont('helvetica', 'normal'); doc.setFontSize(8);   // reset after bold text
        doc.text('...............................', 165, 160, { align: 'center' });
        center('Payroll Officer', 130, 168, 70, false, 8);

        // ---------- THICK main lines (drawn last, on top of thin ones) ----------
        thickRect(X0, 32, 190, 27);          // top table outer border
        thickLine(135, 32, 135, 59);         // Name | Department divider
        thickLine(X0, 59, X1, 59);           // under Designation row
        thickLine(80, 59, 80, 125);          // right edge of left block
        thickLine(X1, 59, X1, 125);          // right edge of signature block
        thickLine(80, 86, X1, 86);           // between signature rows
        thickLine(X0, 59, X0, 125);          // left edge of left block
        thickLine(X0, 125, X1, 125);         // above HR Department
        thickRect(X0, 125, 190, 7);          // HR Department bar
        thickRect(X0, 152, 190, 20);         // bottom "Leave Updated By" box
        thickLine(X0, 132, X0, 148);   // left
        thickLine(X1, 132, X1, 148);   // right
        thickLine(X0, 148, X1, 148);   // bottom

        doc.save('Leave_Application_' + (empNo || d.emp_id) + '_' + d.from_date + '.pdf');
    }

// ---------- click handler ----------
$(document).on('click', '.pdf-export', function () {
    var id = $(this).attr('id');
    var row = $('#divicestable').DataTable().row($(this).closest('tr')).data();

    $.ajax({
        url: '{!! route("leaverequestedit") !!}',
        type: 'POST',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { id: id },
        success: function (data) {
            generateLeavePdf(data.result, row);
        }
    });
});
    </script>

@endsection