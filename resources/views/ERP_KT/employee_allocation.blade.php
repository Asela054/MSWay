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


    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                    <div class="col-12 col-sm-4">
                                        <label class="small font-weight-bold text-dark">In time*</label>
                                        <input type="datetime-local" name="in_time" id="until_time" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="small font-weight-bold text-dark">Out time*</label>
                                        <input type="datetime-local" name="out_time" id="until_time" class="form-control form-control-sm" required />
                                    </div>
                                    <hr>
                                    <div class="form-row mb-1">
                                        <div class="col-12 col-sm-6">
                                            <label class="small font-weight-bold text-dark">Employee*</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                        <div class="col-12 col-sm-6">
                                            <button type="button" id="formsubmit"
                                                class="btn btn-primary btn-sm px-4 float-right"><i
                                                    class="fas fa-plus"></i>&nbsp;Add to list</button>
                                            <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                            <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                                class="btn btn-primary btn-sm px-4 fa-pull-right" style="display:none;"><i
                                                    class="fas fa-plus"></i>&nbsp;Update List</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <input type="hidden" name="detailsid" id="detailsid">
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
                                            <th class="text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableorderlist"></tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-primary btn-sm fa-pull-right px-4"><i
                                        class="fas fa-plus"></i>&nbsp;Create</button>
                            </div>
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
                // 'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "order": [
                [0, "desc"]
            ],
            ajax: {
                url: scripturl + "/employee_allocation_list.php",
                type: "POST",
                data: {},
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'date_from',
                    name: 'date_from'
                },
                {
                    data: 'shift_name',
                    name: 'shift_name'
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
                        var approval_status = row.approval_status;
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
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i> Create');

            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function() {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedText = $('#employee option:selected').text();
                var in_time = $('#in_time').val();
                var out_time = $('#out_time').val();
                
                var in_time_display = in_time.replace('T', ' ');
                var out_time_display = out_time.replace('T', ' ');

                $('#tableorder > tbody:last').append(
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
            var action_url = '';
            if ($('#action').val() == 'Add') {
                action_url = "{{ route('ky_addEmployee_Allocation') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('KTEmployee_Allocation.update') }}";
            }
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
                            html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#formTitle')[0].reset();
                            $('#tableorder tbody').empty();
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function() {
                                $('#formModal').modal('hide');
                            }, 2000);
                            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
                        }
                        $('#form_result').html(html);
                    }
                });
            } else {
                alert('Cannot Create.. Table Empty!!');
                $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus mr-2"></i> Create');
            }
        });


        $('#formUpload').on('submit', function(e) {
            e.preventDefault();
            let save_btn = $("#btn-upload");
            let btn_prev_text = save_btn.html();

            //save_btn.prop("disabled", true);
            save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...');
            let formData = new FormData($('#formUpload')[0]);

            let url_text = '{{ url("/night_shiftallocate_csv") }}';
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: url_text,
                type: 'POST',
                contentType: false,
                processData: false,
                data: formData,
                success: function(res) {
                    if (res.status == 1) {
                        $('#upload_response').html("<div class='alert alert-success'>" + res.msg + "</div>");

                        save_btn.html(btn_prev_text);
                        save_btn.prop("disabled", false);
                        $("#formUpload")[0].reset();
                        $('#uploadAtModal').scrollTop(0);
                        $('#dataTable').DataTable().ajax.reload();
                        setTimeout(function() {
                            $('#uploadAtModal').modal('hide');
                        }, 2000);

                    } else {

                        var html = '';
                        if (res.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < res.errors.length; count++) {
                                html += res.errors[count] + '<br>';
                            }
                            html += '</div>';
                        }

                        $('#upload_response').html(html);

                        save_btn.prop("disabled", false);
                        save_btn.html(btn_prev_text);
                    }
                },
                error: function(res) {
                    alert(data);
                }
            });
        });


        var user_id;

        $(document).on('click', '.delete', async function() {
            var r = await Otherconfirmation("You want to remove this?");
            if (r == true) {
                var user_id = $(this).attr('id');
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
                        id: user_id
                    },
                    success: function(data) {
                        setTimeout(function() {
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload();
                    }
                });
            }
        });

  
    });

    function productDelete(row) {
        $(row).closest('tr').remove();
    }
</script>
<script>
    $('#fromdate').on('change', function() {
        $('#todate').val($(this).val());
    });
</script>

@endsection