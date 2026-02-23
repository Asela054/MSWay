@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.meter_reading_nav_bar')
        </div>
    <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-tachometer-alt"></i></div>
                    <span>Meter Reading Count</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <!-- Data Table Card -->
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
                            <i class="fas fa-plus mr-2"></i>Add Meter Reading
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm fa-pull-right mr-2" name="csv_upload" id="csv_upload">
                            <i class="fas fa-upload mr-2"></i>CSV Upload
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
                                        <th>DEPARTMENT</th>
                                        <th>DATE</th>
                                        <th>COUNT</th>
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
                                      value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> To Date*</label>
                                  <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                      placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
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

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Meter Reading Count</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="employee" id="employee" class="form-control form-control-sm" required></select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Count*</label>
                                        <input type="number" name="count" id="count" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="col-6">
                                        <button type="button" id="formsubmit" class="btn btn-primary btn-sm px-4 float-right">
                                            <i class="fas fa-plus"></i>&nbsp;Add
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                        
                        <!-- Preview Table -->
                        <div class="col-12 mt-3">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                        <th>Date</th>
                                        <th>Count</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableorderlist"></tbody>
                            </table>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder" class="btn btn-primary btn-sm fa-pull-right px-4">
                                    <i class="fas fa-save"></i>&nbsp;Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSV Upload Modal -->
    <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="csvmodal-title" id="staticBackdropLabel1">Upload CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="upload_response"></div>
                    <div class="row">
                        <div class="col">
                            <a href="{{ url('/public/csvsample/Meter Reading Count.csv') }}" class="control-label d-flex justify-content-end">
                                CSV Format - Download Sample File
                            </a>
                        </div>
                    </div>
                    <form method="post" id="formUpload" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input required type="date" id="date_u" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">CSV File</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="btn-upload" class="btn btn-primary btn-sm fa-pull-right px-4">
                                        <i class="fas fa-upload"></i>&nbsp;Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                    <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Edit Modal -->
    <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="editModalLabel">Edit Meter Reading</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="edit_form_result"></div>
                    <form id="editForm">
                        <div class="form-group">
                            <label class="small font-weight-bold text-dark">Date*</label>
                            <input type="date" name="edit_date" id="edit_date" class="form-control form-control-sm" required />
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-dark">Employee*</label>
                            <input type="text" id="edit_employee_name" class="form-control form-control-sm" readonly />
                            <input type="hidden" name="edit_employee_id" id="edit_employee_id" />
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-dark">Count*</label>
                            <input type="number" name="edit_count" id="edit_count" class="form-control form-control-sm" required min="0" />
                        </div>
                        <input type="hidden" id="edit_record_id" />
                    </form>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" id="updateRecord" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i>&nbsp;Update
                    </button>
                </div>
            </div>
        </div>
    </div>

</main>

@endsection

