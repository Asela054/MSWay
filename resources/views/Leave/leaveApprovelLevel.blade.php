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
                        <button type="button" class="btn btn-primary btn-sm px-4" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Approval Level</button>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
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
    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title" id="staticBackdropLabel">Add Approval Level</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-9">
                                        <label class="small font-weight-bold text-dark">Company <span class="text-danger">*</span> </label>
                                        <select name="company" id="company" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <label class="small font-weight-bold text-dark">Department <span class="text-danger">*</span> </label>
                                        <select name="department" id="department" class="form-control form-control-sm" required>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>JOB TITLE</th>
                                                        <th>APPROVAL LEVEL</th>
                                                        <th style="white-space: nowrap;">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="emplistbody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary btn-sm px-4" id="add_detail_row">
                                            <i class="fas fa-plus"></i> Add Level
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
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
var assetUrl = "{{ rtrim(asset(''), '/') }}";
$(document).ready(function(){
    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#leavemaster').addClass('navbtnactive');

    // department wise salary advance modal
    let company_d = $('#company');
    let department_d = $('#department');

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

    var rowIndex = 0;

    function initBankRow(rowIndex) {
        var jobSel = $('#job_sel_' + rowIndex);
        var levelSel = $('#level_sel_' + rowIndex);

        jobSel.select2({
            placeholder: 'Select Bank...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("jobTitle_list") }}',
                dataType: 'json',
                data: function(params) {
                    return { term: params.term || '', page: params.page || 1 };
                },
                cache: true
            }
        });

        levelSel.select2({
            placeholder: 'Select Branch...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("apLevel_list2") }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                cache: true
            }
        });
    }

    function addBankRow(detailId, bankCode, bankText, branchCode, branchText, accountNo, accountName) {
        rowIndex++;
        var row = `
        <tr id="row_${rowIndex}">
            <td>
                <input type="hidden" name="detail_id[]" value="${detailId || ''}" />
                <select class="form-control form-control-sm" id="job_sel_${rowIndex}" name="bank_code[]" required></select>
            </td>
            <td>
                <select class="form-control form-control-sm" id="level_sel_${rowIndex}" name="branch_code[]" required></select>
            </td>
            <td>
                <input type="text" name="bank_account_number[]" class="form-control form-control-sm" placeholder="Account No" value="${accountNo || ''}" required />
            </td>
            <td>
                <input type="text" name="bank_account_name[]" class="form-control form-control-sm" placeholder="Account Name" value="${accountName || ''}" required />
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove_row" data-id="${rowIndex}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;

        $('#emplistbody').append(row);
        initBankRow(rowIndex);

        // If pre-loading existing data (edit mode), set selected options
        if (bankCode && bankText) {
            var bankOption = new Option(bankText, bankCode, true, true);
            $('#job_sel_' + rowIndex).append(bankOption).trigger('change');
        }
        if (branchCode && branchText) {
            var branchOption = new Option(branchText, branchCode, true, true);
            $('#level_sel_' + rowIndex).append(branchOption).trigger('change');
        }
    }

    $('#add_detail_row').click(function() {
        addBankRow('', '', '', '', '', '', '');
    });

    $(document).on('click', '.remove_row', function() {
        var id = $(this).data('id');
        $('#row_' + id).remove();
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
                title: 'Approval Level Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Approval Level Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Approval Level Information',
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
            url: scripturl + "/approvalLevel_list.php",
            type: "POST",
            data: {},
        },
        columns: [
            { 
                data: 'id', 
                name: 'id'
            },
            { 
                data: 'department', 
                name: 'department'
            },
            { 
                data: 'job_title', 
                name: 'job_title'
            },
            { 
                data: 'approval_level', 
                name: 'approval_level'
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';

                    buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                    return buttons;
                }
            }
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    $('#create_record').click(function(){
        $('.modal-title').text('Add New Approval Level');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#emplistbody').empty();    
        rowIndex = 0;

        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addApprovalLevel') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('ApprovalLevel.update') }}";
        }

        var formData = new FormData(this); 

        $.ajax({
            url: action_url,
            method: "POST",
            data: formData,
            dataType: "json",
            processData: false,          
            contentType: false,  
            success: function (data) {//alert(data); 
                if (data.errors) {
                    var errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                    $.each(data.errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                    errorHtml += '</ul></div>';
                    $('#form_result').html(errorHtml);  
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

    $(document).on('click', '.edit', async function() {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "ApprovalLevel/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#name').val(data.result.name);
                    $('#code').val(data.result.code);
                    $('#address').val(data.result.address);
                    $('#mobile').val(data.result.mobile);
                    $('#land').val(data.result.land);
                    $('#email').val(data.result.email);
                    $('#domain_name').val(data.result.domain_name);
                    $('#epf').val(data.result.epf);
                    $('#etf').val(data.result.etf);
                    $('#ref_no').val(data.result.ref_no);
                    $('#vat_reg_no').val(data.result.vat_reg_no);
                    $('#svat_no').val(data.result.svat_no);
                    $('#account_name').val(data.result.bank_account_name);
                    $('#account_no').val(data.result.bank_account_number);
                    $('#account_branchcode').val(data.result.bank_account_branch_code);
                    $('#employeeno').val(data.result.employer_number);
                    $('#zone_code').val(data.result.zone_code);

                    $('#emplistbody').empty();
                    rowIndex = 0;
                    if (data.bank_details && data.bank_details.length > 0) {
                        $.each(data.bank_details, function(i, detail) {
                            addBankRow(
                                detail.id,
                                detail.bank_code,
                                detail.bank_name,
                                detail.branch_code,
                                detail.branch_name,
                                detail.bank_account_number,
                                detail.bank_account_name
                            );
                        });
                    }

                    if (data.result.logo) {
                        $('#preview').attr('src', assetUrl + '/' + data.result.logo);
                        $('#remove_logo').show();
                    } else {
                        $('#preview').attr('src', '');
                        $('#remove_logo').hide();
                    }
                    $('#remove_logo_flag').val('0'); 

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Approval Level');
                    $('#action_button').html('Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
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
                url: "ApprovalLevel/destroy/" + user_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {//alert(data);
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