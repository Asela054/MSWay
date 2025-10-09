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
    <br>
    <br>
    <div class="container-fluid">
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
                                        <th>Year</th>
                                        <th>Employee</th>
                                        <th>Department</th>
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
    <br>

<!-- Modal Area Start -->
<div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Employee Allocation</h5>
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
                                        <label class="small font-weight-bold text-dark">KPI*</label>
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
                                        <select name="emp" id="emp" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->emp_id}}">{{$employee->emp_name_with_initial}}</option>
                                            @endforeach
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
                        <div class="col-9">
                            <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
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
            "bDestroy": true,
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
                url: '{!! route("empallocationdelete") !!}',
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



    function productDelete(row) {
        $(row).closest('tr').remove();
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    } 
    
    //Employee table
    $('#empdataTable').DataTable({
            processing: true,
            serverSide: true,
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
                    data: 'empname',
                    name: 'empname'
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
            "bDestroy": true,
            "order": [
                [0, "desc"]
            ]
        });

</script>

@endsection