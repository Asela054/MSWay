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
                        <span>Behavioural Weightage</span>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>ATTRIBUTE</th>
                                        <th>WEIGHTAGE</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Add Behavioural Weightage</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-5 col-12 mb-3 mb-md-0">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Employee*</label>
                                        <select name="emp" id="emp" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Attribute*</label>
                                        <select name="type" id="type" class="form-control form-control-sm"
                                            required>
                                            <option value="">Select Attribute</option>
                                            @foreach($behaviouraltypes as $behaviouraltype)
                                            <option value="{{$behaviouraltype->id}}">{{$behaviouraltype->type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Weightage*</label>
                                        <input type="number" id="weightage" name="weightage" class="form-control form-control-sm"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group mt-3 mb-0">
                                    <button type="button" id="formsubmit"
                                        class="btn btn-primary btn-sm px-3 px-md-4 float-right"><i
                                            class="fas fa-plus"></i>&nbsp;<span class="d-none d-sm-inline">Add to list</span><span class="d-inline d-sm-none">Add</span></button>
                                    <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                                    <button type="button" name="Btnupdatelist" id="Btnupdatelist"
                                        class="btn btn-primary btn-sm px-3 px-md-4 float-right mr-2" style="display:none;"><i
                                            class="fas fa-plus"></i>&nbsp;<span class="d-none d-sm-inline">Update List</span><span class="d-inline d-sm-none">Update</span></button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                                <input type="hidden" name="oprderdetailsid" id="oprderdetailsid">
                            </form>
                        </div>
                        <div class="col-lg-8 col-md-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="tableorder">
                                    <thead>
                                        <tr>
                                            <th>ATTRIBUTE</th>
                                            <th>WEIGHTAGE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableorderlist"></tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2 mb-0">
                                <button type="button" name="btncreateorder" id="btncreateorder"
                                    class="btn btn-primary btn-sm float-right px-3 px-md-4"><i
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

        $("#behavioural").addClass('navbtnactive');
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
                    title: 'Behavioural Details',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Behavioural Details', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Behavioural Details',
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
                "url": "{!! route('behaviouralweightagelist') !!}",

            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'employee_display',
                    name: 'employee_display'
                },
                {
                    data: 'type',
                    name: 'type'
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
            $('.modal-title').text('Add New Behavioural Weightage');
            $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
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
                var type = $('#type').val();
                var weightage = $('#weightage').val();

                $('#tableorder > tbody:last').append('<tr class="pointer"><td>' + type +
                    '</td><td>' + weightage + '</td><td class="d-none">NewData</td><td><button type="button" onclick= "productDelete(this);" id="btnDeleterow" class=" btn btn-danger btn-sm "><i class="fas fa-trash-alt"></i></button></td></tr>'
                );

                $('#type').val('');
                $('#weightage').val('') ;
            }
        });


        $('#btncreateorder').click(function () {

    var action_url = '';

    if ($('#action').val() == 'Add') {
        action_url = "{{ route('behaviouralweightageinsert') }}";
    }
    if ($('#action').val() == 'Edit') {
        // action_url = "{{ route('behaviouralweightageupdate') }}";
    }

    var totalWeightage = 0;
    $("#tableorder tbody tr").each(function () {
        var weightage = parseFloat($(this).find('td').eq(1).text());
        totalWeightage += weightage;
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

                var emp = $('#emp').val();
                var hidden_id = $('#hidden_id').val();

                $.ajax({
                    method: "POST",
                    dataType: "json",
                    data: {
                        _token: '{{ csrf_token() }}',
                        tableData: jsonObj,
                        emp: emp,
                        hidden_id: hidden_id,

                    },
                    url: action_url,
                    success: function (data) {
                        if (data.errors) {
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
                        }
                        if (data.success) {
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
                            $('#formModal').modal('hide'); 
                            actionreload(actionJSON);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: 'Something went wrong!',
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
                url: '{!! route("behaviouralweightageedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#editemp').val(data.result.mainData.emp_id).trigger('change'); 
                    $('#tableorderlist').html(data.result.requestdata);

                    $('#edithidden_id').val(id);
                    $('.modal-title').text('Edit Functional KPI');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                    $('#EditformModal').modal('show');
                },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                })
            }
        });

        // update
        $('#action_button').click(function ()  {
            var id = $('#edithidden_id').val();
            var emp = $('#editemp').val();
            var type = $('#edittype').val();
            var weightage = $('#editweightage').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("behaviouralweightageupdate") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    hidden_id: id,
                    emp: emp,
                    type: type,
                    weightage: weightage
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
                var user_id = $(this).attr('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{!! route("behaviouralweightagedelete") !!}',
                    type: 'POST',
                    dataType: "json",
                    data: { id: user_id },
                    success: function (data) {
                        if (data.success) {
                            const actionObj = {
                                icon: 'fas fa-trash-alt',
                                title: '',
                                message: data.success,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            actionreload(actionJSON);
                        } else if (data.error) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: data.error,
                                url: '',
                                target: '_blank',
                                type: 'danger'
                            };
                            const actionJSON = JSON.stringify(actionObj, null, 2);
                            action(actionJSON);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        const actionObj = {
                            icon: 'fas fa-warning',
                            title: '',
                            message: 'Failed to delete record',
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
    });
</script>

@endsection