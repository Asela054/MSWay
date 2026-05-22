@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Training Allocation</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
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
                                        <th>TRAINING NAME</th>
                                        <th>DATE</th>
                                        <th>VENUE</th>
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
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Training Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}

                                <!-- Training Details -->
                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Training Name</label>
                                        <input type="text" name="training_name" id="training_name" class="form-control form-control-sm" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Venue</label>
                                        <input type="text" name="venue" id="venue" class="form-control form-control-sm" />
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" name="date" id="date" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <hr>
                                <!-- Session Details -->
                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Session Name</label>
                                        <input type="text" name="session_name" id="session_name" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Trainer Name</label>
                                        <select name="trainer_id" id="trainer_id" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Start Time</label>
                                        <input type="datetime-local" name="start_time" id="start_time" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">End Time</label>
                                        <input type="datetime-local" name="end_time" id="end_time" class="form-control form-control-sm" />
                                    </div>

                                </div>
                                <div class="form-row mt-3">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-primary btn-sm fa-pull-right px-4" id="add_session_row">
                                            <i class="fas fa-plus mr-1"></i>Add
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />

                                <!-- Preview Table -->
                                <div class="col-12 mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>SESSION NAME</th>
                                                    <th>START TIME</th>
                                                    <th>END TIME</th>
                                                    <th>TRAINER NAME</th>
                                                    <th class="text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableorderlist"></tbody>
                                        </table>
                                    </div>

                                    <div class="form-group mt-2 text-right">
                                        <button type="submit" form="formTitle" name="btncreatesession" id="btncreatesession" class="btn btn-primary btn-sm px-4">
                                            <i class="fas fa-save"></i>&nbsp;Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->

    <!-- Types Modal -->
    <div class="modal fade" id="typesModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title">Training Types</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="types_allocation_id" />
                    <div id="types_list" class="row px-2"></div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-primary btn-sm" id="saveTypes">
                        <i class="fas fa-save mr-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Employees Modal Start -->
    <div class="modal fade" id="empModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="empModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="empModalLabel">View Employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2 p-sm-3">
                    <div class="row">
                        <div class="col-12">
                            <span id="emp_form_result"></span>
                            <input type="hidden" id="emp_detailsid" />

                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label class="small font-weight-bold text-dark">Employee</label>
                                    <select class="form-control form-control-sm" name="emp_employee" id="emp_employee" style="width:100%"></select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-sm-6 col-md-4 mb-2 mb-sm-0">
                                    <button type="button" id="emp_addtolist" class="btn btn-primary btn-sm px-4 w-100 w-sm-auto">
                                        <i class="fas fa-plus"></i>&nbsp;Add to list
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="empAllocationTbl" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Emp ID</th>
                                            <th>Employee Name</th>
                                            <th style="white-space: nowrap;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emplistbody">
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-3 mb-0">
                                <button type="button" id="emp_action_button" class="btn btn-primary btn-sm float-right px-4">
                                    <i class="fas fa-plus"></i>&nbsp;Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- View Employees Modal End -->
</main>

@endsection

@section('script')

