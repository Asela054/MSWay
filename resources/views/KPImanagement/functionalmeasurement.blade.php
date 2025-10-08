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
                            id="create_record"><i class="fas fa-plus mr-2"></i>Add Measurement</button>
                    
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
                                        <th>KRA</th>
                                        <th>KPI</th>
                                        <th>Parameter</th>
                                        <th>Measurement</th>
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
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Measurement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
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
                                        <label class="small font-weight-bold text-dark">Measurement*</label>
                                        <input type="text" id="measurement" name="measurement" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Department*</label>
                                        <select name="name" id="name" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select Department</option>
                                            @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Department Weightage*</label>
                                        <input type="number" id="departmentweightage" name="departmentweightage" class="form-control form-control-sm"
                                            required>
                                    </div>
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
                                <input type="hidden" name="oprderdetailsid" id="oprderdetailsid">

                            </form>
                        </div>
                        <div class="col-8">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Department Weightage</th>
                                        <th>Action</th>
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

    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="aviewmodal-title" id="staticBackdropLabel">View Mesurement Departments</h5>
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
                                        <th>Weightage</th>
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
                "url": "{!! route('functionalmeasurementlist') !!}",

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
                    data: 'measurement',
                    name: 'measurement'
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
            $('.modal-title').text('Add New Functional Measurement');
            $('#action').val('Add');
            $('#form_result').html('');
            $('#formTitle')[0].reset();

            $('#formModal').modal('show');
        });

        $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#submitBtn").click();
            } else {
                var name = $('#name').val();
                var departmentweightage = $('#departmentweightage').val();

                $('#tableorder > tbody:last').append('<tr class="pointer"><td> '+ name +' </td><td> '+ departmentweightage +' </td><td><button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#name').val('');
                $('#departmentweightage').val('');
            }
        });


        $('#btncreateorder').click(function () {

            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('functionalmeasurementinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                // action_url = "{{ route('functionalmeasurementupdate') }}";
            }

            var totalWeightage = 0;
            $("#tableorder tbody tr").each(function () {
                var departmentweightage = parseFloat($(this).find('td').eq(1).text());
                totalWeightage += departmentweightage;
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
                var name = $('#name').val();
                var departmentweightage = $('#departmentweightage').val();
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        type: type,
                        kpi: kpi,
                        parameter: parameter,
                        measurement: measurement,
                        name : name,
                        departmentweightage : departmentweightage,
                        hidden_id: hidden_id,

                    },
                    url: action_url,
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
                            $('#formTitle')[0].reset();
                            //$('#titletable').DataTable().ajax.reload();
                            window.location.reload(); // Use window.location.reload()
                        }

                        $('#form_result').html(html);
                        // resetfield();

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
                url: '{!! route("functionalmeasurementedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#type').val(data.result.type_id);
                    getkpi(data.result.type_id,data.result.kpi_id)
                    getparameter(data.result.kpi_id,data.result.parameter_id)
                    $('#measurement').val(data.result.measurement);
                    $('#name').val(data.result.name);
                    $('#departmentweightage').val(data.result.departmentweightage);

                    $('#edithidden_id').val(id);
                    $('.modal-title').text('Edit Functional Measurement');
                    $('#action_button').html('Edit');
                    $('#EditformModal').modal('show');
                }
            })
        });
    // update 
        $('#action_button').click(function ()  {
            var id = $('#edithidden_id').val();
            var type = $('#edittype').val();
            var parameter = $('#editparameter').val();
            var measurement = $('#editmeasurement').val();
            var kpi = $('#editkpi').val();
            var name = $('#editname').val();
            var departmentweightage = $('#editdepartmentweightage').val();
            

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("functionalmeasurementupdate") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    hidden_id: id,
                    type: type,
                    parameter: parameter,
                    measurement: measurement, 
                    name: name,
                    departmentweightage: departmentweightage,
                    kpi: kpi
                    
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
                url: '{!! route("functionalmeasurementdelete") !!}',
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

    // view Department
    $(document).on('click', '.view', function () {
            id = $(this).attr('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("functionalmeasurementview") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#view_measurement').val(data.result.mainData.id).trigger('change'); 
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
            url: '{!! route("functionalmeasurementgetkpifilter", ["type_id" => "type_id"]) !!}'
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
                    url: '{!! route("functionalmeasurementgetparameterfilter", ["kpi_Id" => "kpi_id"]) !!}'
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

    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
</script>

@endsection