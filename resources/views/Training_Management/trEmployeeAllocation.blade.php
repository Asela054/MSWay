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
                    <span>Employee Allocation</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
    <div class="card">
        <div class="card-body p-2">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                        <h5 class="mb-2 mb-sm-0">Training Type: {{$type->name}} - {{$allocation->venue}}</h5>
                        <button type="button" class="btn btn-primary btn-sm" name="create_record" id="create_record">
                            <i class="fas fa-plus mr-2"></i>Add
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <hr class="border-dark">
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>EMPLOYEE NAME</th>
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
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Employees</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 p-sm-3">
                <div class="row">
                    <div class="col-12">
                        <span id="form_result"></span>
                        <form method="post" id="formTitle" class="form-horizontal">
                            {{ csrf_field() }}
                            <input type="hidden" name="action" id="action" />
                            <input type="hidden" name="hidden_id" id="hidden_id" />
                            <input type="hidden" name="detailsid" id="detailsid" />
                            
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                    <label class="small font-weight-bold text-dark">Employee</label>
                                    <select class="employee form-control form-control-sm" name="employee" id="employee" style="width:100%"></select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-sm-6 col-md-4 mb-2 mb-sm-0">
                                    <button type="button" id="addtolist" class="btn btn-primary btn-sm px-4 w-100 w-sm-auto">
                                        <i class="fas fa-plus"></i>&nbsp;Add to list
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <button type="button" id="Btnupdatelist" class="btn btn-success btn-sm px-3 w-100 w-sm-auto" style="display:none;">
                                        <i class="fas fa-edit"></i>&nbsp;Update
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
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
                            </div>
                            
                            <div class="form-group mt-3 mb-0">
                                <button type="button" name="action_button" id="action_button" class="btn btn-primary btn-sm float-right px-4">
                                    <i class="fas fa-plus"></i>&nbsp;Add
                                </button>
                            </div>
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
$(document).ready(function(){
    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#training').addClass('navbtnactive');

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
                title: 'Employee allocation Details',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Employee allocation Details', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Employee allocation Details',
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
            "url": scripturl + "/employee_allocation_training.php",
            "type": "POST",
            "data": function(d) {
                d.allocation_id = {{ $allocation->id }};  
            }
        },
        columns: [{
                data: 'emp_id',
                name: 'emp_id'
            },
            {
                data: 'employee_display',
                name: 'employee_display'
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
            {   data: "emp_name", 
                name: "emp_name", 
                visible: false
            },
            {   data: "calling_name",
                name: "calling_name", 
                visible: false
            }
        ],
    });

    // Create record button
    $('#create_record').click(function () {
        $('.modal-title').text('Employee Allocation');
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
    $('#action_button').click(function (e) {
        e.preventDefault(); 
        
        var action_url = '';
        
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('trainingEmpAllocationinsert') }}";
        }

        $('#action_button').prop('disabled', true).html(
            '<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing');

        var tbody = $("#emplistbody");

        if (tbody.children().length > 0) {
            var jsonObj = [];
            $("#emplistbody tr").each(function () {
                var item = {};
                $(this).find('td').each(function (col_idx) {
                    if (col_idx !== 2) { // Skip action column
                        item["col_" + (col_idx + 1)] = $(this).text();
                    }
                });
                jsonObj.push(item);
            });

            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    detailsid: '{{ $allocation->id }}',
                    empData: JSON.stringify(jsonObj),
                    action: $('#action').val()
                },
                dataType: "json",
                success: function (data) {
                    $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
                    
                    if (data.errors) {
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: data.errors.join(', '),
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
                    $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
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
        } else {
            $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            alert('Please add at least one employee to the list');
        }
    });

    // Delete main record
    var user_id;
    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            
            $.ajax({
                url: "{{ url('trainingEmpAllocation/destroy') }}/" + user_id,  
                method: "GET",  
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {
                    if (data.success) {
                        const actionObj = {
                            icon: 'fas fa-trash-alt',
                            title: '',
                            message: data.success,  
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    } else if (data.error) {
                        alert(data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('Response:', xhr.responseText);  
                    alert('Failed to delete employee');
                }
            });
        }
    });

});
</script>

@endsection