@section('script')
<script>
$(document).ready(function () {
    
    $('#meter_reading_menu_link').addClass('active');
    $('#meter_reading_menu_link_icon').addClass('active');
    $('#meterreading').addClass('navbtnactive');

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

    // Initialize employee dropdown in modal
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

    // Date change handler
    $('#date').on('change', function() {
        if ($(this).val()) {
            $('#employee').prop('disabled', false);
            $('#employee').val(null).trigger('change'); 
        } else {
            $('#employee').prop('disabled', true);
            $('#employee').val(null).trigger('change'); 
        }
    });

    // Load DataTable
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
                    title: 'Employee Meter reading Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Meter reading Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Meter reading Information',
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
                url: scripturl + '/meter_reading_list.php',
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
                { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                { data: 'department_name', name: 'department_name' },
                { data: 'date', name: 'date' },
                { data: 'count', name: 'count' },
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
    load_dt('', '', '', '', '', '');

    // Filter functionality
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

    // Create new record
    $('#create_record').click(function () {
        $('.modal-title').text('Add Meter Reading Count');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
        $('#tableorder > tbody').html('');
        $('#hidden_id').val('');
        $('#employee').prop('disabled', true);
        $('#formModal').modal('show');
    });

    // Add to list functionality
    $("#formsubmit").click(function () {
        let employee = $('#employee').val();
        let date = $('#date').val();
        let count = $('#count').val();
        
        if (!employee) {
            Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Please select an employee',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }
        
        if (!date || !count) {
            Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Please fill in all required fields',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }
        
        let empName = $('#employee option[value="' + employee + '"]').text();
        let existingRow = false;
        
        // Check if employee already exists in table
        $('#tableorder tbody tr').each(function() {
            if ($(this).find('td:first').text() === employee) {
                existingRow = true;
                return false;
            }
        });
        
        if (!existingRow) {
            $('#tableorder > tbody:last').append('<tr class="pointer">' +
                '<td>' + employee + '</td>' +
                '<td>' + empName + '</td>' +
                '<td>' + date + '</td>' +
                '<td>' + count + '</td>' +
                '<td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>' +
                '</tr>');
        } else {
            Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'This employee is already added to the list',
                showConfirmButton: false,
                timer: 2500
            });
        }
        
        $('#employee').val('').trigger('change');
        $('#count').val('');
    });

    // Save/Update functionality
    $('#btncreateorder').click(function () {
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('meter_reading_insert') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('meter_reading_update') }}";
        }

        $('#btncreateorder').prop('disabled', true).html(
            '<i class="fas fa-circle-notch fa-spin mr-2"></i> Saving');

        var tbody = $("#tableorder tbody");

        if (tbody.children().length > 0) {
            var jsonObj = [];
            $("#tableorder tbody tr").each(function () {
                var item = {};
                $(this).find('td').each(function (col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObj.push(item);
            });

            var hidden_id = $('#hidden_id').val();

            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    hidden_id: hidden_id,
                },
                url: action_url,
                success: function (data) {
                    var html = '';
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
                    $('#form_result').html(html);
                    $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
                },
                error: function(xhr) {
                    var html = '<div class="alert alert-danger">An error occurred while saving</div>';
                    $('#form_result').html(html);
                    $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-save"></i>&nbsp;Save');
                }
            });
        } else {
             Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Cannot Create..Table Empty!',
                showConfirmButton: false,
                timer: 2500
            });
            $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
        }
    });

    // Edit functionality
    $(document).on('click', '.edit',async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
         if (r == true) {
        var id = $(this).attr('id');
        
        $.ajax({
            url: '{{ route("meter_reading_edit") }}',
            type: 'POST',
            dataType: "json",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                $('#edit_date').val(data.result.mainData.date);
                $('#edit_count').val(data.result.mainData.count);
                $('#edit_employee_id').val(data.result.mainData.emp_id);
                $('#edit_employee_name').val(data.result.mainData.employee ? data.result.mainData.employee.emp_name_with_initial : data.result.mainData.emp_id);
                $('#edit_record_id').val(id);
                $('#edit_form_result').html('');
                $('#editModal').modal('show');
            }
            })
         }
    });

    $('#updateRecord').click(function() {
        let updateBtn = $(this);
        let originalText = updateBtn.html();
        
        updateBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        let tableData = [{
            col_1: $('#edit_employee_id').val(),
            col_2: $('#edit_employee_name').val(), 
            col_3: $('#edit_date').val(),
            col_4: $('#edit_count').val()
        }];
        
        $.ajax({
            url: '{{ route("meter_reading_update") }}',
            type: 'POST',
            dataType: "json",
            data: {
                _token: '{{ csrf_token() }}',
                tableData: tableData,
                hidden_id: $('#edit_record_id').val()
            },
            success: function(data) {
                if (data.success) {
                    $('#editModal').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    Swal.fire({
                        position: "top-end",
                        icon: 'success',
                        title: data.success,
                        showConfirmButton: false,
                        timer: 2500
                    });
                } else if (data.errors) {
                    let html = '<div class="alert alert-danger">';
                    data.errors.forEach(function(error) {
                        html += '<p>' + error + '</p>';
                    });
                    html += '</div>';
                    $('#edit_form_result').html(html);
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while updating';
                Swal.fire({
                    position: "top-end",
                    icon: 'error',
                    title: errorMsg,
                    showConfirmButton: false,
                    timer: 2500
                });
            },
            complete: function() {
                updateBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // CSV Upload functionality
    $('#csv_upload').click(function() {
        $('#uploadAtModal').modal('show');
        $('#upload_response').html('');
    });

    $('#date_u').on('change', function() {
        if ($(this).val()) {
            $('#csv_file_u').prop('disabled', false);
        } else {
            $('#csv_file_u').prop('disabled', true);
            $('#csv_file_u').val('');
        }
    });

    if (!$('#date_u').val()) {
        $('#csv_file_u').prop('disabled', true);
    }

    $('#formUpload').on('submit', function(e) {
        e.preventDefault();
        let save_btn = $("#btn-upload");
        let btn_prev_text = save_btn.html();
        
        save_btn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        let formData = new FormData($('#formUpload')[0]);
        
        $.ajax({
            url: '{{ route("meter_reading_upload_csv") }}',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function(res) {
                if (res.status) {
                    let successHtml = `<div class='alert alert-success'>${res.msg}</div>`;
                    
                    if (res.errors && res.errors.length > 0) {
                        let errorHtml = '<div class="alert alert-warning mt-2"><strong>Some issues occurred:</strong><ul>';
                        res.errors.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                        errorHtml += '</ul></div>';
                        successHtml += errorHtml;
                    }
                    
                    $('#upload_response').html(successHtml);
                    
                    if (!res.errors || res.errors.length === 0) {
                        $("#formUpload")[0].reset();
                        setTimeout(function() {
                            $('#uploadAtModal').modal('hide');
                            Swal.fire({
                                position: "top-end",
                                icon: 'success',
                                title: res.msg,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }, 2000);
                    }
                } else {
                    let html = '<div class="alert alert-danger">';
                    if (res.errors && Array.isArray(res.errors)) {
                        html += '<strong>Errors occurred:</strong><ul>';
                        res.errors.forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += res.msg || 'Something went wrong. Please check your file.';
                    }
                    html += '</div>';
                    $('#upload_response').html(html);
                }
                
                save_btn.html(btn_prev_text);
                $('#uploadAtModal').scrollTop(0);
                $('#dataTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Something went wrong. Please check your file.';
                Swal.fire({
                    position: "top-end",
                    icon: 'error',
                    title: errorMessage,
                    showConfirmButton: false,
                    timer: 2500
                });
                save_btn.html(btn_prev_text);
            }
        });
    });

    // Delete functionality
    var user_id;

    $(document).on('click', '.delete', function () {
    user_id = $(this).attr('id');

    Swal.fire({
        position: "top-end",
        title: 'Are you sure?',
        text: 'You want to remove this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0d0d0e',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("meter_reading_delete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
                },
                success: function (data) {
                    $('#dataTable').DataTable().ajax.reload();
                    Swal.fire({
                        position: "top-end",
                        icon: 'success',
                        title: 'Record deleted successfully',
                        showConfirmButton: false,
                        timer: 2500
                    });
                },
                error: function () {
                    Swal.fire({
                        position: "top-end",
                        icon: 'error',
                        title: 'Error deleting record',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        }
    });
});

});

// Helper functions
function productDelete(row) {
    $(row).closest('tr').remove();
}
</script>

@endsection