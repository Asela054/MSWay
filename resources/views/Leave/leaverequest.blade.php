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
                                                    <td> <span>Weekly</span> </td>
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
                                              <label class="small font-weight-bolder text-dark">Leave Type *</label>
                                              <select name="leavetype" id="leavetype"
                                                  class="form-control form-control-sm"required>
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
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
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
                    
                                buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm" style="margin:1px;" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                                buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Remove" ><i class="far fa-trash-alt"></i></button>';
                            

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

    </script>

@endsection