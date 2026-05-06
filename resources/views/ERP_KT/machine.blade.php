@extends('layouts.app')

@section('content')

<main> 
   <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                 @include('ERP_KT.erp_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-industry"></i></div>
                        <span>Machine</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record"
                        id="create_record"><i class="fas fa-plus mr-2"></i>Add Machine</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>MACHINE NAME</th>
                                    <th>MACHINE TYPE</th>
                                    <th>HELPER RATE</th>
                                    <th>OPERATOR RATE</th>
                                    <th>STATUS</th>
                                    <th>DATE</th>
                                    <th>REMARKS</th>
                                    <th class="text-right">ACTION</th>
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
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Machine</h5>
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
                                <input type="hidden" name="action" id="action" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="detailsid" id="detailsid" />
                                
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 ">
                                        <label class="small font-weight-bold ">Machine Name <span class="text-red">*</span></label>
                                        <input type="text" name="machine_name" id="machine_name" class="form-control form-control-sm" required/>
                                        <input type="hidden" name="machine_name_hidden" id="machine_name_hidden" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold">Machine Type</label>
                                        <input type="text" name="machine_type" id="machine_type" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold">Date</label>
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold ">Helper Rate</label>
                                         <input type="number" step="0.01" name="helper_rate" id="helper_rate" class="form-control form-control-sm" />
                                    </div>    
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold">Operator Rate</label>
                                        <input type="number" step="0.01" name="operator_rate" id="operator_rate" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold">Status</span></label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <option value="1">Active</option>
                                            <option value="3">Maintenance</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-8 ">
                                        <label class="small font-weight-bold ">Remarks</label>
                                        <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" />
                                    </div>

                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->  
     
       <!-- Add Operator Modal Start-->
    <div class="modal fade" id="operatorModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="operatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="operatorModalLabel">Add Operator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="operator_emp_form_result"></span>
                            <form method="post" id="operatorEmpFormTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <input type="hidden" name="operator_emp_action" id="operator_emp_action" />
                                <input type="hidden" name="operator_emp_hidden_id" id="operator_emp_hidden_id" />
                                <input type="hidden" name="operator_detailsid" id="operator_detailsid" />

                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="operator_emp_machine" id="operator_emp_machine" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            @foreach ($machines as $m)
                                                <option value="{{ $m->id }}">{{ $m->machine_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="operator_emp_machine" id="operator_emp_machine_hidden" />
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Operator <span class="text-red">*</span></label>
                                        <select class="form-control form-control-sm" name="operator_employee" id="operator_employee" style="width:100%" required></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <button type="button" id="operator_addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                </div>

                                <br>
                                <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="operator_allocationtbl" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>OPERATOR NAME</th>
                                            <th style="white-space: nowrap;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="operator_emplistbody">
                                    </tbody>
                                </table>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" name="operator_emp_action_button" id="operator_emp_action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
           <!-- Add Operator Modal End-->

    <!-- Add Helper Modal Start-->
    <div class="modal fade" id="helperModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="helperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="helperModalLabel">Add Helper</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="helper_emp_form_result"></span>
                            <form method="post" id="helperEmpFormTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <input type="hidden" name="helper_emp_action" id="helper_emp_action" />
                                <input type="hidden" name="helper_emp_hidden_id" id="helper_emp_hidden_id" />
                                <input type="hidden" name="helper_detailsid" id="helper_detailsid" />

                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="helper_emp_machine" id="helper_emp_machine" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            @foreach ($machines as $m)
                                                <option value="{{ $m->id }}">{{ $m->machine_name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="helper_emp_machine" id="helper_emp_machine_hidden" />
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Helper<span class="text-red">*</span></label>
                                        <select class="form-control form-control-sm" name="helper_employee" id="helper_employee" style="width:100%" required></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <button type="button" id="helper_addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                </div>

                                <br>
                                <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="helper_allocationtbl" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>HELPER NAME</th>
                                            <th style="white-space: nowrap;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="helper_emplistbody">
                                    </tbody>
                                </table>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" name="helper_emp_action_button" id="helper_emp_action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
           <!-- Add Helper Modal End-->
</main>
              
@endsection

@section('script')
<script>

$(document).ready(function(){
    $('#erp_menu_link_KT').addClass('active');
   $('#erp_menu_link_KT_icon').addClass('active');
   $('#erp_kt_master').addClass('navbtnactive');

  // Employee Select2 Initialization
    let operator_employee = $('#operator_employee');
    operator_employee.select2({
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

    let helper_employee = $('#helper_employee');
    helper_employee.select2({
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

    // Modal close handlers
    $('#viewconfirmModal .close').click(function(){
        $('#viewconfirmModal').modal('hide');
    });


    // DataTable initialization
    $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Machine Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Machine Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Machine Information',
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
            url: scripturl + "/ERP_KT/machine_list.php",
            type: "POST",
            data: {},
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'machine_name',
                name: 'machine_name'
            },
            {
                data: 'machine_type',
                name: 'machine_type'
            },
             {
                data: 'helper_rate',
                name: 'helper_rate'
            },
             {
                data: 'operator_rate',
                name: 'operator_rate'
            },
             {
                data: 'status',
                name: 'status',
                render: function (data, type, row) {
                    if (data == 1) return 'Active';
                    if (data == 3) return 'Maintenance';
                    if (data == 2) return 'Inactive';
                    return 'Unknown';
    }
            },
             {
                data: 'date',
                name: 'date'
            },
            {
                data: 'remarks',
                name: 'remarks'
            },
            {
                data: 'action',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var buttons = '';

                    // buttons += '<button type="submit" name="addoperator" id="'+row.id+'" class="view-operator btn btn-info btn-sm mr-1" data-toggle="tooltip" title="Operators"><i class="fas fa-user-shield"></i></button>';

                    // buttons += '<button type="submit" name="addhelper" id="'+row.id+'" class="view-helper btn btn-info btn-sm mr-1" data-toggle="tooltip" title="Helpers"><i class="fas fa-user-plus"></i></button>';

                    buttons += ' <button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                    buttons += '<button name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="far fa-trash-alt"></i></button>';

                      return buttons;
                }
            },
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Create record button
    $('#create_record').click(function () {
        $('.modal-title').text('Add Machine');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#formModal').modal('show');
    });

    // Form submission
    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';
        
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('kt_addMachine') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('KTMachine.update') }}";
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

    // Edit function
    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            
            $.ajax({
                url: "{{ url('KTMachine/') }}/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#machine_name').val(data.result.machine_name);
                    $('#machine_type').val(data.result.machine_type);
                    $('#helper_rate').val(data.result.helper_rate);
                    $('#operator_rate').val(data.result.operator_rate);
                    $('#status').val(data.result.status);
                    $('#date').val(data.result.date);
                    $('#remarks').val(data.result.remarks);
                    $('#hidden_id').val(id);

                    $('.modal-title').text('Edit Machine');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Update');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
         }
    });

    // Delete main record
    var user_id;
    $(document).on('click', '.delete', async function () {
        user_id = $(this).attr('id');
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            $.ajax({
                url: "{{ url('KTMachine/destroy/') }}/" + user_id,
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

    //Operator Assign
    
    var operatorCurrentMachineId = null;
    var operatorEmpList = [];
    var operatorDeleteList = []; 

    $(document).on('click', '.view-operator', function () {
        operatorCurrentMachineId = $(this).attr('id');
        operatorEmpList = [];
        operatorDeleteList = [];
        $('#operator_emplistbody').empty();

        $('#operator_emp_machine').val(operatorCurrentMachineId);
        $('#operator_emp_machine_hidden').val(operatorCurrentMachineId);

        $.ajax({
            url: '{{ url("KTMachine") }}/' + operatorCurrentMachineId + '/operators',
            dataType: 'json',
            success: function (data) {
                $.each(data.employees, function (i, emp) {
                    appendSavedOperator(emp);
                });
            }
        });

        $('#operatorModal').modal('show');
    });

    $('#operator_emp_action_button').click(function () {
     if (operatorEmpList.length === 0 && operatorDeleteList.length === 0) {
            alert('No changes to save.');
            return;
        }

        var requests = [];

        $.each(operatorDeleteList, function (i, rowId) {
            requests.push(
                $.ajax({
                    url: '{{ url("KTMachine/destroyOperator") }}/' + rowId,
                    dataType: 'json'
                })
            );
        });

        if (operatorEmpList.length > 0) {
            var empIds = operatorEmpList.map(function (e) { return e.emp_id; });
            requests.push(
                $.ajax({
                    url: "{{ route('KTMachine.storeOperators') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        machine_id: operatorCurrentMachineId,
                        employees: empIds
                    },
                    dataType: 'json'
                })
            );
        }

        $.when.apply($, requests).then(function () {
            operatorEmpList = [];
            operatorDeleteList = [];

            const actionObj = {
                icon: 'fas fa-save',
                title: '',
                message: 'Changes saved successfully.',
                url: '',
                target: '_blank',
                type: 'success'
            };
            action(JSON.stringify(actionObj));

            $('#operatorModal').modal('hide');   
        });
    });

    function appendSavedOperator(emp) {
        var row = '<tr id="operator_saved_row_' + emp.id + '">' +
            '<td>' + emp.emp_id + '</td>' +
            '<td>' + emp.emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-saved-operator" data-id="' + emp.id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#operator_emplistbody').append(row);
    }

    $(document).on('click', '.remove-saved-operator', function () {
        var rowId = $(this).data('id');

        var alreadyMarked = operatorDeleteList.indexOf(rowId) !== -1;
        if (alreadyMarked) {
            operatorDeleteList = operatorDeleteList.filter(function (id) { return id !== rowId; });
            $('#operator_saved_row_' + rowId).css('opacity', '1');
            $(this).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            operatorDeleteList.push(rowId);
            $('#operator_saved_row_' + rowId).css('opacity', '0.4');
            $(this).removeClass('btn-danger').addClass('btn-secondary');
        }
    });

    $('#operator_addtolist').click(function () {
        var empSelect = $('#operator_employee');
        var emp_id   = empSelect.val();
        var emp_name = empSelect.find('option:selected').text();

        if (!emp_id) {
            alert('Please select an employee.');
            return;
        }

        var duplicate = operatorEmpList.some(function (e) { return e.emp_id == emp_id; });
        if (duplicate) {
            alert('Employee already in the list.');
            return;
        }

        operatorEmpList.push({ emp_id: emp_id, emp_name: emp_name });

        var row = '<tr id="operator_staging_row_' + emp_id + '">' +
            '<td>' + emp_id + '</td>' +
            '<td>' + emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-staging-operator" data-empid="' + emp_id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#operator_emplistbody').append(row);

        empSelect.val(null).trigger('change');
    });

    $(document).on('click', '.remove-staging-operator', function () {
        var emp_id = $(this).data('empid');
        operatorEmpList = operatorEmpList.filter(function (e) { return e.emp_id != emp_id; });
        $('#operator_staging_row_' + emp_id).remove();
    });
    
    //Helper Assign
    
    var helperCurrentMachineId = null;
    var helperEmpList = [];
    var helperDeleteList = []; 

    $(document).on('click', '.view-helper', function () {
        helperCurrentMachineId = $(this).attr('id');
        helperEmpList = [];
        helperDeleteList = [];
        $('#helper_emplistbody').empty();

        $('#helper_emp_machine').val(helperCurrentMachineId);
        $('#helper_emp_machine_hidden').val(helperCurrentMachineId);

        $.ajax({
            url: '{{ url("KTMachine") }}/' + helperCurrentMachineId + '/helpers',
            dataType: 'json',
            success: function (data) {
                $.each(data.employees, function (i, emp) {
                    appendSavedHelper(emp);
                });
            }
        });

        $('#helperModal').modal('show');
    });

    $('#helper_emp_action_button').click(function () {
     if (helperEmpList.length === 0 && helperDeleteList.length === 0) {
            alert('No changes to save.');
            return;
        }

        var requests = [];

        $.each(helperDeleteList, function (i, rowId) {
            requests.push(
                $.ajax({
                    url: '{{ url("KTMachine/destroyHelper") }}/' + rowId,
                    dataType: 'json'
                })
            );
        });

        if (helperEmpList.length > 0) {
            var empIds = helperEmpList.map(function (e) { return e.emp_id; });
            requests.push(
                $.ajax({
                    url: "{{ route('KTMachine.storeHelpers') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        machine_id: helperCurrentMachineId,
                        employees: empIds
                    },
                    dataType: 'json'
                })
            );
        }

        $.when.apply($, requests).then(function () {
            helperEmpList = [];
            helperDeleteList = [];

            const actionObj = {
                icon: 'fas fa-save',
                title: '',
                message: 'Changes saved successfully.',
                url: '',
                target: '_blank',
                type: 'success'
            };
            action(JSON.stringify(actionObj));

            $('#helperModal').modal('hide');   
        });
    });

    function appendSavedHelper(emp) {
        var row = '<tr id="helper_saved_row_' + emp.id + '">' +
            '<td>' + emp.emp_id + '</td>' +
            '<td>' + emp.emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-saved-helper" data-id="' + emp.id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#helper_emplistbody').append(row);
    }

    $(document).on('click', '.remove-saved-helper', function () {
        var rowId = $(this).data('id');

        var alreadyMarked = helperDeleteList.indexOf(rowId) !== -1;
        if (alreadyMarked) {
            helperDeleteList = helperDeleteList.filter(function (id) { return id !== rowId; });
            $('#helper_saved_row_' + rowId).css('opacity', '1');
            $(this).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            helperDeleteList.push(rowId);
            $('#helper_saved_row_' + rowId).css('opacity', '0.4');
            $(this).removeClass('btn-danger').addClass('btn-secondary');
        }
    });

    $('#helper_addtolist').click(function () {
        var empSelect = $('#helper_employee');
        var emp_id   = empSelect.val();
        var emp_name = empSelect.find('option:selected').text();

        if (!emp_id) {
            alert('Please select an employee.');
            return;
        }

        var duplicate = helperEmpList.some(function (e) { return e.emp_id == emp_id; });
        if (duplicate) {
            alert('Employee already in the list.');
            return;
        }

        helperEmpList.push({ emp_id: emp_id, emp_name: emp_name });

        var row = '<tr id="helper_staging_row_' + emp_id + '">' +
            '<td>' + emp_id + '</td>' +
            '<td>' + emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-staging-helper" data-empid="' + emp_id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#helper_emplistbody').append(row);

        empSelect.val(null).trigger('change');
    });

    $(document).on('click', '.remove-staging-helper', function () {
        var emp_id = $(this).data('empid');
        helperEmpList = helperEmpList.filter(function (e) { return e.emp_id != emp_id; });
        $('#helper_staging_row_' + emp_id).remove();
    });


});

 function productDelete(ctl) {
    	$(ctl).parents("tr").remove();
    }
</script>

@endsection