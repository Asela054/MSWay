@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.attendant&leave_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                    <span>Leave Approval Level</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-primary btn-sm px-4" id="create_record">
                            <i class="fas fa-plus mr-2"></i>Approval Level
                        </button>
                    </div>
                    <div class="col-12"><hr></div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DEPARTMENT</th>
                                        <th>JOB TITLE</th>
                                        <th>APPROVAL LEVEL</th>
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

    <!-- Modal  -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false"
         tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title" id="formModalLabel">Add Approval Level</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle">
                                {{ csrf_field() }}

                                <div class="form-row mb-3">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">
                                            Company <span class="text-danger">*</span>
                                        </label>
                                        <select name="company" id="company"
                                                class="form-control form-control-sm" required>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">
                                            Department <span class="text-danger">*</span>
                                        </label>
                                        <select name="department" id="department"
                                                class="form-control form-control-sm" required>
                                        </select>
                                    </div>
                                </div>

                                <!-- Job Title / Approval Level rows -->
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-striped table-bordered table-sm small nowrap"
                                                   id="allocationtbl" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>JOB TITLE</th>
                                                        <th>APPROVAL LEVEL</th>
                                                        <th style="white-space:nowrap;">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="emplistbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <button type="button" class="btn btn-secondary btn-sm px-4"
                                                id="add_detail_row">
                                            <i class="fas fa-plus"></i> Add Row
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group mt-3 mb-0">
                                    <button type="submit" name="action_button" id="action_button"
                                            class="btn btn-primary btn-sm fa-pull-right px-4">
                                        <i class="fas fa-plus"></i>&nbsp;Add
                                    </button>
                                </div>

                                <input type="hidden" name="action"    id="action"    value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" value="" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal  -->
</main>

@endsection

