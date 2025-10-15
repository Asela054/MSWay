@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.functional_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-chart-user"></i></div>
                    <span>KPI Allocation</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
               
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
                    
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner table-responsive">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>YEAR</th>
                                        <th>KRA</th>
                                        <th>KPI</th>
                                        <th>PARAMETER</th>
                                        <th>PARAMETER WEIGHTAGE</th>
                                        <th>MEASUREMENT</th>        
                                        <th>FIGURE</th>
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
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Kpi Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-3 mb-3 mb-lg-0">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">

                                    <div class="col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Kpi Year*</label>
                                        <select name="year" id="year" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select Year</option>
                                            @foreach($kpiyears as $kpiyear)
                                            <option value="{{$kpiyear->id}}">{{$kpiyear->year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Functional KRA*</label>
                                        <select name="type" id="type" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select KRA</option>
                                            @foreach($functionaltypes as $functionaltype)
                                            <option value="{{$functionaltype->id}}">{{$functionaltype->type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                 
                                    <div class="col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">KPI*</label>
                                        <select name="kpi" id="kpi" class="form-control form-control-sm" required>
                                        <option value="">Select KPI</option>
                                    </select>
                                    </div>
                                 
                                    <div class="col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Parameter*</label>
                                        <select name="parameter" id="parameter" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select Parameter</option>
                                    </select>
                                    </div>
                                 
                                    <div class="col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Measurement*</label>
                                        <select name="measurement" id="measurement" class="form-control form-control-sm" onchange="getDepartment()" required>
                                             <option value="">Select Measurement</option>
                                        </select>
                                    </div>
                                 
                                    <div class="col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Figure*</label>
                                        <input type="number" id="figure" name="figure" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12 px-0">
                                <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="dept_tableorder">
                                <thead>
                                        <tr>
                                        <th>DEPARTMENT</th>
                                        <th>WEIGHTAGE</th>
                                        </tr>
                                </thead>
                                <tbody id="dept_tableorderlist"></tbody>
                                </table>
                                </div>
                                </div>
                                
                                <div class="form-group mt-3 mb-0">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-4 float-right"><i
                                            class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                        class="btn btn-primary btn-sm px-4 fa-pull-right" style="display:none;"><i
                                            class="fas fa-plus"></i>&nbsp;Update List</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="hidden_department_figures" id="hidden_department_figures" />
                                <input type="hidden" name="oprderdetailsid" id="oprderdetailsid">

                            </form>
                        </div>
                        <div class="col-12 col-lg-9">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>MEASUREMENT</th>
                                        <th>FIGURE</th>
                                        <th class="text-right">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody id="tableorderlist"></tbody>
                            </table>
                            </div>
                            <div class="form-group mt-2 mb-0">
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

    <div class="modal fade" id="viewInserListModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title">Department List</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="viewInserListTableorder">
                                    <thead>
                                        <tr>
                                            <th>DEPARTMENT</th>
                                            <th>FIGURE</th>
                                        </tr>
                                    </thead>
                                    <tbody id="viewInserListTableorderlist"></tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">View Departments Figure</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 p-md-3">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-row mb-2">
                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Measurement*</label>
                                    <select name="view_measurement" id="view_measurement" class="form-control form-control-sm"
                                        required style="pointer-events: none">
                                        <option value="">Select Measurement</option>
                                        @foreach($functionalmeasurements as $functionalmeasurement)
                                        <option value="{{$functionalmeasurement->id}}">{{$functionalmeasurement->measurement}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                <thead>
                                    <tr>
                                        <th>DEPARTMENT</th>
                                        <th>FIGURE</th>
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

        $("#functional").addClass('navbtnactive');
        $('#functional_menu_link').addClass('active');

        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": false, 
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'KPI Allocation Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'KPI Allocation Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'KPI Allocation Details',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            ajax: {
                "url": "{!! route('kpiallocationlist') !!}",

            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'year',
                    name: 'year'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'kpi',
                    name: 'kpi'
                },
                {
                    data: 'parameter',
                    name: 'parameter'
                },
                {
                    data: 'weightage',
                    name: 'weightage'
                },
                {
                    data: 'measurement',
                    name: 'measurement'
                },
                {
                    data: 'figure',
                    name: 'figure'
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
            "order": [
                [0, "desc"]
            ]
        });

        $('#create_record').click(function () {
        $('.modal-title').text('Add New KPI Allocation');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();  
        $('#tableorder tbody').empty();
        $('#formModal').modal('show');
    });

        $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();  // Trigger native validation
            } else {
                var measurement = $('#measurement').val();
                var figure = parseFloat($('#figure').val());

                if (isNaN(figure) || figure <= 0) {
                    alert('Please enter a valid figure greater than 0.');
                    return;
                }

                var existingRow = $("#tableorder tbody tr").filter(function () {
                    return $(this).find("td:first").text() === measurement;
                });

                if (existingRow.length > 0) {
                    alert('This measurement has already been added.');
                    return;
                }

                var departmentFigures = [];
                $('#dept_tableorderlist tr').each(function () {
                    var departmentId = $(this).find('.department_id').text();
                    var departmentName = $(this).find('.department_name').text();
                    var weightage = parseFloat($(this).find('.weightage').text());

                    if (!isNaN(weightage)) {
                        var departmentFigure = (weightage / 100) * figure;
                        departmentFigures.push({
                            department_id: departmentId,
                            department_name: departmentName,
                            department_figure: departmentFigure
                        });
                    }
                });

                $('#tableorder > tbody:last').append('<tr class="pointer"><td>' + measurement +
                    '</td><td> ' + figure + ' </td><td class="text-right"><button type="button"  class="btn btn-primary btn-sm mr-2 btnView"><i class="fas fa-eye"></i></button><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#hidden_department_figures').val(JSON.stringify(departmentFigures));
                $('#measurement').val('');
                $('#figure').val('');
            }
        });





    $('#btncreateorder').click(function () {
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('kpiallocationinsert') }}";
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

            var year = $('#year').val();
            var hidden_id = $('#hidden_id').val();
            var hidden_department_figures = $('#hidden_department_figures').val();

            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    year: year,
                    hidden_department_figures: hidden_department_figures,
                    hidden_id: hidden_id,
                },
                url: action_url,
                success: function (data) {
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Create');

                        if (data.errors) {
                            if (Array.isArray(data.errors)) {
                                var errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                                data.errors.forEach(function(error) {
                                    errorHtml += '<li>' + error + '</li>';
                                });
                                errorHtml += '</ul></div>';
                                $('#form_result').html(errorHtml);
                            } else {
                                $('#form_result').html('<div class="alert alert-danger">' + data.errors + '</div>');
                            }

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
                        } else if (data.success) {
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
                            $('#tableorderlist').empty();
                            $('#tableorder tbody').empty();
                            $('#form_result').html('');
                            $('#formModal').modal('hide');
                            actionreload(actionJSON);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Create');

                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);

                        var errorMessage = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#form_result').html('<div class="alert alert-danger">' + errorMessage + '</div>');

                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: errorMessage,
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        action(actionJSON);
                    }
                });
            }
        });

        // edit function
        $(document).on('click', '.edit', async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
            var id = $(this).attr('id');

            $('#form_result').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("kpiallocationedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#year').val(data.result.year);
                    $('#type').val(data.result.type_id);
                    getkpi(data.result.type_id,data.result.kpi_id)
                    getparameter(data.result.kpi_id,data.result.parameter_id)
                    getmeasurement(data.result.parameter_id,data.result.measurement_id)
                    $('#figure').val(data.result.figure);

                    $('#edithidden_id').val(id);
                    $('.modal-title').text('Edit KPI Allocation');
                    $('#action_button').html('Edit');
                    $('#EditformModal').modal('show');
                },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }
        });

        
        $('#action_button').click(function ()  {
            var id = $('#edithidden_id').val();
            var year = $('#year').val();
            var type = $('#edittype').val();
            var kpi = $('#editkpi').val();
            var parameter = $('#editparameter').val();
            var measurement = $('#editmeasurement').val();
            var figure = $('#figure').val();
            

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("kpiallocationupdate") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    hidden_id: id,
                    year: year,
                    type: type,
                    kpi: kpi,
                    parameter: parameter,
                    measurement: measurement,
                    figure: figure 
                    
                },
                success: function (data) { //alert(data);
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = '<div class="alert alert-success">' + data.success +
                                '</div>';
                            $('#formTitle1')[0].reset();
                            //$('#titletable').DataTable().ajax.reload();
                            window.location.reload(); // Use window.location.reload()
                        }

                        $('#form_result1').html(html);
                        // resetfield();

                    }
            })
        });

        // delete function
        var user_id;
        $(document).on('click', '.delete', async function () {
            var r = await Otherconfirmation("You want to remove this ? ");
            if (r == true) {
                user_id = $(this).attr('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{!! route("kpiallocationdelete") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: { id: user_id },
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
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }
        });

    });

    // insert department details model
    $(document).on('click', '.btnView', function () {
            var departmentFigures=$('#hidden_department_figures').val();
            var parsedFigures = JSON.parse(departmentFigures);
            parsedFigures.forEach(function(data){
                $('#viewInserListTableorder > tbody:last').append('<tr class="pointer"><td>' + data.department_name +
                '</td><td> ' + data.department_figure+ ' </td></tr>'
                );

            });

            $('#viewInserListModal').modal('show');
        });

        // view Department Figure
        $(document).on('click', '.view', function () {
                id = $(this).attr('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $.ajax({
                    url: '{!! route("kpiallocationview") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#view_measurement').val(data.result.mainData.measurement_id).trigger('change'); 
                        $('#view_tableorderlist').html(data.result.requestdata);
                        $('#viewconfirmModal').modal('show');

                    }
                })
        });

    // KPI filter insert part
    $('#type').change(function () {
    var type = $(this).val();
    if (type !== '') {
        $.ajax({
            url: '{!! route("kpiallocationgetkpifilter", ["type_id" => "type_id"]) !!}'
                .replace('type_id', type),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#kpi').empty().append('<option value="">Select KPI</option>');
                $.each(data, function (index, kpi) {
                    $('#kpi').append('<option value="' + kpi.id + '">' + kpi.kpi + '</option>');
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#kpi').html('<option>Error loading KPIs</option>'); // Show error message
            }
        });
    } else {
        $('#kpi').empty().append('<option value="">Select KPI</option>');
    }
});

    // Parameter filter insert part
    $('#kpi').change(function () {
            var kpi = $(this).val();
            if (kpi !== '') {
                $.ajax({
                    url: '{!! route("kpiallocationgetparameterfilter", ["kpi_Id" => "kpi_id"]) !!}'
                        .replace('kpi_id', kpi),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#parameter').empty().append(
                            '<option value="">Select Parameter</option>');
                        $.each(data, function (index, parameter) {
                            $('#parameter').append('<option value="' + parameter.id + '">' + parameter.parameter + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#parameter').empty().append('<option value="">Select Parameter</option>');
            }
        });

    // Measurement filter insert part
    $('#parameter').change(function () {
            var parameter = $(this).val();
            if (parameter !== '') {
                $.ajax({
                    url: '{!! route("kpiallocationgetmeasurementfilter", ["parameter_Id" => "parameter_id"]) !!}'
                        .replace('parameter_id', parameter),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#measurement').empty().append(
                            '<option value="">Select measurement</option>');
                        $.each(data, function (index, measurement) {
                            $('#measurement').append('<option value="' + measurement.id + '">' + measurement.measurement + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#measurement').empty().append('<option value="">Select measurement</option>');
            }
        });

        
        // When the measurement is changed
        $('#measurement').change(function () {
        var measurement_id = $(this).val();

        if (measurement_id) {
            $.ajax({
                url: '{!! route("kpiallocationgetdepartmentfilter", ["measurement_id" => ":measurement_id"]) !!}'.replace(':measurement_id', measurement_id),
                type: 'GET',
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    if (data.result) {
                        $('#dept_tableorderlist').html(data.result);  // Update the table with department rows
                    } else {
                        $('#dept_tableorderlist').html('<tr><td colspan="2">No departments found</td></tr>');  // No data message
                    }
                },
                error: function () {
                    $('#dept_tableorderlist').html('<tr><td colspan="2">Error loading departments</td></tr>');  // Error message
                }
            });
        } else {
            $('#dept_tableorderlist').html('');  // Clear the table when no measurement is selected
        }
    });

     

    // KPI filter edit part
    function getkpi(type,kpi_id){
            if (type !== '') {
                $.ajax({
                    url: '{!! route("kpiallocationgetkpifilter", ["type_id" => "type_id"]) !!}'
                        .replace('type_id', type),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#kpi').empty().append(
                            '<option value="">Select KPI</option>');
                        $.each(data, function (index, kpi) {
                            $('#kpi').append('<option value="' + kpi
                                .id + '">' + kpi.kpi + '</option>');
                        });
                        $('#kpi').val(kpi_id);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#kpi').empty().append('<option value="">Select KPI</option>');
            }
        };

    // Parameter filter edit part
    function getparameter(kpi,parameter_id){
            if (kpi !== '') {
                $.ajax({
                    url: '{!! route("kpiallocationgetparameterfilter", ["kpi_id" => "kpi_id"]) !!}'
                        .replace('kpi_id', kpi),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#parameter').empty().append(
                            '<option value="">Select Parameter</option>');
                        $.each(data, function (index, parameter) {
                            $('#parameter').append('<option value="' + parameter
                                .id + '">' + parameter.parameter + '</option>');
                        });
                        $('#parameter').val(parameter_id);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#parameter').empty().append('<option value="">Select Parameter</option>');
            }
        };

    // Measurement filter edit part
    function getmeasurement(parameter,measurement_id){
            if (parameter !== '') {
                $.ajax({
                    url: '{!! route("kpiallocationgetmeasurementfilter", ["parameter_id" => "parameter_id"]) !!}'
                        .replace('parameter_id', parameter),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#measurement').empty().append(
                            '<option value="">Select measurement</option>');
                        $.each(data, function (index, measurement) {
                            $('#measurement').append('<option value="' + measurement
                                .id + '">' + measurement.measurement + '</option>');
                        });
                        $('#measurement').val(measurement_id);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#measurement').empty().append('<option value="">Select measurement</option>');
            }
        };

        // Department filter edit part
        function getDepartment() {
        var measurement_id = $('#measurement').val();

        if (measurement_id) {
            $.ajax({
                url: '{!! route("kpiallocationgetdepartmentfilter", ["measurement_id" => ":measurement_id"]) !!}'.replace(':measurement_id', measurement_id),
                type: 'GET',
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}'  
                },
                success: function (data) {
                    if (data.result) {
                        $('#dept_tableorderlist').html(data.result);   
                    } else {
                        $('#dept_tableorderlist').html('<tr><td colspan="2">No departments found</td></tr>');   
                    }
                },
                error: function () {
                    $('#dept_tableorderlist').html('<tr><td colspan="2">Error loading departments</td></tr>');   
                }
            });
        } else {
            $('#dept_tableorderlist').html('');   
        }
    }
</script>

@endsection