@extends('layouts.app')

@section('content')
<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('ERP_KT.erp_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-industry"></i></div>
                    <span>Employee Allocation</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
                        <button type="button" class="btn btn-secondary btn-sm fa-pull-right mr-2" name="csv_upload"
                            id="csv_upload"><i class="fas fa-upload mr-2"></i>CSV Upload</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>DATE</th>
                                        <th>IN TIME</th>
                                        <th>OUT TIME</th>
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


    <!-- Add Shift Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Shift Type*</label>
                                        <select name="shift" id="shift" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift_name }} - {{ $shift->shift_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="fromdate" id="fromdate" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-6 mt-2">
                                        <label class="small font-weight-bold text-dark">In Time*</label>
                                        <input type="datetime-local" name="in_time" id="in_time" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-6 mt-2">
                                        <label class="small font-weight-bold text-dark">Out Time*</label>
                                        <input type="datetime-local" name="out_time" id="out_time" class="form-control form-control-sm" required />
                                    </div>
                                    <hr class="w-100 mt-3">
                                    <div class="col-12 col-sm-6 mt-2">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="employee" id="employee" class="form-control form-control-sm" style="width:100%;" required>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <button type="button" id="formsubmit"
                                            class="btn btn-primary btn-sm px-4 float-right"><i
                                                class="fas fa-plus mr-2"></i>&nbsp;Add to list</button>
                                        <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </div>
                            </form>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                    <thead>
                                        <tr>
                                            <th>Emp ID</th>
                                            <th>Employee Name</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th class="d-none"></th>
                                            <th class="d-none"></th>
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableorderlist"></tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder" class="btn btn-primary btn-sm fa-pull-right mr-2 px-4">
                                    <i class="fas fa-plus mr-2"></i>Create
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Shift Modal Area End -->

    <!--CSV Modal Area Start -->
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
                            <a href="{{ url('/public/csvsample/kt_employee_allocation.csv') }}" class="control-label d-flex justify-content-end">CSV Format - Download Sample File</a>
                        </div>
                    </div>
                    <form method="post" id="formUpload" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Shift Type*</label>
                                        <select name="csv_shift" id="csv_shift" class="form-control form-control-sm" style="width: 100%;" required>
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift_name }} - {{ $shift->shift_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">CSV File*</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv" />
                                    </div>
                                </div>
                                <small class="text-muted">CSV format: Date: 2026-01-01, Time: 20:00:00 (Use this format to upload data)</small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    <button type="submit" name="action_button" id="btn-upload" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-upload"></i>&nbsp;Upload</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--CSV Modal Area End -->
</main>

@endsection


@section('script')

<script>
    $(document).ready(function() {

        $('#erp_menu_link_KT').addClass('active');
        $('#erp_menu_link_KT_icon').addClass('active');
        $('#erp_kt_shiftot').addClass('navbtnactive');

        // Employee Select2 Initialization
        let employee = $('#employee');
        employee.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#formModal'),
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

        // DataTable Initialization
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Allocation Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'Employee Allocation Information',
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'legal',
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Allocation Information',
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
                url: scripturl + "/ERP_KT/employee_allocation_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'employee_name',
                    name: 'employee_name'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'in_time',
                    name: 'in_time'
                },
                {
                    data: 'out_time',
                    name: 'out_time'
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        buttons += '<button type="button" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm mr-1" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        return buttons;
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Add Record
        $('#create_record').click(function() {
            $('.modal-title').text('Add Employee Shift');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#tableorderlist').empty();
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
            $('#formModal').modal('show');
        });

        // Add to list button
        $("#formsubmit").click(function() {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedText = $('#employee option:selected').text();
                var in_time = $('#in_time').val();
                var out_time = $('#out_time').val();

                if (!emp_id) {
                    alert('Please select an employee.');
                    return;
                }

                var duplicate = false;
                $('#tableorderlist tr').each(function() {
                    if ($(this).find('td:first').text() == emp_id) {
                        duplicate = true;
                        return false;
                    }
                });
                if (duplicate) {
                    alert('This employee is already added to the list.');
                    return;
                }


                var in_time_display = in_time ? in_time.replace('T', ' ') : '';
                var out_time_display = out_time ? out_time.replace('T', ' ') : '';

                $('#tableorderlist').append(
                    '<tr class="pointer">' +
                    '<td>' + emp_id + '</td>' +
                    '<td>' + selectedText + '</td>' +
                    '<td>' + in_time_display + '</td>' +
                    '<td>' + out_time_display + '</td>' +
                    '<td class="d-none">NewData</td>' +
                    '<td class="d-none"></td>' +
                    '<td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>' +
                    '</tr>'
                );

                $('#employee').val('').trigger('change');
            }
        });

        // Save Record
        $('#btncreateorder').click(function() {
            var action_url = "{{ route('ky_addEmployee_Allocation') }}";

            $('#btncreateorder').prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');
            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableorder tbody tr").each(function() {
                    var item = {};
                    $(this).find('td').each(function(col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });

                var shift = $('#shift').val();
                var datefrom = $('#fromdate').val();
                var in_time = $('#in_time').val();
                var out_time = $('#out_time').val();
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        shift: shift,
                        datefrom: datefrom,
                        in_time: in_time,
                        out_time: out_time,
                        hidden_id: hidden_id,
                    },
                    url: action_url,
                    success: function(data) {
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            Swal.fire({
                                position: "top-end",
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: false,
                                timer: 2500
                            });
                            $('#formTitle')[0].reset();
                            $('#tableorderlist').empty();
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function() {
                                $('#formModal').modal('hide');
                            }, 2000);
                            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
                        }
                        $('#form_result').html(html);
                        if (!data.success) {
                            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
                    }
                });
            } else {
                alert('Cannot Create.. Table Empty!!');
                $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
            }
        });

        // CSV Upload button
        $('#csv_upload').click(function() {
            $('#uploadAtModal').modal('show');
            $('#upload_response').html('');
        });

        // CSV Upload form submit
        $('#formUpload').on('submit', function(e) {
            e.preventDefault();
            let save_btn = $("#btn-upload");
            let btn_prev_text = save_btn.html();

            save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...');
            let formData = new FormData($('#formUpload')[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("kt_employee_allocation_csv") }}',
                type: 'POST',
                contentType: false,
                processData: false,
                data: formData,

                success: function(res) {
                    if (res.status == 1) {
                        var html = '';
                        if (res.errors && res.errors.length > 0) {
                            html = '<div class="alert alert-warning"><strong>Some rows had issues:</strong><br>' + res.errors.join('<br>') + '</div>';
                        }
                        $('#upload_response').html(html);
                        Swal.fire({
                            position: "top-end",
                            icon: 'success',
                            title: res.msg,
                            showConfirmButton: false,
                            timer: 2500
                        });

                        save_btn.html(btn_prev_text);
                        save_btn.prop("disabled", false);
                        $("#formUpload")[0].reset();
                        $('#uploadAtModal').scrollTop(0);
                        $('#dataTable').DataTable().ajax.reload();

                        $("#formUpload")[0].reset();
                        if (!res.errors || res.errors.length === 0) {
                            setTimeout(function() {
                                $('#uploadAtModal').modal('hide');
                            }, 2000);
                        }

                    } else {
                        var html = '';
                        if (res.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < res.errors.length; count++) {
                                html += res.errors[count] + '<br>';
                            }
                            html += '</div>';
                        }
                        if (res.msg) {
                            html += '<div class="alert alert-warning">' + res.msg + '</div>';
                        }
                        $('#upload_response').html(html);
                        save_btn.prop("disabled", false);
                        save_btn.html(btn_prev_text);
                    }
                },
                error: function(xhr) {
                    alert('Upload failed. Please try again.');
                    save_btn.prop("disabled", false);
                    save_btn.html(btn_prev_text);
                }
            });
        });

        // Delete record
        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this?");
            if (r == true) {
                var record_id = $(this).attr('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{!! route("KTEmployee_Allocation/destroy/") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        id: record_id
                    },
                    success: function(data) {
                        Swal.fire({
                            position: "top-end",
                            icon: 'error',
                            title: data.success,
                            showConfirmButton: false,
                            timer: 2500
                        });
                        setTimeout(function() {
                            $('#dataTable').DataTable().ajax.reload();
                        }, 500);
                    },
                    error: function() {
                        alert('Delete failed. Please try again.');
                    }
                });
            }
        });

    });

    function productDelete(row) {
        $(row).closest('tr').remove();
    }
</script>

@endsection