@section('script')
<script>
$(document).ready(function () {

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#leavemaster').addClass('navbtnactive');

    var rowIndex = 0;

    var $company = $('#company');
    var $department = $('#department');

    $company.select2({
        placeholder: 'Select Company...',
        width: '100%',
        allowClear: true,
        dropdownParent: $('#formModal'),
        ajax: {
            url: '{{ url("company_list_sel2") }}',
            dataType: 'json',
            data: function (params) {
                return { term: params.term || '', page: params.page || 1 };
            },
            cache: true
        }
    });

    $department.select2({
        placeholder: 'Select Department...',
        width: '100%',
        allowClear: true,
        dropdownParent: $('#formModal'),
        ajax: {
            url: '{{ url("department_list_sel2") }}',
            dataType: 'json',
            data: function (params) {
                return { term: params.term || '', page: params.page || 1,
                    company: $company.val() };
            },
            cache: true
        }
    });

    // When a department is selected, load any existing hierarchy rows
    $department.on('select2:select', function () {
        var deptId = $(this).val();
        if (!deptId) return;

        $.ajax({
            url: '{{ url("ApprovalLevel/department-hierarchy") }}',
            data: { department_id: deptId },
            dataType: 'json',
            success: function (data) {
                $('#emplistbody').empty();
                rowIndex = 0;

                if (data.rows && data.rows.length > 0) {
                    $.each(data.rows, function (i, row) {
                        addRow(row.id, row.title_id, row.title_text, row.approval_level, row.approval_level);
                    });
                    setFormMode('Edit');
                } else {
                    setFormMode('Add');
                }
            }
        });
    });

    // When department is cleared, reset to Add mode
    $department.on('select2:clear', function () {
        $('#emplistbody').empty();
        rowIndex = 0;
        setFormMode('Add');
    });

    function setFormMode(mode) {
        $('#action').val(mode);
        if (mode === 'Add') {
            $('.modal-title').text('Add New Approval Level');
            $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        } else {
            $('.modal-title').text('Edit Approval Level');
            $('#action_button').html('<i class="fas fa-save"></i>&nbsp;Update');
        }
    }

    function initRowSelects(idx) {
        // Job Title
        $('#job_sel_' + idx).select2({
            placeholder: 'Select Job Title...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("jobTitle_list") }}',
                dataType: 'json',
                data: function (params) {
                    return { term: params.term || '', page: params.page || 1 };
                },
                cache: true
            }
        });

        // Approval Level
        $('#level_sel_' + idx).select2({
            placeholder: 'Select Level...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("apLevel_list2") }}',
                dataType: 'json',
                data: function (params) {
                    return { term: params.term || '', page: params.page || 1 };
                },
                cache: true
            }
        });
    }

    
    function addRow(rowId, titleId, titleText, levelId, levelText) {
        rowIndex++;
        var row = `
        <tr id="row_${rowIndex}">
            <td>
                <input type="hidden" name="row_id[]"   value="${rowId   || ''}" />
                <input type="hidden" name="title_id[]" id="title_id_${rowIndex}" value="${titleId || ''}" />
                <select class="form-control form-control-sm"
                        id="job_sel_${rowIndex}"
                        name="job_sel_display_${rowIndex}"
                        required>
                </select>
            </td>
            <td>
                <input type="hidden" name="approval_level[]" id="level_id_${rowIndex}" value="${levelId || ''}" />
                <select class="form-control form-control-sm"
                        id="level_sel_${rowIndex}"
                        name="level_sel_display_${rowIndex}"
                        required>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove_row" data-row="${rowIndex}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

        $('#emplistbody').append(row);
        initRowSelects(rowIndex);

        // Sync hidden inputs when select2 changes
        $('#job_sel_' + rowIndex).on('select2:select select2:clear', function () {
            $('#title_id_' + rowIndex).val($(this).val() || '');
        });
        $('#level_sel_' + rowIndex).on('select2:select select2:clear', function () {
            $('#level_id_' + rowIndex).val($(this).val() || '');
        });

        // Pre-load existing values (edit mode)
        if (titleId && titleText) {
            var opt = new Option(titleText, titleId, true, true);
            $('#job_sel_' + rowIndex).append(opt).trigger('change');
            $('#title_id_' + rowIndex).val(titleId);
        }
        if (levelId && levelText) {
            var opt2 = new Option(levelText, levelId, true, true);
            $('#level_sel_' + rowIndex).append(opt2).trigger('change');
            $('#level_id_' + rowIndex).val(levelId);
        }
    }

    $('#add_detail_row').on('click', function () {
        addRow('', '', '', '', '');
    });

    $(document).on('click', '.remove_row', function () {
        var idx = $(this).data('row');
        $('#row_' + idx).remove();
    });

    // DataTable
    var table = $('#dataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'csv',   className: 'btn btn-success btn-sm', title: 'Approval Level Information',
              text: '<i class="fas fa-file-csv mr-2"></i> CSV' },
            { extend: 'pdf',   className: 'btn btn-danger btn-sm',  title: 'Approval Level Information',
              text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
              orientation: 'landscape', pageSize: 'legal',
              customize: function (doc) {
                  doc.content[1].table.widths =
                      Array(doc.content[1].table.body[0].length + 1).join('*').split('');
              }
            },
            { extend: 'print', className: 'btn btn-primary btn-sm', title: 'Approval Level Information',
              text: '<i class="fas fa-print mr-2"></i> Print',
              customize: function (win) {
                  $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
              }
            }
        ],
        order: [[0, 'desc']],
        ajax: {
            url:  scripturl + '/approvalLevel_list.php',
            type: 'POST',
            data: {}
        },
        columns: [
            { data: 'id',             name: 'id' },
            { data: 'name',           name: 'name' },
            { data: 'title',          name: 'title' },
            { data: 'approval_level', name: 'approval_level' },
            {
                data: null,
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<button class="edit btn btn-primary btn-sm mr-1" ' +
                           'data-id="' + row.id + '" data-dept="' + row.department_id + '" ' +
                           'type="button" data-toggle="tooltip" title="Edit">' +
                           '<i class="fas fa-pencil-alt"></i></button>' +
                           '<button class="delete btn btn-danger btn-sm" ' +
                           'data-id="' + row.id + '" ' +
                           'type="button" data-toggle="tooltip" title="Remove">' +
                           '<i class="far fa-trash-alt"></i></button>';
                }
            }
        ],
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Open modal for Add
    $('#create_record').on('click', function () {
        resetModal();
        setFormMode('Add');
        $('#formModal').modal('show');
    });

    function resetModal() {
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#emplistbody').empty();
        rowIndex = 0;
        $('#hidden_id').val('');

        // Reset department select2
        $department.val(null).trigger('change');
    }

    // Edit button — load existing rows for that department
    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Edit this?");
        if (!r) return;

        var deptId = $(this).data('dept');

        resetModal();
        setFormMode('Edit');
        $('#hidden_id').val(deptId);

        $.ajax({
            url: '{{ url("ApprovalLevel/department-hierarchy") }}',
            data: { department_id: deptId },
            dataType: 'json',
            success: function (data) {
                // Pre-select the department in the select2
                if (data.rows && data.rows.length > 0) {
                    // We need the department name; fetch it
                    var firstRow = data.rows[0];
                }

                // Set department select2 value via AJAX lookup isn't needed —
                // load department name from DataTable row or via a direct query.
                // We'll use the department name from the table row's data attribute.
                var deptName = $('button.edit[data-dept="' + deptId + '"]')
                                   .closest('tr')
                                   .find('td:eq(1)').text().trim();

                if (deptId && deptName) {
                    var deptOption = new Option(deptName, deptId, true, true);
                    $department.append(deptOption).trigger('change');
                }

                $('#emplistbody').empty();
                rowIndex = 0;

                if (data.rows && data.rows.length > 0) {
                    $.each(data.rows, function (i, row) {
                        addRow(row.id, row.title_id, row.title_text,
                               row.approval_level, row.approval_level);
                    });
                }

                $('#formModal').modal('show');
            }
        });
    });

    // Delete a single hierarchy row
    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this?");
        if (!r) return;

        var id = $(this).data('id');
        $.ajax({
            url: '{{ url("ApprovalLevel/destroy") }}/' + id,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    const actionObj = {
                        icon: 'fas fa-trash-alt', title: '',
                        message: data.success, url: '', target: '_blank', type: 'danger'
                    };
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            }
        });
    });

    // Form submit (Add / Edit)
    $('#formTitle').on('submit', function (event) {
        event.preventDefault();

        var action = $('#action').val();

        //  Client-side validation 
        var rows = $('#emplistbody tr');
        if (rows.length === 0) {
            $('#form_result').html(
                '<div class="alert alert-danger">Please add at least one job title with an approval level.</div>'
            );
            return;
        }

        var seenTitles = [], seenLevels = [], valid = true, errorMsg = '';

        rows.each(function () {
            var $row     = $(this);
            var titleVal = $row.find('input[name="title_id[]"]').val();
            var levelVal = $row.find('input[name="approval_level[]"]').val();

            if (!titleVal || !levelVal) {
                errorMsg = 'Each row must have both a Job Title and an Approval Level selected.';
                valid = false; return false;
            }
            if (seenTitles.indexOf(titleVal) !== -1) {
                errorMsg = 'Within the same department, a single Job Title cannot be assigned to two or more approval levels.';
                valid = false; return false;
            }
            if (seenLevels.indexOf(levelVal) !== -1) {
                errorMsg = 'Within the same department, a single Approval Level cannot be assigned to two or more job titles.';
                valid = false; return false;
            }
            seenTitles.push(titleVal);
            seenLevels.push(levelVal);
        });

        if (!valid) {
            $('#form_result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
            return;
        }

        var actionUrl = (action === 'Add')
            ? '{{ route("addApprovalLevel") }}'
            : '{{ route("ApprovalLevel.update") }}';

        var formData = new FormData(this);

        $.ajax({
            url:         actionUrl,
            method:      'POST',
            data:        formData,
            dataType:    'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.errors) {
                    var html = '<div class="alert alert-danger"><ul class="mb-0">';
                    $.each(data.errors, function (k, v) { html += '<li>' + v + '</li>'; });
                    html += '</ul></div>';
                    $('#form_result').html(html);
                }
                if (data.success) {
                    $('#formModal').modal('hide');
                    var actionObj = {
                        icon: 'fas fa-save', title: '',
                        message: data.success, url: '', target: '_blank', type: 'success'
                    };
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            }
        });
    });

});
</script>
@endsection
