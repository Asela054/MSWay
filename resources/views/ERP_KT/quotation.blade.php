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
                    <span>Quotation</span>
                </h1>
            </div>
        </div>
    </div>


       <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight"
                            aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Records</button>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Quotation</h5>
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
                                    <select name="customer_id" id="customer_id" class="form-control form-control-sm" disabled>
                                    <option value="">Select Customer...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" readonly/>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" readonly/>
                                </div>
                            </div>
                             <div class="col-12 mt-2">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Inquiry</th>
                                                <th>Quotation</th>
                                            </tr>
                                        </thead>
                                        <tbody id="emplistbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                              <div class="col-12">
                                <div class="form-group mt-3 mb-0">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Update</button>
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

    <!-- filter function -->
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
                            <label class="small font-weight-bolder text-dark">Customer Name</label>
                            <select name="customer_f" id="customer_f" class="form-control form-control-sm"></select>
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
    <!-- filter function end-->
    
</main>
              
@endsection

@section('script')

<script>
var rowIndex = 0;
$(document).ready(function(){

   $('#erp_menu_link_KT').addClass('active');
   $('#erp_menu_link_KT_icon').addClass('active');
   $('#erp_kt_calculations').addClass('navbtnactive');



    // Customer Select2 Initialization
    let customer = $('#customer_id');
    customer.select2({
    placeholder: 'Select Customer...',
    width: '100%',
    allowClear: true,
    dropdownParent: $('body'),
    ajax: {
        url: '{{ url("kt_customer_list_sel2") }}',
        dataType: 'json',
        data: function(params) {
            return { term: params.term || '', page: params.page || 1 }
        },
        cache: true
    }
    });

    let customer_f = $('#customer_f');
    customer_f.select2({
    placeholder: 'Select Customer...',
    width: '100%',
    allowClear: true,
    dropdownParent: $('body'),
    ajax: {
        url: '{{ url("kt_customer_list_sel2") }}',
        dataType: 'json',
        data: function(params) {
            return { term: params.term || '', page: params.page || 1 }
        },
        cache: true
    }
    });

    function load_dt(customer_id, from_date, to_date) {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().destroy();
                $('#dataTable tbody').empty();
    }
    $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Quotation  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Quotation Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Quotation Information',
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
            data: {
                customer_id: customer_id,
                from_date: from_date,
                to_date: to_date
            },
        },
        columns: [
            { 
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
                    
                    buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Add Quotation"><i class="fas fa-file-invoice-dollar"></i></button>';

                    buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                    return buttons;
                }
            }
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    }

    load_dt('', '', '');
 
    //Submit function
    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('kt_addQuotation') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('KTQuotation.update') }}";
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

    //Handle edit click
    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Add Quotation ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "{{ url('KTQuotation/') }}/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    var customerId   = data.result.customer_id;
                    
                    // Clear previous selection
                    $('#customer_id').val(null).trigger('change');

                    // customer id → name and populate Select2
                    var newOption = new Option(customerId, customerId, true, true);
                    
                    $.ajax({
                        url: '{{ url("kt_customer_list_sel2") }}',
                        dataType: 'json',
                        data: { term: customerId },
                        success: function(resp) {
                            var match = resp.results ? resp.results.find(function(r){ return r.id == customerId; }) : null;
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
                                        <input type="text" name="detail_inquiry[]" class="form-control form-control-sm" value="${detail.inquiry}" placeholder="Inquiry detail"  readonly required />
                                    </td>
                                     <td>
                                        <input type="number" step="0.01" name="detail_quotation[]" class="form-control form-control-sm text-right" value="${detail.quotation || ''}" placeholder="0.00" />
                                    </td>
                                </tr>
                            `);
                        });
                    }

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Add Quotation');
                    $('#action_button').html('<i class="fas fa-pencil-alt"></i>&nbsp;Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        }
    });

    //Handle delete click
    var user_id;
    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "{{ url('KTQuotation/destroy/') }}/" + user_id,
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

        //filter handler
        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let customer = $('#customer_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            if (!from_date || !to_date) {
                alert('Please select both From and To dates');
                return;
            }
            
            if (from_date > to_date) {
                alert('From date cannot be greater than To date');
                return;
            }

            load_dt(customer, from_date, to_date);
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').click(function() {
            $('#formFilter')[0].reset();
            $('#customer_f').val(null).trigger('change');
            load_dt('', '', '');
        });

    });
</script>

@endsection