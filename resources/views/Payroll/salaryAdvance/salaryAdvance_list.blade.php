@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.payroll_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-money-check-dollar-pen"></i></div>
                    <span>Salary Advances</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right mr-2" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div><br><br>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record" id="create_record">
                            <i class="fas fa-plus mr-2"></i>Salary Advances
                        </button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>EMP ID</th>
                                        <th>EMP NAME</th>
                                        <th>JOB CATEGORY</th>
                                        <th>DATE</th>
                                        <th>REQUESTED AMOUNT</th>
                                        <th>PAID AMOUNT</th>
                                        <th class="text-right">Action</th>
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

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
              <div class="offcanvas-header">
                  <h2 class="offcanvas-title font-weight-bolderer" id="offcanvasRightLabel">Records Filter Options</h2>
                  <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                      <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                  </button>
              </div>
              <div class="offcanvas-body">
                  <ul class="list-unstyled">
                      <form class="form-horizontal" id="formFilter">
                          <li class="mb-2">
                            <div class="col-md-12">
                                  <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                           <li class="mb-2">
                              <div class="col-md-12">
                                 <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm"></select>
                            </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> From Date* </label>
                                  <input type="date" id="from_date" name="from_date"
                                      class="form-control form-control-sm" placeholder="yyyy-mm-dd"
                                      value="{{date('Y-m-d') }}">
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> To Date*</label>
                                  <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                      placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}">
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

        <!-- Modal Area Start -->
        <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Salary Advance</h5>
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

                                    <div class="form-row mb-2">
                                        <div class="col-md-6">
                                            <label class="small font-weight-bolder text-dark">Employee*</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm" required>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small font-weight-bolder text-dark">Date*</label>
                                            <input type="date" name="date" id="date" class="form-control form-control-sm" required>
                                            <span id="date_error" class="text-danger small"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row mb-2">
                                        <div class="col-md-6">
                                            <label class="small font-weight-bold text-dark">Available Amount</label>
                                            <input type="number" name="available_amount" id="available_amount" class="form-control form-control-sm" placeholder="Available Amount" step="0.01" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small font-weight-bold text-dark">Request Amount*</label>
                                            <input type="number" name="request_amount" id="request_amount" class="form-control form-control-sm" placeholder="Request Amount" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="form-row mb-2">
                                        <div class="col-md-6">
                                            <label class="small font-weight-bold text-dark">Remarks</label>
                                            <input type="text" name="remark" id="remark" class="form-control form-control-sm" placeholder="Remarks">
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="paidformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="paidformModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="paidformModalLabel">Paid Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div id="paidform_result"></div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Paid Amount</label>
                                    <input type="number" class="form-control form-control-sm" id="paid_amount" name="paid_amount">
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" name="paid_button" id="paid_button" class="btn btn-primary btn-sm fa-pull-right px-4 paid_button">
                                        <i class="fas fa-plus"></i>&nbsp;Paid
                                    </button>
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
    
    $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#policymanagement').addClass('navbtnactive');

    // Initialize filter dropdowns
    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select a Company',
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
        placeholder: 'Select a Department',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                    location: location_f.val()
                }
            },
            cache: true
        }
    });

    employee_f.select2({
        placeholder: 'Select an Employee',
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
                    location: location_f.val(),
                    department: department_f.val()
                }
            },
            cache: true
        }
    });

    location_f.select2({
        placeholder: 'Select Location',
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

    // Initialize employee dropdowns in modal
    let employee = $("#employee").select2({
        placeholder: 'Select Employees',
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

    // Fetch available request_amount when employee is selected
    $('#employee').on('change', function () {
        var emp_id = $(this).val();
        $('#available_amount').val('');
        if (emp_id) {
            $.ajax({
                url: '{{ url("SalaryAdvance/available-amount") }}/' + emp_id,
                type: 'GET',
                success: function (data) {
                    $('#available_amount').val(data.available_amount);
                    $('#request_amount').attr('max', data.available_amount);
                }
            });
        }
    });

    // Prevent request_amount from exceeding available request_amount on client side
    $('#request_amount').on('input', function () {
        var available = parseFloat($('#available_amount').val()) || 0;
        var entered   = parseFloat($(this).val()) || 0;
        if (available > 0 && entered > available) {
            $(this).val(available);
        }
    });

    // Fetch available request_amount when employee or date changes
    function fetchAvailableAmount() {
        var emp_id = $('#employee').val();
        var date   = $('#date').val();
        $('#available_amount').val('');
        $('#request_amount').removeAttr('max');
        $('#date_error').text('');

        if (emp_id && date) {
            $.ajax({
                url: '{{ url("SalaryAdvance/available-amount") }}/' + emp_id,
                type: 'GET',
                data: { date: date },
                success: function (data) {
                    if (data.errors) {
                        $('#available_amount').val(0);
                        $('#request_amount').attr('max', 0);
                        $('#date_error').text(data.errors);
                    } else {
                        $('#date_error').text('');
                        $('#available_amount').val(data.available_amount);
                        $('#request_amount').attr('max', data.available_amount);
                    }
                }
            });
        }
    }

    $('#employee').on('change', fetchAvailableAmount);
    $('#date').on('change',     fetchAvailableAmount);

    function load_dt(company, department, employee, location, from_date, to_date) {
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Salary Advance Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Salary Advance Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Salary Advance Information',
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
                url: scripturl + '/salary_advance_list.php',
                type: "POST",
                data: {
                    company: company,
                    department: department,
                    employee: employee,
                    location: location,
                    from_date: from_date,
                    to_date: to_date
                },
            },
            columns: [
                { data: 'emp_id', name: 'emp_id' },
                { data: 'employee_display', name: 'employee_display' },
                { data: 'category', name: 'category' },
                { data: 'date', name: 'date' },
                { data: 'request_amount', name: 'request_amount' },
                { data: 'paid_amount', name: 'paid_amount' },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var paid_status = row.paid_status;
                        var approve_status = row.approve_status;
                        var buttons = '';

                        if (paid_status === '0') {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-primary btn-sm edit" id="' + row.id + '"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if (paid_status === '0' || paid_status === '1' && approve_status !== '1') {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Paid Details" class="btn btn-success btn-sm paid" id="' + row.id + '"><i class="fas fa-dollar-sign"></i></button>';
                        }
                        if (paid_status === '0' || paid_status === '1') {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-danger btn-sm delete" id="' + row.id + '"><i class="far fa-trash-alt"></i></button>';
                        }
                        return buttons;
                    }
                },
                { data: 'id', name: 'id' , visible: false},
                { data: "emp_name_with_initial", name: "emp_name_with_initial", visible: false},
                {   data: "calling_name",name: "calling_name", visible: false},
            ],
        });
    }

    load_dt('', '', '', '', '', '');

    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        let company = company_f.val() || '';
        let department = department_f.val() || '';
        let employee = employee_f.val() || '';
        let location = location_f.val() || '';
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        load_dt(company, department, employee, location, from_date, to_date);
        closeOffcanvasSmoothly();
    });

    $('#btn-reset').click(function() {
        $('#formFilter')[0].reset();
        company_f.val(null).trigger('change');
        department_f.val(null).trigger('change');
        employee_f.val(null).trigger('change');
        location_f.val(null).trigger('change');
        load_dt('', '', '', '', '', '');
    });

    $('#create_record').click(function(){
        $('.modal-title').text('Add Salary Advance Detail');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#employee').val(null).trigger('change');  
        $('#available_amount').val('');              
        $('#request_amount').removeAttr('max');             
        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();

        // Block submission if attendance validation failed
        if ($('#date_error').text().trim() !== '') {
            return false;
        }

        // Block submission if available request_amount is 0 or empty
        var available = parseFloat($('#available_amount').val()) || 0;
        if (available <= 0) {
            return false;
        }

        var action_url = '';
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addSalaryAdvance') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('salaryAdvance.update') }}";
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
                    actionreload(actionJSON);
                }
            }
        });
    });

    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $('#date_error').text('');

            $.ajax({
                url: "salaryAdvance/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    // Set date first
                    $('#date').val(data.result.date);

                    // Set employee option then trigger change with date already set
                    if ($('#employee').find('option[value="' + data.result.employee_id + '"]').length === 0) {
                        $('#employee').append('<option value="' + data.result.employee_id + '" selected>' + data.result.employee_name + '</option>');
                    }
                    $('#employee').val(data.result.employee_id).trigger('change');

                    $('#remark').val(data.result.remark);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Salary Advance Detail');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                    $('#action').val('Edit');

                    // Manually fetch available request_amount with both emp_id and date
                    $.ajax({
                        url: '{{ url("SalaryAdvance/available-amount") }}/' + data.result.employee_id,
                        type: 'GET',
                        data: { date: data.result.date },
                        success: function (res) {
                            if (res.errors) {
                                $('#available_amount').val(0);
                                $('#request_amount').attr('max', 0);
                                $('#date_error').text(res.errors);
                            } else {
                                $('#date_error').text('');
                                $('#available_amount').val(res.available_amount);
                                $('#request_amount').attr('max', res.available_amount);
                            }
                            // Set request_amount after available request_amount is ready
                            $('#request_amount').val(data.result.request_amount);
                        }
                    });

                    $('#formModal').modal('show');
                }
            });
        }
    });

    var user_id;

    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "{{ url('SalaryAdvance/destroy/') }}/" + user_id,
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

    flatpickr("#date", {
        allowInput: true,
        enable: [
            function(date) {
                return date.getDate() >= 25;
            }
        ]
    });

    // pay details 
    $(document).on('click', '.paid', function () {
        user_id = $(this).attr('id');

        $('#paidform_result').html('');
        $('#paidformModal').modal('show');
        $('#paid_amount').val(''); 

        $.ajax({
            url: '{!! route("SalaryAdvance/get-paid-amount") !!}',
            type: 'GET',
            data: { id: user_id },
            success: function (response) {
                if (response.paid_amount !== undefined && response.paid_amount !== null) {
                    $('#paid_amount').val(response.paid_amount);
                } else {
                    alert('Failed to fetch the paid amount.');
                }
            },
            error: function () {
                alert('An error occurred while fetching the paid amount.');
            }
        });
    });
    
    $(document).on('click', '.paid_button', async function () {
        var checkpaidamount = $('#paid_amount').val();
        if (checkpaidamount == '') {
            alert('Please Enter Amount');
        } else {
            var r = await Otherconfirmation("You want to paid this employee? ");
            if (r == true) {
                var paid_amount = $('#paid_amount').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{!! route("SalaryAdvance/paid-amount") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        recordID: user_id,
                        paid_amount: paid_amount,
                    },
                    success: function (data) {
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-check-circle',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'success'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                            $('#paidformModal').modal('hide');
                        } else if (data.errors) {
                            var html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                            $('#paidform_result').html(html);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }
        }
    });

});
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@endsection