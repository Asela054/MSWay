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
                    <span>Measurement Weightage</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
               
                        <button type="button" class="btn btn-primary btn-sm float-right" name="create_record"
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
                                        <th>KRA</th>
                                        <th>KPI</th>
                                        <th>PARAMETER</th>
                                        <th>PARAMETER WEIGHTAGE</th>
                                        <th>MEASUREMENT</th>        
                                        <th>MEASUREMENT WEIGHTAGE</th>
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
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Measurement Weightage</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-3 mb-lg-0">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
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
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">KPI*</label>
                                        <select name="kpi" id="kpi" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select KPI</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Parameter*</label>
                                        <select name="parameter" id="parameter" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select Parameter</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Measurement Weightage*</label>
                                        <select name="measurement" id="measurement" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select Measurement Weightage</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Measurement Weightage Weightage*</label>
                                        <input type="number" id="measurement_weightage" name="measurement_weightage" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group mt-3 mb-0">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-4 float-right"><i
                                            class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                        class="btn btn-primary btn-sm px-4 float-right mr-2" style="display:none;"><i
                                            class="fas fa-plus"></i>&nbsp;Update List</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="oprderdetailsid" id="oprderdetailsid">

                            </form>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                    <thead>
                                        <tr>
                                            <th>MEASUREMENT</th>
                                            <th>WEIGHTAGE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableorderlist"></tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2 mb-0">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-primary btn-sm float-right px-4"><i
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
    function productDelete(button) {
        $(button).closest('tr').remove();
    }

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
                    title: 'Measurement Weightage Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Measurement Weightage Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Measurement Weightage Details',
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
                "url": "{!! route('functionalmeasurementweightagelist') !!}",

            },
            columns: [{
                    data: 'id',
                    name: 'id'
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
                    data: 'measurement_weightage',
                    name: 'measurement_weightage'
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
            $('.modal-title').text('Add New Functional Measurement Weightage');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();
            $('#tableorder tbody').empty();
            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();
            } else {
                var measurement = $('#measurement option:selected').text(); 
                var measurement_weightage = $('#measurement_weightage').val();

                $('#tableorder > tbody:last').append('<tr class="pointer"><td>' + measurement +
                    '</td><td> '+ measurement_weightage +' </td><td><button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#measurement').val('');
                $('#measurement_weightage').val('');
            }
        });


        $('#btncreateorder').click(function () {

            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('functionalmeasurementweightageinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                // action_url = "{{ route('functionalmeasurementweightageupdate') }}";
            }

            var totalWeightage = 0;
            $("#tableorder tbody tr").each(function () {
                var measurement_weightage = parseFloat($(this).find('td').eq(1).text());
                totalWeightage += measurement_weightage;
            });

            if (totalWeightage !== 100) {
                $('#form_result').html('<div class="alert alert-danger">Total weightage must equal 100. Current total is ' + totalWeightage + '.</div>');
                return false;
            }

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating');

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

                var type = $('#type').val();
                var kpi = $('#kpi').val();
                var parameter = $('#parameter').val();
                var measurement = $('#measurement').val();
                var mesurement_weightage = $('#measurement_weightage').val();
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type,
                    kpi: kpi,
                    parameter: parameter,
                    // measurement: measurement,  
                    // mesurement_weightage: mesurement_weightage,  
                    tabledata: JSON.stringify(jsonObj), 
                    hidden_id: hidden_id,
                    action: $('#action').val()
                },
                dataType: "json",
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
                url: '{!! route("functionalmeasurementweightageedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#type').val(data.result.type_id);
                    getkpi(data.result.type_id,data.result.kpi_id)
                    getparameter(data.result.kpi_id,data.result.parameter_id)
                    getmeasurement(data.result.parameter_id,data.result.measurement_id)
                    $('#measurement_weightage').val(data.result.measurement_weightage);

                    $('#edithidden_id').val(id);
                    $('.modal-title').text('Edit Functional Measurement Weightage Weightage');
                    $('#action_button').html('Edit');
                    $('#EditformModal').modal('show');
                },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }
        });
    
        $('#action_button').click(function () {
            var id = $('#hidden_id').val(); 
            var type = $('#type').val(); 
            var kpi = $('#kpi').val(); 
            var parameter = $('#parameter').val(); 
            var measurement = $('#measurement').val(); 
            var measurement_weightage = $('#measurement_weightage').val();
            

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("functionalmeasurementweightageupdate") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    hidden_id: id,
                    type: type,
                    kpi: kpi,
                    parameter: parameter,
                    measurement: measurement, 
                    measurement_weightage: measurement_weightage
                    
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
                    url: '{!! route("functionalmeasurementweightagedelete") !!}',
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

    // KPI filter insert part
    $('#type').change(function () {
    var type = $(this).val();
    if (type !== '') {
        $.ajax({
            url: '{!! route("functionalmeasurementweightagegetkpifilter", ["type_id" => "type_id"]) !!}'
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
                    url: '{!! route("functionalmeasurementweightagegetparameterfilter", ["kpi_Id" => "kpi_id"]) !!}'
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

    // Measurement Weightage filter insert part
    $('#parameter').change(function () {
            var parameter = $(this).val();
            if (parameter !== '') {
                $.ajax({
                    url: '{!! route("functionalmeasurementweightagegetmeasurementfilter", ["parameter_Id" => "parameter_id"]) !!}'
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
         

    // KPI filter edit part
    function getkpi(type,kpi_id){
            if (type !== '') {
                $.ajax({
                    url: '{!! route("functionalmeasurementgetkpifilter", ["type_id" => "type_id"]) !!}'
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
                    url: '{!! route("functionalmeasurementgetparameterfilter", ["kpi_id" => "kpi_id"]) !!}'
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

    // Measurement Weightage filter edit part
    function getmeasurement(parameter,measurement_id){
            if (parameter !== '') {
                $.ajax({
                    url: '{!! route("functionalmeasurementweightagegetmeasurementfilter", ["parameter_id" => "parameter_id"]) !!}'
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
</script>

@endsection