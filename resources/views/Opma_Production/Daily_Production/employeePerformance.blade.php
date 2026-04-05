@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.production&task_nav_bar_opma')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-ballot-check"></i></div>
                    <span>Employee Performance</span>
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
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div><br><br>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record" id="create_record">
                            <i class="fas fa-plus mr-2"></i>Add Employee
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
                                        <th>AMOUNT</th>
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
                        <h5 class="modal-title" id="staticBackdropLabel">Add Employee Performance</h5>
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
                                            <label class="small font-weight-bold text-dark">Amount*</label>
                                            <input type="number" name="amount" id="amount" class="form-control form-control-sm" placeholder="Amount" step="0.01" required>
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
        <!-- Modal Area End -->

</main>

@endsection

@section('script')
<script>
$(document).ready(function () {
    
    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailymaster').addClass('navbtnactive');

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

    // Load DataTable
    function load_dt(company, department, employee, location) {
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Performance Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Performance Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Performance Information',
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
                url: scripturl + '/Opma_Production/employee_performance_list.php',
                type: "POST",
                data: {
                    company: company,
                    department: department,
                    employee: employee,
                    location: location
                },
            },
            columns: [
                { data: 'emp_id', name: 'emp_id' },
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                { data: 'amount', name: 'amount' },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Edit" class="btn btn-primary btn-sm edit" id="' + row.id + '"><i class="fas fa-pencil-alt"></i></button>' +
                               '<button style="margin:1px;" data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-danger btn-sm delete" id="' + row.id + '"><i class="far fa-trash-alt"></i></button>';
                    }
                }
            ],
        });
    }

    // Initial load
    load_dt('', '', '', '');

    // Filter functionality
    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        let company = company_f.val() || '';
        let department = department_f.val() || '';
        let employee = employee_f.val() || '';
        let location = location_f.val() || '';
        load_dt(company, department, employee, location);
        closeOffcanvasSmoothly();
    });

    $('#btn-reset').click(function() {
        $('#formFilter')[0].reset();
        company_f.val(null).trigger('change');
        department_f.val(null).trigger('change');
        employee_f.val(null).trigger('change');
        location_f.val(null).trigger('change');
        load_dt('', '', '', '');
    });

    $('#create_record').click(function(){
        $('.modal-title').text('Add Employee Performance');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();

        $('#employee').empty().trigger('change');

        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addopma_employee_performance') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('opma_employee_performance.update') }}";
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
        
        $('#company_f').empty().trigger('change');
        $('#department_f').empty().trigger('change');
        
        $.ajax({
            url: "opma_employee_performance/" + id + "/edit",
            dataType: "json",
            success: function (data) {
                $('#employee').append('<option value="' + data.result.emp_id + '" selected>' + data.result.employee_name + '</option>').trigger('change');
                $('#amount').val(data.result.amount);

                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Employee Performance');
                $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            }
            })
        }
    });

    var user_id;

    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "{{ url('opma_employee_performance/destroy/') }}/" + user_id,
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
</script>

@endsection