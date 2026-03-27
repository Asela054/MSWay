@extends('layouts.app')

@section('content')
<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.shift_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-business-time"></i></div>
                    <span>Additional Work Hours</span>
                </h1>
            </div>
        </div>
    </div>
      <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('employee-shift-allocation-create')
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
                            <button type="button" class="btn btn-secondary btn-sm fa-pull-right mr-2" name="csv_upload"
                            id="csv_upload"><i class="fas fa-plus mr-2"></i>CSV Upload</button>
                            @endif
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
                                        <th>DATE</th>
                                        <th>SHIFT</th>
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
                                                <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="fromdate" id="fromdate" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="small font-weight-bold text-dark">Until time*</label>
                                        <input type="datetime-local" name="until_time" id="until_time" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label class="small font-weight-bold text-dark">Off Next Day</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input off_next_day" name="off_next_day" id="off_next_day_0" value="0" checked>No
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input off_next_day" name="off_next_day" id="off_next_day_1" value="1">Yes
                                            </label>
                                        </div>
                                    </div>
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
                                            <th>Until Time</th>
                                            <th>Off Next Day</th>
                                            <th class="d-none"></th>
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

    <div class="modal fade" id="uploadAtModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel1" aria-hidden="true">
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
                            <a href="{{ url('/public/csvsample/Additional Work Allocation.csv') }}" class="control-label d-flex justify-content-end">CSV Format-Download Sample File</a>
                        </div>
                    </div>
                    <form method="post" id="formUpload" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col">
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Shift Type*</label>
                                        <select name="csv_shift" id="csv_shift" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">CSV File</label>
                                        <input required type="file" id="csv_file_u" name="csv_file_u" class="form-control form-control-sm" accept=".csv"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="loading"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="btn-upload" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-upload"></i>&nbsp;Upload</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="aviewmodal-title" id="staticBackdropLabel">View Employee Shifts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row mb-1">
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Shift Type*</label>
                                        <select name="view_shift" id="view_shift" class="form-control form-control-sm" style="width: 100%;" disabled>
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->shift_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="small font-weight-bold text-dark">Date*</label>
                                        <input type="date" name="view_fromdate" id="view_fromdate" class="form-control form-control-sm" required readonly style="pointer-events: none"/>
                                    </div>
                                    <div class="col-4 d-none">
                                        <label class="small font-weight-bold text-dark">Date To*</label>
                                        <input type="date" name="view_todate" id="view_todate" class="form-control form-control-sm" required readonly style="pointer-events: none"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="center-block fix-width scroll-inner table-responsive">
                                    <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                        <thead>
                                            <tr>
                                                <th>Emp ID</th>
                                                <th>Employee Name</th>
                                                <th>Until Time</th>
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
    $(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#employeeshift_link').addClass('navbtnactive');

        $('#viewconfirmModal .close').click(function(){
            $('#viewconfirmModal').modal('hide');
        });

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
                    title: 'Additional work  Information',
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
                    title: 'Additional work  Information',
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
                url: scripturl + "/employeeshiftlist.php",
                type: "POST",
                data: {},
            },
            columns: [
                { 
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
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';
                        // View button
                        buttons += '<button name="view" id="'+row.id+'" class="view btn btn-secondary btn-sm mr-1" type="button"><i class="fas fa-eye"></i></button>';
                        // Edit button
                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        // Delete button
                        buttons += '<button type="button" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm mr-1" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                        return buttons;
                    }

                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        $('#create_record').click(function () {
            $('.modal-title').text('Add Shift');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i> Create');
            
            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var emp_id = $('#employee').val();
                var selectedText = $('#employee option:selected').text();
                var until_time = $('#until_time').val();
                var off_next_day = $('input[name="off_next_day"]:checked').val();
                var off_next_day_label = off_next_day == '1' ? 'Yes' : 'No';

                var until_time_display = until_time.replace('T', ' ');

                $('#tableorder > tbody:last').append(
                    '<tr class="pointer">' +
                    '<td>' + emp_id + '</td>' +
                    '<td>' + selectedText + '</td>' +
                    '<td>' + until_time_display + '</td>' +
                    '<td>' + off_next_day_label + '</td>' +
                    '<td class="d-none">NewData</td>' +
                    '<td class="d-none"></td>' +
                    '<td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td>' +
                    '</tr>'
                );

                $('#employee').val('').trigger('change');
            }
        });

        $('#btncreateorder').click(function () {
            var action_url = '';
            if ($('#action').val() == 'Add') {
                action_url = "{{ route('employeeshiftinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('employeeshiftupdate') }}";
            }
            $('#btncreateorder').prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');
            var tbody = $("#tableorder tbody");

            if (tbody.children().length > 0) {
                var jsonObj = [];
                $("#tableorder tbody tr").each(function () {
                    var item = {};
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });

                var shift = $('#shift').val();
                var datefrom = $('#fromdate').val();
                var until_time = $('#until_time').val();          
                var off_next_day = $('input[name="off_next_day"]:checked').val();  
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        shift: shift,
                        datefrom: datefrom,
                        until_time: until_time,
                        off_next_day: off_next_day,
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
                            $('#tableorder tbody').empty();
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function () {
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

        // edit function
        $(document).on('click', '.edit', function () {
            var id = $(this).attr('id');

            $('#form_result').html('');
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $.ajax({
                url: '{!! route("employeeshiftedit") !!}',
                type: 'POST',
                dataType: "json",
                data: { id: id },
                success: function (data) {
                    $('#shift').val(data.result.mainData.shift_id);
                    $('#fromdate').val(data.result.mainData.date_from);

                    if (data.result.until_time) {
                        var ut = data.result.until_time.replace(' ', 'T').substring(0, 16);
                        $('#until_time').val(ut);
                    }
                    if (data.result.off_next_day == 1) {
                        $('#off_next_day_1').prop("checked", true);
                    } else {
                        $('#off_next_day_0').prop("checked", true);
                    }

                    $('#tableorderlist').html(data.result.requestdata);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Shift');
                    $('#btncreateorder').html('<i class="fas fa-plus"></i> Update Request');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            });
        });

          // request detail edit
          $(document).on('click', '.btnEditlist', function () {
            var id = $(this).attr('id');
            $('#employee').val('').trigger('change');

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $.ajax({
                url: '{!! route("employeeshifteditdetails") !!}',
                type: 'POST',
                dataType: "json",
                data: { id: id },
                success: function (data) {
                    // Load employee into select2
                    var option = new Option(data.result.employee_name, data.result.emp_id, true, true);
                    $('#employee').append(option).trigger('change');

                    // Load until_time and off_next_day
                    $('#until_time').val(data.result.until_time);
                    if (data.result.off_next_day == 1) {
                        $('#off_next_day_1').prop("checked", true);
                    } else {
                        $('#off_next_day_0').prop("checked", true);
                    }

                    $('#detailsid').val(data.result.id);
                    $('#Btnupdatelist').show();
                    $('#formsubmit').hide();
                }
            });
        });

        // request detail update list
        $(document).on("click", "#Btnupdatelist", function () {
        if (!$("#formTitle")[0].checkValidity()) {
            $("#submitBtn").click();
        } else {
            var emp_id = $('#employee').val();
            var selectedOption = $('#employee option:selected');
            var employeename = selectedOption.text();
            var until_time = $('#until_time').val();
            var until_time_display = until_time.replace('T', ' ');
            var off_next_day = $('input[name="off_next_day"]:checked').val();
            var off_next_day_label = off_next_day == '1' ? 'Yes' : 'No';
            var detailid = $('#detailsid').val();

            $("#tableorder > tbody").find('input[name="hiddenid"]').each(function () {
                if ($(this).val() == detailid) {
                    $(this).parents("tr").remove();
                }
            });

            $('#tableorder > tbody:last').append(
                '<tr class="pointer">' +
                '<td name="empid">' + emp_id + '</td>' +
                '<td name="empname">' + employeename + '</td>' +
                '<td>' + until_time_display + '</td>' +
                '<td>' + off_next_day_label + '</td>' +
                '<td class="d-none">Updated</td>' +
                '<td class="d-none">' + detailid + '</td>' +
                '<td class="text-right">' +
                    '<button type="button" id="' + detailid + '" class="btnEditlist btn btn-primary btn-sm"><i class="fas fa-pen"></i></button>' +
                    '&nbsp;<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>' +
                '</td>' +
                '<td class="d-none"><input type="hidden" name="hiddenid" value="' + detailid + '"></td>' +
                '</tr>'
            );

            $('#employee').val('').trigger('change');
            $('#until_time').val('');
            $('#off_next_day_0').prop("checked", true);
            $('#Btnupdatelist').hide();
            $('#formsubmit').show();
        }
    });

        //details delete
        $(document).on('click', '.btnDeletelist', async function () {
            var r = await Otherconfirmation("You want to remove this?");
            if (r == true) {
                var rowid = $(this).attr('rowid');
                $('#form_result').html('');
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });
                $.ajax({
                    url: '{!! route("employeeshiftdeletelist") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: { id: rowid },
                    success: function (data) {
                        setTimeout(function () {
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload();
                    }
                });
            }
        });

        $('#csv_upload').click(function () {
                $('#uploadAtModal').modal('show');
                $('#upload_response').html('');
        });
        $('#formUpload').on('submit',function(e) {
                e.preventDefault();
                let save_btn=$("#btn-upload");
                let btn_prev_text = save_btn.html();
               
                //save_btn.prop("disabled", true);
                save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
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
                            $('#upload_response').html("<div class='alert alert-success'>"+res.msg+"</div>");

                            save_btn.html(btn_prev_text);
                            save_btn.prop("disabled", false);
                            $("#formUpload")[0].reset();
                            $('#uploadAtModal').scrollTop(0);
                            $('#dataTable').DataTable().ajax.reload();
                            setTimeout(function(){
                                $('#uploadAtModal').modal('hide');
                            }, 2000);

                        }else {

                            var html = '';
                            if (res.errors) {
                                html = '<div class="alert alert-danger">';
                                for (var count = 0; count < res.errors.length; count++) {
                                    html +=   res.errors[count]+'<br>' ;
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

        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this?");
            if (r == true) {
                var user_id = $(this).attr('id');
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                });
                $.ajax({
                    url: '{!! route("employeeshiftdelete") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: { id: user_id },
                    success: function (data) {
                        setTimeout(function () {
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload();
                    }
                });
            }
        });

        // view modal 
        $(document).on('click', '.view', function () {
            id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("employeeshiftview") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#view_shift').val(data.result.mainData.shift_id); 
                    $('#view_fromdate').val(data.result.mainData.date_from); 
                    $('#view_todate').val(data.result.mainData.date_to); 
                    $('#view_tableorderlist').html(data.result.requestdata);

                    $('#viewconfirmModal').modal('show');

                }
            })


        });

        $('#csv_sample').click(function () {
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