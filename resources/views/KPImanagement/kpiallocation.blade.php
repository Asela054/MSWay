@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            @include('layouts.functional_nav_bar')
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
               
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record"
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add KPI Allocation</button>
                    
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
                                        <th>Year</th>
                                        <th>KRA</th>
                                        <th>KPI</th>
                                        <th>Parameter</th>
                                        <th>Parameter Weightage</th>
                                        <th>Measurement</th>        
                                        <th>Figure</th>
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
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Kpi Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">

                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Kpi Year*</label>
                                        <select name="year" id="year" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select Year</option>
                                            @foreach($kpiyears as $kpiyear)
                                            <option value="{{$kpiyear->id}}">{{$kpiyear->year}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Functional KRA*</label>
                                        <select name="type" id="type" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select KRA</option>
                                            @foreach($functionaltypes as $functionaltype)
                                            <option value="{{$functionaltype->id}}">{{$functionaltype->type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                 
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">KPI*</label>
                                        <select name="kpi" id="kpi" class="form-control form-control-sm" required>
                                        <option value="">Select KPI</option>
                                    </select>
                                    </div>
                                 
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Parameter*</label>
                                        <select name="parameter" id="parameter" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select Parameter</option>
                                    </select>
                                    </div>
                                 
                                    <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Measurement*</label>
                                        <select name="measurement" id="measurement" class="form-control form-control-sm" onchange="getDepartment()" required>
                                             <option value="">Select Measurement</option>
                                        </select>
                                    </div>
                                 
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Figure*</label>
                                        <input type="number" id="figure" name="figure" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>

                                <div class="col-12">
                                <table class="table table-striped table-bordered table-sm small" id="dept_tableorder">
                                <thead>
                                        <tr>
                                        <th>Department</th>
                                        <th>Weightage</th>
                                        </tr>
                                </thead>
                                <tbody id="dept_tableorderlist"></tbody>
                                </table>
                                </div>
                                
                                <div class="form-group mt-3">
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
                        <div class="col-9">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Measurement</th>
                                        <th>Figure</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableorderlist"></tbody>
                            </table>
                            <div class="form-group mt-2">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                        class="fas fa-plus"></i>&nbsp;Create</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewInserListModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                            <table class="table table-striped table-bordered table-sm small" id="viewInserListTableorder">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Figure</th>
                                        </tr>
                                    </thead>
                                    <tbody id="viewInserListTableorderlist"></tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="aviewmodal-title" id="staticBackdropLabel">View Departments Figure</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-row mb-1">
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
                    <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small" id="view_tableorder">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Figure</th>
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
            processing: true,
            serverSide: true,
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
            "bDestroy": true,
            "order": [
                [0, "desc"]
            ]
        });

        $('#create_record').click(function () {
        $('.modal-title').text('Add New KPI Allocation');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();  
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
                console.log(data); // Log the response
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
                window.location.reload();
            }

                $('#form_result').html(html);
            }

            });
        }
    });

        // edit function
        $(document).on('click', '.edit', function () {
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
                }
            })
        });

        
    // update 
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
                url: '{!! route("kpiallocationdelete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
                },
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) { //alert(data);
                    setTimeout(function () {
                        $('#confirmModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        // alert('Data Deleted');
                    }, 2000);
                    location.reload()
                }
            })
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
    
    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    function approve_confirm() {
        return confirm("Are you sure you want to approve this?");
    }
</script>

@endsection