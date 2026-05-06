@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('ERP_KT.erp_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-industry"></i></div>
                    <span>Inquiry</span>
                </h1>
            </div>
        </div>
    </div>


    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Inquiry</button>
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
                                        <th>CUSTOMER</th>
                                        <th>DATE</th>
                                        <th>REMARKS</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Inquiry</h5>
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
                                    <label class="small font-weight-bold text-dark">Customer Name<span class="text-red">*</span></label>
                                    <select name="customer_id" id="customer_id" class="form-control form-control-sm" required>
                                        <option value="">Select Customer...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="button" class="btn btn-primary btn-sm fa-pull-right px-4" id="add_detail_row">
                                    <i class="fas fa-plus mr-1"></i>Add Inquiry
                                </button>
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Inquiry</th>

                                                <th style="white-space: nowrap;">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="emplistbody">
                                        </tbody>
                                    </table>
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
    <!-- Modal Area End -->

    <!-- View Modal -->
    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="aviewmodal-title" id="staticBackdropLabel">View Inquiries</h5>
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
                                    <label class="small font-weight-bold text-dark">Customer Name <span class="text-red">*</span></label>
                                    <select name="view_customer_name" id="view_customer_name" class="form-control form-control-sm" disabled>
                                        <option value="">Select Customer...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input type="date" name="view_date" id="view_date" class="form-control form-control-sm" readonly />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Remarks</label>
                                    <input type="text" name="view_remarks" id="view_remarks" class="form-control form-control-sm" readonly />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Inquiry</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view_emplistbody">
                                        </tbody>
                                    </table>
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
    <!-- View Modal End-->

</main>

@endsection

@section('script')

<script>
    var rowIndex = 0;
    $(document).ready(function() {

        $('#erp_menu_link_KT').addClass('active');
        $('#erp_menu_link_KT_icon').addClass('active');
        $('#erp_kt_master').addClass('navbtnactive');

        // Modal close handlers
        $('#viewconfirmModal .close').click(function() {
            $('#viewconfirmModal').modal('hide');
        });

        // Customer Select2 Initialization
        let customer = $('#customer_id');
        customer.select2({
            placeholder: 'Select Customer...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
            ajax: {
                url: '{{ url("kt_customer_list_sel2") }}',
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
        // Add detail row
        $('#add_detail_row').click(function() {
            rowIndex++;
            $('#emplistbody').append(`
            <tr id="row_${rowIndex}">
                <td>
                    <input type="hidden" name="detail_id[]" value="" />
                    <input type="text" name="detail_inquiry[]" class="form-control form-control-sm" placeholder="Inquiry detail" required />
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove_row" data-id="${rowIndex}"><i class="fas fa-trash-alt"></i></button></td>
            </tr>
        `);
        });

        // Remove detail row
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
                    title: 'Inquiry  Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Inquiry Information',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Inquiry Information',
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
                url: scripturl + "/ERP_KT/inquiry_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
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
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';

                        buttons += ' <button name="view" id="' + row.id + '" class="view btn btn-secondary btn-sm mr-1" type="button" data-toggle="tooltip" title="View"><i class="fas fa-eye"></i></button>';

                        buttons += '<button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                        buttons += '<button type="submit" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                        return buttons;
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });

    $('#create_record').click(function() {
        $('.modal-title').text('Add Inquiry');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');

        // Clear customer select2 and detail rows
        $('#customer_id').val(null).trigger('change');
        $('#emplistbody').empty();
        rowIndex = 0;

        $('#formModal').modal('show');
    });

    //Submit function
    $('#formTitle').on('submit', function(event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('kt_addInquiry') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('KTInquiry.update') }}";
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
                    actionreload(actionJSON);
                }
            }
        });
    });

    //Handle edit click
    $(document).on('click', '.edit', async function() {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "{{ url('KTInquiry/') }}/" + id + "/edit",
                dataType: "json",
                success: function(data) {
                    var customerId = data.result.customer_id;

                    // Clear previous selection
                    $('#customer_id').val(null).trigger('change');

                    // customer id → name and populate Select2
                    var newOption = new Option(customerId, customerId, true, true);

                    $.ajax({
                        url: '{{ url("kt_customer_list_sel2") }}',
                        dataType: 'json',
                        data: {
                            term: customerId
                        },
                        success: function(resp) {
                            var match = resp.results ? resp.results.find(function(r) {
                                return r.id == customerId;
                            }) : null;
                            if (match) {
                                newOption = new Option(match.text, match.id, true, true);
                            }
                            $('#customer_id').empty().append(newOption).trigger('change');
                        },
                        error: function() {
                            $('#customer_id').empty().append(newOption).trigger('change');
                        }
                    });

                    $('#date').val(data.result.date);
                    $('#remarks').val(data.result.remarks);

                    // Load existing inquiry detail rows
                    $('#emplistbody').empty();
                    rowIndex = 0;
                    if (data.details && data.details.length > 0) {
                        $.each(data.details, function(i, detail) {
                            rowIndex++;
                            $('#emplistbody').append(`
                                <tr id="row_${rowIndex}">
                                    <td>
                                        <input type="hidden" name="detail_id[]" value="${detail.id}" />
                                        <input type="text" name="detail_inquiry[]" class="form-control form-control-sm" value="${detail.inquiry}" placeholder="Inquiry detail" required />
                                    </td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove_row" data-id="${rowIndex}"><i class="fas fa-trash-alt"></i></button></td>
                                </tr>
                            `);
                        });
                    }

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Inquiry');
                    $('#action_button').html('<i class="fas fa-pencil-alt"></i>&nbsp;Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        }
    });

    //Handle delete click
    var user_id;
    $(document).on('click', '.delete', async function() {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "{{ url('KTInquiry/destroy/') }}/" + user_id,
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
                }
            })
        }

    });

    // View modal 
    $(document).on('click', '.view', function() {
        var id = $(this).attr('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('KTInquiry/') }}/" + id + "/edit",
            type: 'GET',
            dataType: "json",
            data: { id: id },
            success: function(data) {
                $.ajax({
                    url: '{{ url("kt_customer_list_sel2") }}',
                    dataType: 'json',
                    data: { term: data.result.customer_id },
                    success: function(resp) {
                        var match = resp.results ? resp.results.find(function(r) {
                            return r.id == data.result.customer_id;
                        }) : null;
                        var label = match ? match.text : data.result.customer_id;
                        $('#view_customer_name').empty()
                            .append(new Option(label, data.result.customer_id, true, true));
                    },
                    error: function() {
                        $('#view_customer_name').empty()
                            .append(new Option(data.result.customer_id, data.result.customer_id, true, true));
                    }
                });

                $('#view_date').val(data.result.date);
                $('#view_remarks').val(data.result.remarks);
                $('#view_emplistbody').empty();
                if (data.details && data.details.length > 0) {
                    $.each(data.details, function(i, detail) {
                        $('#view_emplistbody').append(`
                            <tr>
                                <td>${detail.inquiry}</td>
                            </tr>
                        `);
                    });
                }
                $('#viewconfirmModal').modal('show');
            }
        });
    });
</script>

@endsection