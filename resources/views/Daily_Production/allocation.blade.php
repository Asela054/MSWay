@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid">
             @include('layouts.production&task_nav_bar')
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right mr-2" name="create_record"
                        id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee</button>
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
                                    <th>Date</th>
                                    <th>Machine</th>
                                    <th>Product</th>
                                    <th class="text-right">Action</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Production</h5>
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
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" name="production_date" id="production_date"
                                            class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Shift</label>
                                        <select name="shift" id="shift" class="form-control form-control-sm">
                                            <option value="">Please Select</option>
                                            @foreach($shifttype as $shifttypes)
                                            <option value="{{$shifttypes->id}}">{{$shifttypes->shift_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Product</label>
                                        <select name="product" id="product" class="form-control form-control-sm" style="width: 100%;" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->productname }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="machine" id="machine" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="small font-weight-bold text-dark">Employee</label>
                                        <select class="employee form-control form-control-sm" name="employee" id="employee" style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" id="addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" id="Btnupdatelist" class="btn btn-success btn-sm px-3" style="margin-top:30px; display:none;"><i class="fas fa-edit"></i>&nbsp;Update</button>
                                    </div>
                                </div>

                                <br>
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
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
                                
                                <div class="form-group mt-3">
                                    <button type="button" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
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

    <!-- Confirm Delete List Modal -->
    <div class="modal fade" id="confirmModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                    <button type="button" name="ok_button2" id="ok_button2"
                        class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm closebtn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="aviewmodal-title" id="staticBackdropLabel">View Employee Product Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row mb-1">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" name="view_production_date" id="view_production_date"
                                            class="form-control form-control-sm" readonly />
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Shift</label>
                                        <select name="view_shift" id="view_shift" class="form-control form-control-sm" disabled>
                                            <option value="">Please Select</option>
                                            @foreach($shifttype as $shifttypes)
                                            <option value="{{$shifttypes->id}}">{{$shifttypes->shift_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="view_machine" id="view_machine" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            <option value="">Select Machine</option>
                                            @foreach ($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Product</label>
                                        <select name="view_product" id="view_product" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->productname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                        <thead>
                                            <tr>
                                                <th>Emp ID</th>
                                                <th>Employee Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view_tableorderlist"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->     
</main>
              
@endsection

@section('script')
<script>
$(document).ready(function(){
    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailyprocess').addClass('navbtnactive');

    // Modal close handlers
    $('#viewconfirmModal .close').click(function(){
        $('#viewconfirmModal').modal('hide');
    });

    $('#confirmModal2 .close').click(function(){
        $('#confirmModal2').modal('hide');
    });
    $('#confirmModal2 .closebtn').click(function(){
        $('#confirmModal2').modal('hide');
    });

    let machine = $('#machine');
    let product = $('#product');

    machine.select2({
        placeholder: 'Select a Machine',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("Machine_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    product: product.val(),
                }
            },
            cache: true
        }
    });

    // Product change handler to refresh machine options
    product.on('change', function() {
        machine.val(null).trigger('change');
        machine.select2({
            placeholder: 'Select a Machine',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("Machine_list_sel2")}}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1,
                        product: product.val(),
                    }
                },
                cache: true
            }
        });
    });

    // Employee Select2 initialization
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

    // DataTable initialization
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{!! route('productallocationlist') !!}",
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
                data: 'machine',
                name: 'machine'
            },
            {
                data: 'productname',
                name: 'product'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<div style="text-align: right;">' + data + '</div>';
                }
            },
        ],
        "bDestroy": true,
        "order": [
            [0, "desc"]
        ]
    });

    // Create record button
    $('#create_record').click(function () {
        $('.modal-title').text('Add Production Allocation');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#emplistbody').empty();
        $('#employee').val('').trigger('change');
        $('#Btnupdatelist').hide();
        $('#formModal').modal('show');
    });

    // Add to list functionality
    $('#addtolist').click(function () {
        if (!$('#employee').val()) {
            alert('Please select an employee');
            return;
        }
        
        if (!$('#machine').val() || !$('#product').val()) {
            alert('Please select machine and product');
            return;
        }

        var emp_id = $('#employee').val();
        var selectedText = $('#employee option:selected').text();

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
                '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                    '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '<td class="d-none">NewData</td>' +
        '</tr>');

        $('#employee').val('').trigger('change');
    });

    // Form submission
    $('#action_button').click(function () {
        var action_url = '';
        
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('productallocationinsert') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('productallocationupdate') }}";
        }

        $('#action_button').prop('disabled', true).html(
            '<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing');

        var tbody = $("#emplistbody");

        if (tbody.children().length > 0) {
            var jsonObj = [];
            $("#emplistbody tr").each(function () {
                var item = {};
                $(this).find('td').each(function (col_idx) {
                    if (col_idx !== 2) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    }
                });
                jsonObj.push(item);
            });

            var machine = $('#machine').val();
            var product = $('#product').val();
            var shift = $('#shift').val();
            var date = $('#production_date').val(); 
            var hidden_id = $('#hidden_id').val();

            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    machine: machine,
                    product: product,
                    shift: shift,
                    date: date,
                    hidden_id: hidden_id,
                },
                url: action_url,
                success: function (data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#formTitle')[0].reset();
                        $('#emplistbody').empty();
                        $('#dataTable').DataTable().ajax.reload();
                        setTimeout(function(){
                            $('#formModal').modal('hide');
                        }, 2000);
                    }
                    $('#form_result').html(html);
                    $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
                },
                error: function(xhr, status, error) {
                    var html = '<div class="alert alert-danger">An error occurred: ' + error + '</div>';
                    $('#form_result').html(html);
                    $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
                }
            });
        } else {
            alert('Cannot Create..Table Empty!!');
            $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
        }
    });

    // Edit function
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("productallocationedit") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                $('#production_date').val(data.result.mainData.date);
                setTimeout(function() {
                    var machineId = data.result.mainData.machine_id;
                    var machineName = data.result.mainData.machine_name || 'Selected Machine'; 
                    
                    if (machine.find("option[value='" + machineId + "']").length === 0) {
                        var newOption = new Option(machineName, machineId, true, true);
                        machine.append(newOption);
                    }
                    machine.val(machineId).trigger('change');
                }, 500); 
                
                $('#product').val(data.result.mainData.product_id).trigger('change');
                $('#shift').val(data.result.mainData.shift_id).trigger('change'); 
                $('#emplistbody').html(data.result.requestdata);
                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Production Allocation');
                $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Update');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            }
        })
    });

    // Edit list item
    $(document).on('click', '.btnEditlist', function () {
        var id = $(this).attr('id');
        $('#employee').val('').trigger('change');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("productallocationeditdetails") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                $('#employee').val(data.result.emp_id).trigger('change');
                $('#detailsid').val(data.result.id);
                $('#Btnupdatelist').show();
                $('#addtolist').hide();
            }
        })
    });

    // Update list item
    $(document).on("click", "#Btnupdatelist", function () {
        if (!$('#employee').val()) {
            alert('Please select an employee');
            return;
        }

        var emp_id = $('#employee').val();
        var selectedText = $('#employee option:selected').text();
        var detailid = $('#detailsid').val();

        $("#emplistbody tr").each(function () {
            var hiddenInputs = $(this).find('input[name="hiddenid"]');
            if (hiddenInputs.length > 0 && hiddenInputs.val() == detailid) {
                $(this).remove();
            }
        });

        $('#emplistbody').append('<tr class="pointer">' +
            '<td>' + emp_id + '</td>' +
            '<td>' + selectedText + '</td>' +
            '<td class="text-right">' +
                '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                    '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '<td class="d-none">Updated</td>' +
            '<td class="d-none"><input type="hidden" name="hiddenid" value="' + detailid + '"></td>' +
        '</tr>');

        $('#employee').val('').trigger('change');
        $('#Btnupdatelist').hide();
        $('#addtolist').show();
    });

    // Delete list item
    var rowid;
    $(document).on('click', '.btnDeletelist', function () {
        rowid = $(this).attr('rowid');
        $('#confirmModal2').modal('show');
    });

    $('#ok_button2').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("productallocationdeletelist") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                id: rowid
            },
            beforeSend: function () {
                $('#ok_button2').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal2').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    location.reload();
                }, 1000);
            }
        })
    });

    // Delete main record
    var user_id;
    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        
        $.ajax({
            url: '{!! route("productallocationdelete") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                id: user_id
            },
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                }, 1000);
            }
        })
    });

    // View modal 
    $(document).on('click', '.view', function () {
        var id = $(this).attr('id');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $.ajax({
            url: '{!! route("productallocationview") !!}',
            type: 'POST',
            dataType: "json",
            data: {
                id: id
            },
            success: function (data) {
                $('#view_production_date').val(data.result.mainData.date);
                $('#view_machine').val(data.result.mainData.machine_id).trigger('change');
                $('#view_product').val(data.result.mainData.product_id).trigger('change');
                $('#view_shift').val(data.result.mainData.shift_id).trigger('change');
                $('#view_tableorderlist').html(data.result.requestdata);
                $('#viewconfirmModal').modal('show');
            }
        })
    });
});

function productDelete(row) {
    $(row).closest('tr').remove();
}

function deactive_confirm() {
    return confirm("Are you sure you want to deactivate this?");
}

function active_confirm() {
    return confirm("Are you sure you want to activate this?");
}
</script>

@endsection