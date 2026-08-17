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
                    <span>Salary Incentive</span>
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
                    <div class="col-sm-12 col-md-12">
                        <div class="d-flex flex-wrap justify-content-end mb-2">
                            <div class="col-sm-12 col-md-auto mb-1 px-1">
                            <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="create_record" id="create_record">
                                <i class="fas fa-plus mr-2"></i>Salary Incentive
                            </button>
                            </div>
                            <div class="col-sm-12 col-md-auto mb-1 px-1">
                                <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="create_record_dept_wise" id="create_record_dept_wise">
                                    <i class="fas fa-plus mr-2"></i>Add - Department Wise
                                </button>
                            </div>
                        </div>
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
                                        <th>DEPARTMENT</th>
                                        <th>MONTH</th>
                                        <th>PAID AMOUNT</th>
                                        <th class="text-right">Action</th>
                                        <th class="d-none">ID</th>
                                        <th class="d-none">Emp Name With Initial</th>
                                        <th class="d-none">Calling Name</th>
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
                                  <label class="small font-weight-bold text-dark"> Month </label>
                                  <input type="month" name="month" id="month_f" class="form-control form-control-sm" placeholder="yyyy-mm">
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
                        <h5 class="modal-title" id="staticBackdropLabel">Add Salary Incentive</h5>
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
                                            <label class="small font-weight-bolder text-dark">Month*</label>
                                            <input type="month" name="month" id="month"  placeholder="yyyy-mm" class="form-control form-control-sm" required>
                                            <span id="month_error" class="text-danger small"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row mb-2">
                                        <div class="col-md-6">
                                            <label class="small font-weight-bold text-dark">Paid Amount*</label>
                                            <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-sm" placeholder="Paid Amount" step="0.01" required>
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

        <div class="modal fade" id="formModal_dpt" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Salary Incentive - Department Wise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formTitle_dpt" class="form-horizontal">
                                    {{ csrf_field() }}
                                    
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                                <label class="small font-weight-bold text-dark">Company <span class="text-danger">*</span> </label>
                                                <select name="company" id="company_dept_wise" class="form-control form-control-sm">
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-3">
                                                <label class="small font-weight-bold text-dark">Department <span class="text-danger">*</span> </label>
                                                <select name="department" id="department_dept_wise" class="form-control form-control-sm" required>
                                                </select>
                                            </div>
                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bolder">Month*</label>
                                            <input type="month" name="allocation_month" id="allocation_month"
                                                class="form-control form-control-sm" required />
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                                <label class="small font-weight-bold text-dark">&nbsp; </label> <br>
                                                <button type="button" name="search_button" id="search_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-search"></i>&nbsp;Search</button>
                                            </div>
                                        
                                    </div>
                                    <br>
                                    <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="dpt_allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;" class="text-center">
                                                    <input type="checkbox" id="check_all_dpt" title="Select / Deselect All">
                                                </th>
                                                <th>EMP ID</th>
                                                <th>NAME</th>
                                                <th>PAID AMOUNT</th>
                                                <th>REMARK</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dptemplistbody">
                                        </tbody>
                                    </table>
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="button" name="dptaction_button" id="dptaction_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
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
    
    $('#payrollmenu').addClass('active');
    $('#payrollmenu_icon').addClass('active');
    $('#advancesincentives').addClass('navbtnactive');

    // department wise salary advance modal
    let company_d = $('#company_dept_wise');
    let department_d = $('#department_dept_wise');

    company_d.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("company_list_sel2")}}',
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

    department_d.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_d.val()
                }
            },
            cache: true
        }
    });

    // Initialize filter dropdowns
    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select a Company',
        width: '100%',
        allowClear: true,
        dropdownParent: $('#offcanvasRight'),
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
        dropdownParent: $('#offcanvasRight'),
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
        dropdownParent: $('#offcanvasRight'),
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
        dropdownParent: $('#offcanvasRight'),
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

    // Auto-load existing paid_amount when employee + month both selected in formModal
    function loadExistingIncentive() {
        var emp_id = $('#employee').val();
        var month  = $('#month').val();
        if (!emp_id || !month || $('#action').val() === 'Edit') return;

        $.ajax({
            url: '{{ route("salaryIncentive.byEmpMonth") }}',
            type: 'GET',
            data: { emp_id: emp_id, month: month },
            dataType: 'json',
            success: function (data) {
                if (data.result) {
                    $('#paid_amount').val(data.result.paid_amount);
                    $('#remark').val(data.result.remark);
                    $('#hidden_id').val(data.result.id);
                    $('#action').val('Edit');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Update');
                } else {
                    $('#paid_amount').val('');
                    $('#remark').val('');
                    $('#hidden_id').val('');
                    $('#action').val('Add');
                    $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
                }
            }
        });
    }

    $('#employee').on('change', function() { loadExistingIncentive(); });
    $('#month').on('change', function() { loadExistingIncentive(); });

    function load_dt(company, department, employee, location, month) {
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Salary Incentive Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Salary Incentive Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Salary Incentive Information',
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
                url: scripturl + '/salary_incentive_list.php',
                type: "POST",
                data: {
                    company: company,
                    department: department,
                    employee: employee,
                    location: location,
                    month: month
                },
            },
            columns: [
                { data: 'emp_id', name: 'emp_id' },
                { data: 'employee_display', name: 'employee_display' },
                { data: 'department_name', name: 'department_name' },
                { data: 'month', name: 'month' },
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

                        if (approve_status !== '1') {
                            buttons += '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-primary btn-sm edit" id="' + row.id + '"><i class="fas fa-pencil-alt"></i></button>';
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

    load_dt('', '', '', '', '');


    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        let company = company_f.val() || '';
        let department = department_f.val() || '';
        let employee = employee_f.val() || '';
        let location = location_f.val() || '';
        let month = $('#month_f').val() || '';
        load_dt(company, department, employee, location, month);
        closeOffcanvasSmoothly();
    });

    $('#btn-reset').click(function() {
        $('#formFilter')[0].reset();
        company_f.val(null).trigger('change');
        department_f.val(null).trigger('change');
        employee_f.val(null).trigger('change');
        location_f.val(null).trigger('change');
        $('#month_f').val('');
        load_dt('', '', '', '', '');
    });

    $('#create_record').click(function(){
        $('.modal-title').text('Add Salary Incentive Detail');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#employee').val(null).trigger('change');  
        $('#paid_amount').removeAttr('max');             
        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();

        var action_url = '';
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addSalaryIncentive') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('salaryIncentive.update') }}";
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

            $.ajax({
                url: "salaryIncentive/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#month').val(data.result.month);

                    if ($('#employee').find('option[value="' + data.result.employee_id + '"]').length === 0) {
                        $('#employee').append('<option value="' + data.result.employee_id + '" selected>' + data.result.employee_name + '</option>');
                    }
                    $('#employee').val(data.result.employee_id).trigger('change');

                    $('#paid_amount').val(data.result.paid_amount);
                    $('#remark').val(data.result.remark);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Salary Incentive Detail');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                    $('#action').val('Edit');

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
                url: "{{ url('salaryIncentive/destroy/') }}/" + user_id,
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

    // Department wise employee list
    $('#create_record_dept_wise').click(function () {
        $('#dptemplistbody').empty();
        $('#formModal_dpt').modal('show');
    });

    $('#search_button').click(function () {
        var allocation_month = $('#allocation_month').val();
        var department      = $('#department_dept_wise').val();
        var company         = $('#company_dept_wise').val();

        if (!company || !department) {
            Swal.fire({ icon: 'warning', title: 'Please select Company and Department', timer: 2000, showConfirmButton: false });
            return;
        }
        if (!allocation_month) {
            Swal.fire({ icon: 'warning', title: 'Please select a Month', timer: 2000, showConfirmButton: false });
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-1"></i> Searching...');
        $('#check_all_dpt').prop('checked', false);

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: '{!! route("salary_incentive_dept_allocation_list") !!}',
            data: {
                _token: '{{ csrf_token() }}',
                company: company,
                department: department,
                allocation_month: allocation_month,
            },
            success: function (data) {
                if (data.error) {
                    Swal.fire({ icon: 'error', title: data.error, timer: 2500, showConfirmButton: false });
                    return;
                }

                var employees = data.employees || [];
                var html = '';

                if (employees.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted">No employees found for the selected department.</td></tr>';
                } else {
                    $.each(employees, function (i, emp) {
                        var hasRecord  = emp.has_record;
                        var paidAmt    = emp.paid_amount || '';
                        var remark     = emp.remark || '';
                        var checked    = hasRecord ? 'checked' : '';
                        var disabled   = hasRecord ? '' : 'disabled';

                        html += '<tr data-emp-id="' + emp.emp_id + '">';
                        html += '  <td class="text-center">';
                        html += '    <input type="checkbox" class="dpt-emp-chk" data-emp-id="' + emp.emp_id + '" ' + checked + '>';
                        html += '  </td>';
                        html += '  <td>' + emp.emp_id + '</td>';
                        html += '  <td>' + emp.emp_name_with_initial + '</td>';
                        html += '  <td><input type="number" class="form-control form-control-sm dpt-paid-amt" step="0.01" min="0" placeholder="Paid Amount" value="' + paidAmt + '" ' + disabled + '></td>';
                        html += '  <td><input type="text" class="form-control form-control-sm dpt-remark" placeholder="Remark" value="' + remark + '" ' + disabled + '></td>';
                        html += '</tr>';
                    });
                }

                $('#dptemplistbody').html(html);
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Failed to load employees.', timer: 2500, showConfirmButton: false });
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-search"></i>&nbsp;Search');
            }
        });
    });

    $('#check_all_dpt').on('change', function () {
        var isChecked = $(this).is(':checked');
        $('#dptemplistbody .dpt-emp-chk').each(function () {
            var $chk = $(this);
            var $row = $chk.closest('tr');
            $chk.prop('checked', isChecked);
            $row.find('.dpt-paid-amt, .dpt-remark').prop('disabled', !isChecked);
            if (!isChecked) {
                $row.find('.dpt-paid-amt').val('');
                $row.find('.dpt-remark').val('');
            }
        });
    });

    $(document).on('change', '.dpt-emp-chk', function () {
        var $chk   = $(this);
        var $row   = $chk.closest('tr');
        var $paid  = $row.find('.dpt-paid-amt');
        var $rem   = $row.find('.dpt-remark');

        if ($chk.is(':checked')) {
            $paid.prop('disabled', false);
            $rem.prop('disabled', false);
        } else {
            $paid.prop('disabled', true).val('');
            $rem.prop('disabled', true).val('');
        }
    });


    $('#dptaction_button').click(function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing');

        var tbody = $('#dptemplistbody');

        if (tbody.children().length === 0) {
            Swal.fire({ position: 'top-end', icon: 'warning', title: 'Table is empty. Please search first.', showConfirmButton: false, timer: 2500 });
            $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            return;
        }

        var jsonObj        = [];
        var validationFail = [];
        var allocation_month = $('#allocation_month').val();

        // Collect ONLY checked rows
        $('#dptemplistbody .dpt-emp-chk:checked').each(function () {
            var $row           = $(this).closest('tr');
            var emp_id         = $row.data('emp-id');
            var paid_amount   = parseFloat($row.find('.dpt-paid-amt').val()) || 0;
            var remark           = $row.find('.dpt-remark').val() || '';

            if (paid_amount <= 0) {
                validationFail.push('Employee ' + emp_id + ': Please enter a paid amount.');
                return; // continue each
            }

            jsonObj.push({
                emp_id:           emp_id,
                paid_amount:      paid_amount,
                remark:           remark
            });
        });

        if (validationFail.length > 0) {
            Swal.fire({ icon: 'error', title: 'Validation Errors', html: validationFail.join('<br>') });
            $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            return;
        }

        if (jsonObj.length === 0) {
            Swal.fire({ position: 'top-end', icon: 'warning', title: 'No rows selected!', text: 'Please tick at least one employee checkbox.', showConfirmButton: false, timer: 2500 });
            $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            return;
        }

        var department = $('#department_dept_wise').val();

        $.ajax({
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                tableData: jsonObj,
                department: department,
                allocation_month: allocation_month,
            },
            url: '{!! route("salary_incentive_dept_allocation_insert") !!}',
            success: function (data) {
                if (data.errors) {
                    const actionObj = { icon: 'fas fa-warning', title: '', message: data.errors, url: '', target: '_blank', type: 'danger' };
                    action(JSON.stringify(actionObj, null, 2));
                    $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
                    return;
                }
                if (data.success) {
                    // Show any per-row errors if some rows were skipped
                    if (data.row_errors && data.row_errors.length > 0) {
                        Swal.fire({ icon: 'warning', title: 'Saved with warnings', html: data.row_errors.join('<br>') });
                    }
                    const actionObj = { icon: 'fas fa-save', title: '', message: data.success, url: '', target: '_blank', type: 'success' };
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server error. Please try again.' });
                $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            }
        });
    });

});
</script>

@endsection