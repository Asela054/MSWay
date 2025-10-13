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
                        <span>Parameter Weightage</span>
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
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID </th>
                                        <th>KRA</th>
                                        <th>KPI</th>
                                        <th>PARAMETER</th>
                                        <th>WEIGHTAGE(%)</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Weightage</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Form Section -->
                        <div class="col-12 col-md-4 mb-4 mb-md-0">
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
                                        <label class="small font-weight-bold text-dark">Weightage(%)*</label>
                                        <input type="number" id="weightage" name="weightage" class="form-control form-control-sm"
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
                        
                        <!-- Table Section -->
                        <div class="col-12 col-md-8">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                    <thead>
                                        <tr>
                                            <th>PARAMETER</th>
                                            <th>WEIGHTAGE(%)</th>
                                            <th>ACTION</th>
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
                    title: 'Parameter Weightage Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Parameter Weightage Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Parameter Weightage Details',
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
                "url": "{!! route('functionalweightagelist') !!}",
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
            $('.modal-title').text('Add New Functional Weightage');
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
                var parameter = $('#parameter option:selected').text();
                var parameterid = $('#parameter').val();
                var weightage = $('#weightage').val();

                if (!parameterid || !weightage) {
                    alert('Please select parameter and enter weightage');
                    return false;
                }

                $('#tableorder > tbody:last').append('<tr class="pointer" data-parameter-id="' + parameterid + '"><td data-parameter-id="' + parameterid + '">' + parameter +
                    '</td><td>' + weightage + '</td><td><button type="button" onclick="productDelete(this);" id="btnDeleterow" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#parameter').val('');
                $('#weightage').val('');
            }
        });

        $('#btncreateorder').click(function () {
            var action_url = '';

            if ($('#action').val() == 'Add') {
                action_url = "{{ route('functionalweightageinsert') }}";
            }
            if ($('#action').val() == 'Edit') {
                // action_url = "{{ route('functionalweightageupdate') }}";
            }

            var tbody = $("#tableorder tbody");
            if (tbody.children().length === 0) {
                $('#form_result').html('<div class="alert alert-danger">Please add at least one parameter to the list.</div>');
                return false;
            }

            var totalWeightage = 0;
            $("#tableorder tbody tr").each(function () {
                var weightage = parseFloat($(this).find('td').eq(1).text());
                if (!isNaN(weightage)) {
                    totalWeightage += weightage;
                }
            });

            if (totalWeightage !== 100) {
                $('#form_result').html('<div class="alert alert-danger">Total weightage must equal 100%. Current total is ' + totalWeightage + '%.</div>');
                return false;
            }

            $('#form_result').html('');

            $('#btncreateorder').prop('disabled', true).html(
                '<i class="fas fa-circle-notch fa-spin mr-2"></i> Creating...');

            var jsonObj = [];
            $("#tableorder tbody tr").each(function () {
                var parameterid = $(this).find('td').eq(0).data('parameter-id');
                var weightage = $(this).find('td').eq(1).text().trim();
                
                jsonObj.push({
                    "parameter": parameterid,
                    "weightage": weightage
                });
            });

            var type = $('#type').val();
            var kpi = $('#kpi').val();
            var hidden_id = $('#hidden_id').val();

            if (!type || !kpi) {
                $('#form_result').html('<div class="alert alert-danger">Please select KRA and KPI.</div>');
                $('#btncreateorder').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Create');
                return false;
            }

            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type,
                    kpi: kpi,
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
        });

        $(document).on('click', '.edit', async function () {
            var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');

                $('#form_result').html('');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{!! route("functionalweightageedit") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#edittype').val(data.result.type_id);
                        getkpi(data.result.type_id, data.result.kpi_id);
                        getparameter(data.result.kpi_id, data.result.parameter_id);
                        $('#editweightage').val(data.result.weightage);

                        $('#edithidden_id').val(id);
                        $('.modal-title').text('Edit Functional Weightage');
                        $('#action_button').html('<i class="fas fa-save"></i>&nbsp;Update');
                        $('#EditformModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }
        });

        $('#action_button').click(function () {
            var id = $('#edithidden_id').val();
            var type = $('#edittype').val();
            var parameter = $('#editparameter').val();
            var weightage = $('#editweightage').val();
            var kpi = $('#editkpi').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{!! route("functionalweightageupdate") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    hidden_id: id,
                    type: type,
                    parameter: parameter,
                    weightage: weightage,
                    kpi: kpi
                },
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
                        $('#formTitle1')[0].reset();
                        window.location.reload();
                    }
                    $('#form_result1').html(html);
                }
            });
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
                    url: '{!! route("functionalweightagedelete") !!}',
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

        $('#type').change(function () {
            var type = $(this).val();
            if (type !== '') {
                $.ajax({
                    url: '{!! route("functionalweightagegetkpifilter", ["type_id" => "type_id"]) !!}'.replace('type_id', type),
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
                        $('#kpi').html('<option>Error loading KPIs</option>');
                    }
                });
            } else {
                $('#kpi').empty().append('<option value="">Select KPI</option>');
            }
        });

        $('#kpi').change(function () {
            var kpi = $(this).val();
            if (kpi !== '') {
                $.ajax({
                    url: '{!! route("functionalweightagegetparameterfilter", ["kpi_Id" => "kpi_id"]) !!}'.replace('kpi_id', kpi),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#parameter').empty().append('<option value="">Select Parameter</option>');
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

        function getkpi(type, kpi_id) {
            if (type !== '') {
                $.ajax({
                    url: '{!! route("functionalweightagegetkpifilter", ["type_id" => "type_id"]) !!}'.replace('type_id', type),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#editkpi').empty().append('<option value="">Select KPI</option>');
                        $.each(data, function (index, kpi) {
                            $('#editkpi').append('<option value="' + kpi.id + '">' + kpi.kpi + '</option>');
                        });
                        $('#editkpi').val(kpi_id);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#editkpi').empty().append('<option value="">Select KPI</option>');
            }
        }

        function getparameter(kpi, parameter_id) {
            if (kpi !== '') {
                $.ajax({
                    url: '{!! route("functionalweightagegetparameterfilter", ["kpi_id" => "kpi_id"]) !!}'.replace('kpi_id', kpi),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#editparameter').empty().append('<option value="">Select Parameter</option>');
                        $.each(data, function (index, parameter) {
                            $('#editparameter').append('<option value="' + parameter.id + '">' + parameter.parameter + '</option>');
                        });
                        $('#editparameter').val(parameter_id);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#editparameter').empty().append('<option value="">Select Parameter</option>');
            }
        }

    }); // End of document.ready

    // This function should be outside document.ready if it's called from inline onclick
    function productDelete(btn) {
        $(btn).closest('tr').remove();
    }

</script>

@endsection