<script>
    $(document).ready(function() {

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#training').addClass('navbtnactive');

        let employee = $('#trainer_id');
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

        // Employee select2
        $('#emp_employee').select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#empModal'),
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
                    title: 'Employee Training  Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Employee Training Information',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Training  Information',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
                // 'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "order": [
                [0, "desc"]
            ],
            ajax: {
                url: scripturl + "/Training_Management/training_allocation_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'training_name',
                    name: 'training_name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'venue',
                    name: 'venue'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        buttons += '<button name="types" id="' + row.id + '" class="types btn btn-primary btn-sm mr-1" data-toggle="tooltip" title="Types"><i class="fas fa-sitemap"></i></button>'

                        buttons += '<button id="' + row.id + '" class="Employee btn btn-info btn-sm mr-1" data-toggle="tooltip" title="View Employees"><i class="fas fa-users"></i></button> ';

                        buttons += ' <button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                        buttons += '<button name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="far fa-trash-alt"></i></button>';

                        return buttons;
                    }
                },
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Add session row
        var rowIndex = 0;

        $('#add_session_row').click(function() {
            var sessionName = $('#session_name').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            var trainerId = $('#trainer_id').val();
            var trainerText = $('#trainer_id option:selected').text();

            if (!sessionName || !startTime || !endTime) {
                const actionObj = {
                    icon: 'fas fa-warning',
                    title: 'Validation Error',
                    message: 'Please fill all session fields before adding.',
                    url: '',
                    target: '_blank',
                    type: 'warning'
                };

                action(JSON.stringify(actionObj));
                return;
            }

            rowIndex++;

            $('#tableorderlist').append(`
                <tr id="session_row_${rowIndex}">
                    <td>${rowIndex}</td>
                    <td>
                        ${sessionName}
                        <input type="hidden" name="session_name[]"  value="${sessionName}" />
                        <input type="hidden" name="start_time[]"    value="${startTime}" />
                        <input type="hidden" name="end_time[]"      value="${endTime}" />
                        <input type="hidden" name="trainer_id[]"    value="${trainerId}" />
                    </td>
                    <td>${startTime}</td>
                    <td>${endTime}</td>
                    <td>${trainerText}</td>
                    <td class="text-right">
                        <button type="button" class="btn btn-danger btn-sm remove_session" data-id="${rowIndex}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `);
            // Clear session fields
            $('#session_name').val('');
            $('#start_time').val('');
            $('#end_time').val('');
            $('#trainer_id').val(null).trigger('change');
        });

        $(document).on('click', '.remove_session', function() {
            var id = $(this).data('id');
            $('#session_row_' + id).remove();
        });



        $('#create_record').click(function() {
            $('.modal-title').text('Add Training Allocation');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#tableorderlist').html('');
            $('#trainer_id').val(null).trigger('change');
            rowIndex = 0;
            $('#formModal').modal('show');
        });


        $('#formTitle').on('submit', function(event) {
            event.preventDefault();
            var action_url = '';


            if ($('#action').val() == 'Add') {
                action_url = "{{ route('addTrainingAllocation') }}";
            }

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('TrainingAllocation.update') }}";
            }


            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(data) {
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
                        $('#formModal').modal('hide');
                        actionreload(actionJSON);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    const actionObj = {
                        icon: 'fas fa-warning',
                        title: '',
                        message: 'Something went wrong!',
                        url: '',
                        target: '_blank',
                        type: 'danger'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    action(actionJSON);
                }
            });
        });

        $(document).on('click', '.edit', async function() {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "TrainingAllocation/" + id + "/edit",
                    dataType: "json",
                    success: function(data) {
                        $('#training_name').val(data.result.training_name);
                        $('#date').val(data.result.date);
                        $('#venue').val(data.result.venue);

                        //session table edit handler
                        $('#tableorderlist').html('');
                        rowIndex = 0;
                        if (data.sessions && data.sessions.length > 0) {
                            $.each(data.sessions, function(i, s) {
                                rowIndex++;
                                $('#tableorderlist').append(`
                                    <tr id="session_row_${rowIndex}">
                                        <td>${rowIndex}</td>
                                        <td>
                                            ${s.session_name}
                                            <input type="hidden" name="session_name[]" value="${s.session_name}" />
                                            <input type="hidden" name="start_time[]"   value="${s.start_time}" />
                                            <input type="hidden" name="end_time[]"     value="${s.end_time}" />
                                            <input type="hidden" name="trainer_id[]"   value="${s.trainer_id}" />
                                            <input type="hidden" name="session_db_id[]" value="${s.id}" />
                                        </td>
                                        <td>${s.start_time}</td>
                                        <td>${s.end_time}</td>
                                        <td>${s.trainer_name}</td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-danger btn-sm remove_session" data-id="${rowIndex}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        }

                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Training Allocation');
                        $('#action_button').html('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                })
            }
        });

        var user_id;

        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                user_id = $(this).attr('id');

                $.ajax({
                    url: "{{ url('TrainingAllocation/destroy') }}/" + user_id,
                    beforeSend: function() {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function(data) {
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
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                })
            }
        });

        // Types modal list
        $(document).on('click', '.types', function() {
            var id = $(this).attr('id');
            $('#types_allocation_id').val(id);
            $('#types_list').html('<p class="text-center">Loading...</p>');
            $('#typesModal').modal('show');

            $.ajax({
                url: 'TrainingAllocation/' + id + '/types',
                dataType: 'json',
                success: function(data) {
                    $('#types_list').html('');
                    $.each(data.allTypes, function(i, type) {
                        var checked = data.selectedTypes.includes(type.id) ? 'checked' : '';
                        $('#types_list').append(
                            '<div class="col-6 mb-1">' +
                            '<div class="custom-control custom-checkbox">' +
                            '<input type="checkbox" class="custom-control-input" ' +
                            'id="type_' + type.id + '" value="' + type.id + '" ' + checked + '>' +
                            '<label class="custom-control-label small" for="type_' + type.id + '">' +
                            type.name +
                            '</label>' +
                            '</div>' +
                            '</div>'
                        );
                    });
                }
            });
        });

        // Save types
        $('#saveTypes').click(function() {
            var id = $('#types_allocation_id').val();
            var selected = [];
            $('#types_list input[type="checkbox"]:checked').each(function() {
                selected.push($(this).val());
            });

            $.ajax({
                url: "{{ url('TrainingAllocation/saveTypes') }}",
                method: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    allocation_id: id,
                    type_ids: selected
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        $('#typesModal').modal('hide');
                        const actionObj = {
                            icon: 'fas fa-save',
                            title: '',
                            message: data.success,
                            url: '',
                            target: '_blank',
                            type: 'success'
                        };
                        action(JSON.stringify(actionObj));
                    }
                }
            });
        });

        // View Employees button
        $(document).on('click', '.Employee', function() {
            var id = $(this).attr('id');
            $('#empModalLabel').text('View Employees');
            $('#emp_detailsid').val(id);
            $('#emp_form_result').html('');
            $('#emplistbody').empty();
            $('#emp_employee').val(null).trigger('change');
            $('#emp_action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Save');

            $.ajax({
                url: "{{ url('TrainingAllocation') }}/" + id + "/employees",
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(i, emp) {
                        $('#emplistbody').append(
                            '<tr class="pointer" data-db-id="' + emp.id + '">' +
                            '<td>' + emp.emp_id + '</td>' +
                            '<td>' + emp.employee_display + '</td>' +
                            '<td class="text-right">' +
                            '<button type="button" class="btn btn-danger btn-sm emp-delete" data-id="' + emp.id + '">' +
                            '<i class="fas fa-trash-alt"></i>' +
                            '</button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });
                }
            });

            $('#empModal').modal('show');
        });

        // Add employee to list
        $('#emp_addtolist').click(function() {
            if (!$('#emp_employee').val()) {
                alert('Please select an employee');
                return;
            }

            var emp_id = $('#emp_employee').val();
            var selectedText = $('#emp_employee option:selected').text();

            var exists = false;
            $('#emplistbody tr').each(function() {
                if ($(this).find('td:first').text() == emp_id) {
                    exists = true;
                    return false;
                }
            });

            if (exists) {
                alert('Employee already added to the list');
                return;
            }

            $('#emplistbody').append('<tr class="pointer">' +
                '<td>' + emp_id + '</td>' +
                '<td>' + selectedText + '</td>' +
                '<td class="text-right">' +
                '<button type="button" class="btn btn-danger btn-sm emp-delete">' +
                '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
                '</td>' +
                '<td class="d-none">NewData</td>' +
                '</tr>');

            $('#emp_employee').val(null).trigger('change');
        });

        // Save new employees
        $('#emp_action_button').click(function(e) {
            e.preventDefault();
            $('#emp_action_button').prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i>Processing');

            var jsonObj = [];
            $("#emplistbody tr").each(function() {
                var cells = $(this).find('td');
                var lastCell = cells.last().text().trim();
                if (lastCell === 'NewData') {
                    var item = {};
                    item["col_1"] = cells.eq(0).text();
                    item["col_4"] = 'NewData';
                    jsonObj.push(item);
                }
            });

            if (jsonObj.length === 0) {
                $('#emp_action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Save');
                const actionObj = {
                    icon: 'fas fa-info-circle',
                    title: '',
                    message: 'No new employees to add.',
                    url: '',
                    target: '_blank',
                    type: 'info'
                };
                action(JSON.stringify(actionObj));
                return;
            }

            $.ajax({
                url: "{{ route('trainingEmpAllocationinsert') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    detailsid: $('#emp_detailsid').val(),
                    empData: JSON.stringify(jsonObj),
                    action: 'Add'
                },
                dataType: "json",
                success: function(data) {
                    $('#emp_action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Save');
                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors.join(', '),
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        action(JSON.stringify(actionObj));
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
                        action(JSON.stringify(actionObj));
                        $('#empModal').modal('hide');
                    }
                },
                error: function() {
                    $('#emp_action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Save');
                    alert('Something went wrong!');
                }
            });
        });

        // Delete existing employee from allocation
        $(document).on('click', '.emp-delete', async function() {
            var btn = $(this);
            var dbId = btn.data('id');

            if (!dbId) {
                btn.closest('tr').remove();
                return;
            }

            var r = await Otherconfirmation("You want to remove this employee?");
            if (r == true) {
                $.ajax({
                    url: "{{ url('trainingEmpAllocation/destroy') }}/" + dbId,
                    method: "GET",
                    success: function(data) {
                        if (data.success) {
                            btn.closest('tr').remove();
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            action(JSON.stringify(actionObj));
                        } else if (data.error) {
                            alert(data.error);
                        }
                    },
                    error: function() {
                        alert('Failed to delete employee');
                    }
                });
            }
        });

    });
</script>

@endsection