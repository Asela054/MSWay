@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.production&task_nav_bar_opma')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-ballot-check"></i></div>
                        <span>Machines</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Machine</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>MACHINE</th>
                                    <th>BRANCH</th>
                                    <th class="text-right">ACTION</th>
                                </tr>
                            </thead>
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
                    <span id="form_result"></span>
                    <form method="post" id="formTitle" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Company</label>
                                    <select name="company" id="company" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Branch</label>
                                    <select name="location" id="location" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Machine</label>
                                    <input type="text" name="machine" id="machine" class="form-control form-control-sm" required/>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Description</label>
                                    <input type="text" name="description" id="description" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-3 mb-0">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="action" id="action" value="Add" />
                        <input type="hidden" name="hidden_id" id="hidden_id" />
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add employee Modal -->
    <div class="modal fade" id="empModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="empModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="empModalLabel">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="emp_form_result"></span>
                            <form method="post" id="empFormTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <input type="hidden" name="emp_action" id="emp_action" />
                                <input type="hidden" name="emp_hidden_id" id="emp_hidden_id" />
                                <input type="hidden" name="detailsid" id="detailsid" />

                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="emp_machine" id="emp_machine" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            @foreach ($machines as $m)
                                                <option value="{{ $m->id }}">{{ $m->machine }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="emp_machine" id="emp_machine_hidden" />
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select class="form-control form-control-sm" name="employee" id="employee" style="width:100%" required></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <button type="button" id="addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                </div>

                                <br>
                                <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE NAME</th>
                                            <th style="white-space: nowrap;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emplistbody">
                                    </tbody>
                                </table>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" name="emp_action_button" id="emp_action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailymaster').addClass('navbtnactive');

    let company_f = $('#company');
    let location_f = $('#location');

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
                    company: company_f.val()
                }
            },
            cache: true
        }
    });

    // Employee Select2 Initialization
    let employee = $('#employee');
    employee.select2({
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

    $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Machine  Information',
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
                title: 'Machine  Information',
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
            url: scripturl + "/Opma_Production/machinelist.php",
            type: "POST",
            data: {},
        },
        columns: [
            { 
                data: 'id', 
                name: 'id'
            },
            { 
                data: 'machine', 
                name: 'machine'
            },
            { 
                data: 'branch', 
                name: 'branch'
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';

                    buttons += '<button type="submit" name="addemployee" id="'+row.id+'" class="view btn btn-info btn-sm mr-1" data-toggle="tooltip" title="Employees"><i class="fas fa-users"></i></button>';

                    buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                    buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                    return buttons;
                }
            }
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
 
    $('#create_record').click(function () {
        $('.modal-title').text('Add Machine');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#company').val(null).trigger('change');
        $('#location').val(null).trigger('change');
        $('#machine').val(null).trigger('change');
        $('#description').val(null).trigger('change');

        $('#formModal').modal('show');
    });


    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('opma_addMachine') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('OpmaMachine.update') }}";
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
            $.ajax({
                url: "{{ url('OpmaMachine/') }}/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#company').empty();
                    $('#location').empty();
                    
                    var companyOption = new Option(data.result.company_name, data.result.company_id, true, true);
                    $('#company').append(companyOption).trigger('change');
                    
                    var locationOption = new Option(data.result.branch_name, data.result.branch_id, true, true);
                    $('#location').append(locationOption).trigger('change');
                    
                    $('#machine').val(data.result.machine);
                    $('#description').val(data.result.description);

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Machine');
                    $('#action_button').html('Edit');
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
                url: "{{ url('OpmaMachine/destroy/') }}/" + user_id,
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


    var currentMachineId = null;
    var empList = [];
    var deleteList = []; 

    $(document).on('click', '.view', function () {
        currentMachineId = $(this).attr('id');
        empList = [];
        deleteList = [];
        $('#emplistbody').empty();

        $('#emp_machine').val(currentMachineId);
        $('#emp_machine_hidden').val(currentMachineId);

        $.ajax({
            url: '{{ url("OpmaMachine") }}/' + currentMachineId + '/employees',
            dataType: 'json',
            success: function (data) {
                $.each(data.employees, function (i, emp) {
                    appendSavedEmployee(emp);
                });
            }
        });

        $('#empModal').modal('show');
    });

    function appendSavedEmployee(emp) {
        var row = '<tr id="saved_row_' + emp.id + '">' +
            '<td>' + emp.emp_id + '</td>' +
            '<td>' + emp.emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-saved-emp" data-id="' + emp.id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#emplistbody').append(row);
    }

    $(document).on('click', '.remove-saved-emp', function () {
        var rowId = $(this).data('id');

        var alreadyMarked = deleteList.indexOf(rowId) !== -1;
        if (alreadyMarked) {
            deleteList = deleteList.filter(function (id) { return id !== rowId; });
            $('#saved_row_' + rowId).css('opacity', '1');
            $(this).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            deleteList.push(rowId);
            $('#saved_row_' + rowId).css('opacity', '0.4');
            $(this).removeClass('btn-danger').addClass('btn-secondary');
        }
    });

    $('#addtolist').click(function () {
        var empSelect = $('#employee');
        var emp_id   = empSelect.val();
        var emp_name = empSelect.find('option:selected').text();

        if (!emp_id) {
            alert('Please select an employee.');
            return;
        }

        var duplicate = empList.some(function (e) { return e.emp_id == emp_id; });
        if (duplicate) {
            alert('Employee already in the list.');
            return;
        }

        empList.push({ emp_id: emp_id, emp_name: emp_name });

        var row = '<tr id="staging_row_' + emp_id + '">' +
            '<td>' + emp_id + '</td>' +
            '<td>' + emp_name + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm remove-staging-emp" data-empid="' + emp_id + '">' +
                    '<i class="far fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '</tr>';
        $('#emplistbody').append(row);

        empSelect.val(null).trigger('change');
    });

    $(document).on('click', '.remove-staging-emp', function () {
        var emp_id = $(this).data('empid');
        empList = empList.filter(function (e) { return e.emp_id != emp_id; });
        $('#staging_row_' + emp_id).remove();
    });

    $('#emp_action_button').click(function () {
        if (empList.length === 0 && deleteList.length === 0) {
            alert('No changes to save.');
            return;
        }

        var requests = [];

        $.each(deleteList, function (i, rowId) {
            requests.push(
                $.ajax({
                    url: '{{ url("OpmaMachine/destroyEmployee") }}/' + rowId,
                    dataType: 'json'
                })
            );
        });

        if (empList.length > 0) {
            var empIds = empList.map(function (e) { return e.emp_id; });
            requests.push(
                $.ajax({
                    url: "{{ route('OpmaMachine.storeEmployees') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        machine_id: currentMachineId,
                        employees: empIds
                    },
                    dataType: 'json'
                })
            );
        }

        $.when.apply($, requests).then(function () {
            empList = [];
            deleteList = [];

            const actionObj = {
                icon: 'fas fa-save',
                title: '',
                message: 'Changes saved successfully.',
                url: '',
                target: '_blank',
                type: 'success'
            };
            action(JSON.stringify(actionObj));

            $('#empModal').modal('hide');   
        });
    });

});
</script>

@endsection