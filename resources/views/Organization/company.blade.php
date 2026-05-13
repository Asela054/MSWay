@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.corporate_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-building"></i></div>
                    <span>Company</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12 text-right">
                        <button type="button" class="btn btn-primary btn-sm px-4" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Company</button>
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
                                        <th>NAME</th>
                                        <th>CODE</th>
                                        <th>LOGO</th>
                                        <th>ADDRESS</th>
                                        <th>CONTACT NO</th>
                                        <th>EPF NO</th>
                                        <th>ETF NO</th>
                                        <th>REF NO</th>
                                        <th>VAT REG NO</th>
                                        <th>SVAT NO</th>
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
                    <h6 class="modal-title" id="staticBackdropLabel">Add Company</h6>
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
                                        <label class="small font-weight-bolder">Name*</label>
                                        <input type="text" name="name" id="name" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Code*</label>
                                        <input type="text" name="code" id="code" class="form-control form-control-sm" required />
                                    </div>                                    
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder">Address*</label>
                                    <input type="text" name="address" id="address" class="form-control form-control-sm" required />
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Mobile*</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Landline*</label>
                                        <input type="text" name="land" id="land" class="form-control form-control-sm" required />
                                    </div>                                    
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Email*</label>
                                        <input type="text" name="email" id="email" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Domain Name</label>
                                        <input type="text" name="domain_name" id="domain_name" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">EPF No</label>
                                        <input type="text" name="epf" id="epf" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">ETF No</label>
                                        <input type="text" name="etf" id="etf" class="form-control form-control-sm" />
                                    </div> 
                                    <div class="col">
                                        <label class="small font-weight-bolder">Ref No</label>
                                        <input type="text" name="ref_no" id="ref_no" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">VAT No</label>
                                        <input type="text" name="vat_reg_no" id="vat_reg_no" class="form-control form-control-sm" />
                                    </div>                         
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">SVAT No</label>
                                        <input type="text" name="svat_no" id="svat_no" class="form-control form-control-sm" />
                                    </div>    
                                    <div class="col">
                                        <label class="small font-weight-bolder">Zone Code</label>
                                        <input type="text" name="zone_code" id="zone_code" class="form-control form-control-sm" />
                                    </div>                         
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Bank Account Name</label>
                                        <input type="text" name="account_name" id="account_name" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Bank Account No</label>
                                        <input type="text" name="account_no" id="account_no" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-3">
                                        <label class="small font-weight-bolder">Branch Code</label>
                                        <input type="text" name="account_branchcode" id="account_branchcode" class="form-control form-control-sm" />
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bolder">Employee No</label>
                                        <input type="text" name="employeeno" id="employeeno" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bolder">Logo</label>
                                        <input type="file" data-preview="#preview" class="form-control form-control-sm" name="logo" id="logo">
                                        <div class="mt-1">
                                            <img class="" id="preview" src="" style="max-width: 200px; max-height: 200px; width: auto; height: auto;">
                                            <button type="button" id="remove_logo" class="btn btn-danger btn-sm mt-1" style="display:none;">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="remove_logo" id="remove_logo_flag" value="0" />
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <div class="center-block fix-width scroll-inner">
                                            <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Bank Name</th>
                                                        <th>Branch Name</th>
                                                        <th>Account No</th>
                                                        <th>Account Name</th>
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
                                            <i class="fas fa-plus"></i> Bank Details
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
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
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
    $('#organization_menu_link').addClass('active');
    $('#organization_menu_link_icon').addClass('active');
    $('#companylink').addClass('navbtnactive');

    $('#logo').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
                $('#remove_logo').show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#remove_logo').on('click', function() {
        $('#preview').attr('src', '');
        $('#logo').val('');
        $('#remove_logo').hide();
        $('#remove_logo_flag').val('1'); 
    });

    var rowIndex = 0;

    function initBankRow(rowIndex) {
        var bankSel = $('#bank_sel_' + rowIndex);
        var branchSel = $('#branch_sel_' + rowIndex);

        bankSel.select2({
            placeholder: 'Select Bank...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("bank_list") }}',
                dataType: 'json',
                data: function(params) {
                    return { term: params.term || '', page: params.page || 1 };
                },
                cache: true
            }
        });

        branchSel.select2({
            placeholder: 'Select Branch...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("branch_list2") }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        bank: bankSel.val()
                    };
                },
                cache: true
            }
        });

        // Reset branch when bank changes
        bankSel.on('change', function() {
            branchSel.val(null).trigger('change');
        });
    }

    function addBankRow(detailId, bankCode, bankText, branchCode, branchText, accountNo, accountName) {
        rowIndex++;
        var row = `
        <tr id="row_${rowIndex}">
            <td>
                <input type="hidden" name="detail_id[]" value="${detailId || ''}" />
                <select class="form-control form-control-sm" id="bank_sel_${rowIndex}" name="bank_code[]" required></select>
            </td>
            <td>
                <select class="form-control form-control-sm" id="branch_sel_${rowIndex}" name="branch_code[]" required></select>
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
            $('#bank_sel_' + rowIndex).append(bankOption).trigger('change');
        }
        if (branchCode && branchText) {
            var branchOption = new Option(branchText, branchCode, true, true);
            $('#branch_sel_' + rowIndex).append(branchOption).trigger('change');
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
                title: 'Customer  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Location Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Customer  Information',
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
            url: scripturl + "/companylist.php",
            type: "POST",
            data: {},
        },
        columns: [
            { 
                data: 'id', 
                name: 'id'
            },
            { 
                data: 'name', 
                name: 'name'
            },
            { 
                data: 'code', 
                name: 'code'
            },
            { 
                data: 'logo', 
                name: 'logo',
                render: function(data) {
                    return data ? '<img src="' + assetUrl + '/' + data + '" style="max-height:40px;">' : '';
                }
            },
            { 
                data: 'address', 
                name: 'address'
            },
            {
                "targets": -1,
                "data": 'emp_status',
                "name": 'emp_status',
                "render": function(data, type, full) {
                    return full['mobile'] + ' / ' + full['land'];
                }
            },
            { 
                data: 'epf', 
                name: 'epf'
            },
            { 
                data: 'etf', 
                name: 'etf'
            },
            { 
                data: 'ref_no', 
                name: 'ref_no'
            },
            { 
                data: 'vat_reg_no', 
                name: 'vat_reg_no'
            },
            { 
                data: 'svat_no', 
                name: 'svat_no'
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var is_resigned = row.is_resigned;
                    var buttons = '';

                    buttons += '<a href="DepartmentShow/' + row.id + '" title="Departments" class="branches btn btn-info btn-sm mr-1" data-toggle="tooltip" title="Department" ><i class="fas fa-building"></i></a>';
                    buttons += '<a href="BranchShow/' + row.id + '" title="Branches" class="location btn btn-secondary btn-sm mr-1" data-toggle="tooltip" title="Branch" ><i class="fas fa-code-branch"></i></a>';
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
        $('.modal-title').text('Add New Company');
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
            action_url = "{{ route('addCompany') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('Company.update') }}";
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

    $(document).on('click', '.edit', async function() {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "Company/" + id + "/edit",
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
                    $('.modal-title').text('Edit Company');
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
                url: "Company/destroy/" + user_id,
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