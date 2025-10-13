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
                    <hr class="border-dark">
                </div>
                <div class="col-12">
                    <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                            id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>YEAR</th>
                                    <th>KRA</th>
                                    <th>Employee</th>
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
<div class="container-fluid mt-2 p-0 p-2">
    <div class="card">
        <div class="card-body p-0 p-2">
            <div class="row">
                <div class="col-12">
                    <hr class="border-dark">
                </div>
                <div class="col-12">
                    <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                            id="empdataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>YEAR</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
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
<br>

<!-- Modal Area Start -->
<div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Employee Allocation</h5>
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

                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Kpi Year*</label>
                                    <select name="view_year" id="view_year" class="form-control form-control-sm"
                                        required style="pointer-events: none">
                                        <option value="">Select Year</option>
                                        @foreach($kpiyears as $kpiyear)
                                        <option value="{{$kpiyear->id}}">{{$kpiyear->year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Functional KRA*</label>
                                    <select name="view_type" id="view_type" class="form-control form-control-sm"
                                        required style="pointer-events: none">
                                        <option value="">Select KRA</option>
                                        @foreach($functionaltypes as $functionaltype)
                                        <option value="{{$functionaltype->id}}">{{$functionaltype->type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                             
                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Employee*</label>
                                    <select name="view_kpi" id="view_kpi" class="form-control form-control-sm"
                                    required style="pointer-events: none">
                                    @foreach($functionalkpis as $functionalkpi)
                                    <option value="{{$functionalkpi->id}}">{{$functionalkpi->kpi}}</option>
                                    @endforeach
                                </select>
                                </div>
                             
                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Parameter*</label>
                                    <select name="view_parameter" id="view_parameter" class="form-control form-control-sm"
                                    required style="pointer-events: none">
                                    @foreach($functionalparameters as $functionalparameter)
                                    <option value="{{$functionalparameter->id}}">{{$functionalparameter->parameter}}</option>
                                    @endforeach
                                </select>
                                </div>
                             
                                <div class="col-12">
                                <label class="small font-weight-bold text-dark">Measurement*</label>
                                    <select name="view_measurement" id="view_measurement" class="form-control form-control-sm" 
                                    required style="pointer-events: none">
                                    @foreach($functionalmeasurements as $functionalmeasurement)
                                    <option value="{{$functionalmeasurement->id}}">{{$functionalmeasurement->measurement}}</option>
                                    @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Department*</label>
                                    <select name="department" id="department" class="form-control form-control-sm" onchange="getFigure()" required>
                                        @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Department Figure*</label>
                                    <input type="number" id="view_departmentfigure" name="view_departmentfigure" class="form-control form-control-sm" required readonly>
                                </div>

                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Employee*</label>
                                    <select name="emp" id="emp" class="form-control form-control-sm">
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="small font-weight-bold text-dark">Employee Figure*</label>
                                    <input type="number" id="empfigure" name="empfigure" class="form-control form-control-sm" required>
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
                    <div class="col-12 col-lg-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>EMPLOYEE</th>
                                        <th>FIGURE</th>
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

<div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
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
                                <div class="table-responsive">
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
    $(document).ready(function () {

        $("#functional").addClass('navbtnactive');
        $('#functional_menu_link').addClass('active');

        let emp = $('#emp');

        emp.select2({
            placeholder: 'Select a Employee',
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
            "serverSide": false, 
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Allocation Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Allocation Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Allocation Details',
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
                "url": "{!! route('empallocationlist') !!}",

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

        $('.add').click(function () {
        $('.modal-title').text('Add New Employee Allocation');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();  
        $('#formModal').modal('show');
    });

    $("#formsubmit").click(function () {
            if (!$("#formTitle")[0].checkValidity()) {
                $("#submitBtn").click();  // Trigger native validation
            } else {
                var emp = $('#emp').val();
                var empfigure = parseFloat($('#empfigure').val());

                if (isNaN(empfigure) || empfigure <= 0) {
                    alert('Please enter a valid figure greater than 0.');
                    return;
                }

                var existingRow = $("#tableorder tbody tr").filter(function () {
                    return $(this).find("td:first").text() === emp;
                });

                if (existingRow.length > 0) {
                    alert('This Employee has already been added.');
                    return;
                }

                $('#tableorder > tbody:last').append('<tr class="pointer"><td>' + emp +
                    '</td><td> ' + empfigure + ' </td><td class="text-right"><button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#emp').val('');
                $('#empfigure').val('');
            }
        });

        $('#btncreateorder').click(function () {
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('empallocationinsert') }}";
        }

        var totalFigure = 0;
        var figure = parseFloat($('#view_departmentfigure').val());
            $("#tableorder tbody tr").each(function () {
                var empfigure = parseFloat($(this).find('td').eq(1).text());
                totalFigure += empfigure;
            });

            if (totalFigure !== figure) {
                $('#form_result').html('<div class="alert alert-danger">Total figure must equal to department figure. Current total is ' + totalFigure + '.</div>');
                return false;
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

            var year = $('#view_year').val();
            var measurement = $('#view_measurement').val();
            var department = $('#department').val();
            var hidden_id = $('#hidden_id').val();

            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    year: year,
                    measurement: measurement,
                    department: department,
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
                    url: '{!! route("empallocationdelete") !!}',
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

    // view Department Figure
    $(document).on('click', '.view', function () {
                id = $(this).attr('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })

                $.ajax({
                    url: '{!! route("empallocationview") !!}',
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


    // add employee allocation
    $(document).on('click', '.add', function () {
    var id = $(this).attr('id');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '{!! route("empallocationadd") !!}', // Route for fetching allocation data
        type: 'POST',
        dataType: "json",
        data: { id: id },
        success: function (data) {
            // Populate form fields
            $('#view_year').val(data.result.mainData.year_id).trigger('change');
            $('#view_type').val(data.result.mainData.type_id).trigger('change');
            $('#view_kpi').val(data.result.mainData.kpi_id).trigger('change');
            $('#view_parameter').val(data.result.mainData.parameter_id).trigger('change');
            $('#view_measurement').val(data.result.mainData.measurement_id).trigger('change');

            // Populate department dropdown and set figure
            $('#department').empty().append(
                '<option value="">Select Department</option>');
            $.each(data.result.departments, function (index, department) {
                $('#department').append(`<option value="${department.department_id}">${department.department}</option>`);
            });

            // Show the modal with pre-filled data
            $('#formModal').modal('show');
        }
    });
});


    
        // Function to handle department change and update figure
        function getFigure() {
            var department_id = $('#department').val(); // Get selected department ID
            var measurement_id = $('#view_measurement').val(); // Get selected measurement ID

            if (department_id && measurement_id) {
                $.ajax({
                    url: '{!! route("empallocationgetfigurefilter") !!}', // Route to fetch department figure
                    type: 'GET',
                    dataType: "json",
                    data: {
                        department_id: department_id,
                        measurement_id: measurement_id,
                        _token: '{{ csrf_token() }}'  // CSRF token for security
                    },
                    success: function (data) {
                        if (data.result) {
                            $('#view_departmentfigure').val(data.result); // Update the department figure input
                        } else {
                            $('#view_departmentfigure').val(''); // Clear input if no figure found
                        }
                    },
                    error: function () {
                        $('#view_departmentfigure').val('Error loading department figure'); // Handle error
                    }
                });
            } else {
                $('#view_departmentfigure').val(''); // Clear input if no department or measurement selected
            }
        }

    
    //Employee table
    $('#empdataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": false, 
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Details',
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
                "url": "{!! route('empallocationlist2') !!}",

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
                    data: 'employee_display',
                    name: 'employee_display'
                },
                {
                    data: 'department',
                    name: 'department'
                },
                {
                    data: 'measurement',
                    name: 'measurement'
                },
                {
                    data: 'empfigure',
                    name: 'empfigure'
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

</script>

@endsection