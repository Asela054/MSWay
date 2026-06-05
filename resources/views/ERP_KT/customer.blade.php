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
                    <span>Customer</span>
                </h1>
            </div>
        </div>
    </div>


       <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Customer</button>
                            <button type="button" class="btn btn-secondary btn-sm fa-pull-right mr-2" name="csv_upload" id="csv_upload"><i class="fas fa-upload mr-2"></i>CSV Upload</button>
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
                                    <th>NAME</th>
                                    <th>CONTACT NUMBER</th>
                                    <th>EMAIL</th>
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
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Customer</h5>
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
                                    <label class="small font-weight-bold text-dark">Name <span class="text-red">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Contact Number</label>
                                    <input type="text" name="contact_number" id="contact_number" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Email</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-sm"/>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control form-control-sm" />
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

    <!-- CSV Upload Modal -->
    <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="csvmodal-title" id="staticBackdropLabel1">Upload CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="upload_response"></div>
                    <div class="row">
                        <div class="col">
                            <a href="{{ url('/public/csvsample/KT Customer.csv') }}" class="control-label d-flex justify-content-end">
                                CSV Format - Download Sample File
                            </a>
                        </div>
                    </div>
                    <form method="post" id="formUpload" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">CSV File</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="btn-upload" class="btn btn-primary btn-sm fa-pull-right px-4">
                                        <i class="fas fa-upload"></i>&nbsp;Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</main>
              
@endsection

@section('script')

<script>
$(document).ready(function(){

   $('#erp_menu_link_KT').addClass('active');
   $('#erp_menu_link_KT_icon').addClass('active');
   $('#erp_kt_master').addClass('navbtnactive');

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
                title: 'Customer  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Customer Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Customer Information',
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
            url: scripturl + "/ERP_KT/customer_list.php",
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
                data: 'contact_number', 
                name: 'contact_number'
            },
            { 
                data: 'email', 
                name: 'email'
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
        $('.modal-title').text('Add Customer');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');

        $('#formModal').modal('show');
    });


    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('kt_addCustomer') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('KTCustomer.update') }}";
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
                url: "{{ url('KTCustomer/') }}/" + id + "/edit",
                dataType: "json",
                success: function (data) {
                    $('#name').val(data.result.name);
                    $('#contact_number').val(data.result.contact_number);
                    $('#email').val(data.result.email);
                    $('#remarks').val(data.result.remarks);

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Customer');
                    $('#action_button').html('<i class="fas fa-pencil-alt"></i>&nbsp;Edit');
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
                url: "{{ url('KTCustomer/destroy/') }}/" + user_id,
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


    var currentCustomerId = null;
    var empList = [];
    var deleteList = []; 

    $(document).on('click', '.view', function () {
        currentCustomerId = $(this).attr('id');
        empList = [];
        deleteList = [];
        $('#emplistbody').empty();

        $('#emp_machine').val(currentCustomerId);
        $('#emp_machine_hidden').val(currentCustomerId);

        $.ajax({
            url: '{{ url("KTCustomer") }}/' + currentCustomerId + '/employees',
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

    // CSV Upload functionality
    $('#csv_upload').click(function() {
        $('#uploadAtModal').modal('show');
        $('#upload_response').html('');
        $('#formUpload')[0].reset();
    });

    $('#formUpload').on('submit', function(e) {
        e.preventDefault();
        let save_btn = $("#btn-upload");
        let btn_prev_text = save_btn.html();
        
        save_btn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        let formData = new FormData($('#formUpload')[0]);
        
        $.ajax({
            url: '{{ route("kt_customer_upload_csv") }}',
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success: function(res) {
                if (res.status) {
                    let successHtml = `<div class='alert alert-success'>${res.msg}</div>`;
                    
                    if (res.errors && res.errors.length > 0) {
                        let errorHtml = '<div class="alert alert-warning mt-2"><strong>Some issues occurred:</strong><ul>';
                        res.errors.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                        errorHtml += '</ul></div>';
                        successHtml += errorHtml;
                    }
                    
                    $('#upload_response').html(successHtml);
                    
                    if (!res.errors || res.errors.length === 0) {
                        $("#formUpload")[0].reset();
                        setTimeout(function() {
                            $('#uploadAtModal').modal('hide');
                            Swal.fire({
                                position: "top-end",
                                icon: 'success',
                                title: res.msg,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }, 2000);
                    }
                } else {
                    let html = '<div class="alert alert-danger">';
                    if (res.errors && Array.isArray(res.errors)) {
                        html += '<strong>Errors occurred:</strong><ul>';
                        res.errors.forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += res.msg || 'Something went wrong. Please check your file.';
                    }
                    html += '</div>';
                    $('#upload_response').html(html);
                }
                
                save_btn.html(btn_prev_text);
                $('#uploadAtModal').scrollTop(0);
                $('#dataTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Something went wrong. Please check your file.';
                Swal.fire({
                    position: "top-end",
                    icon: 'error',
                    title: errorMessage,
                    showConfirmButton: false,
                    timer: 2500
                });
                save_btn.html(btn_prev_text);
            }
        });
    });

});
</script>

@endsection