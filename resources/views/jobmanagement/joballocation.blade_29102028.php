
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @can('Job-Allocation-create')
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Job Locaton</button>
                            @endcan
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                {{-- <div class="custom-control custom-checkbox ml-2 mb-2">
                                    <input type="checkbox" class="custom-control-input checkallocate" id="selectAll">
                                    <label class="custom-control-label" for="selectAll">Select All Records</label>
                                </div> --}}
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display"
                                        style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Employee Name</th>
                                                <th>Location Name</th>
                                                <th>Shift</th>
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
    </main>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Job Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="col-4">
                                    <select name="location" id="location" class="form-control form-control-sm " style="width: 100%;" required>
                                        <option value="">Select Job Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Empolyee Name</th>
                                        <th>Shift</th>
                                        <th style="white-space: nowrap;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="emplistbody">
                                    <tr>
                                        <td style="white-space: nowrap;">
                                            <select name="employee" id="employee"
                                                class="employee form-control form-control-sm" style="width:100%">
                                                <option value="">Select Employees</option>
                                                @foreach($employees as $employee)
                                                <option value="{{$employee->id}}">
                                                    {{$employee->emp_name_with_initial}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <select name="shift" id="shift"
                                                class="form-control form-control-sm title" required>
                                                <option value="">Select Shift</option>
                                                @foreach($shifts as $shift)
                                                <option value="{{$shift->id}}">{{$shift->shift_name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <button type="button" onclick="productDelete(this);"
                                                class="deletebtn btn btn-danger btn-sm " disabled><i
                                                    class="fas fa-trash-alt"></i></button>
                                            <button class="addRowBtn btn btn-success btn-sm "><i
                                                    class="fas fa-plus"></i></button>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Job Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result2"></span>
                            <form method="post" id="formTitleedit" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-4">
                                        <select name="editlocation" id="editlocation"
                                            class="form-control form-control-sm " style="width: 100%;" readonly>
                                            <option value="">Select Job Location</option>
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location_name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select name="editemployee" id="editemployee"
                                            class="form-control form-control-sm" style="width:100%">
                                            <option value="">Select Employees</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->id}}">
                                                {{$employee->emp_name_with_initial}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select name="editshift" id="editshift"
                                            class="form-control form-control-sm title" required>
                                            <option value="">Select Shift</option>
                                            @foreach($shifts as $shift)
                                            <option value="{{$shift->id}}">{{$shift->shift_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_buttonedit" id="action_buttonedit"
                                        class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i
                                            class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="1" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
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

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#jobmanegment').addClass('navbtnactive');

            $(".employee").select2();
            $('#location').select2();
            $('#editemployee').select2();

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Job Category');
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('joballocationslist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'shift_name', name: 'shift_name' },
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

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('joballocationsave') }}";
                var tbody = $("#allocationtbl tbody");
                if (tbody.children().length > 0) {
                    var jsonObj = [];
                    $("#allocationtbl tbody tr").each(function () {
                        var item = {};
                        $(this).find('td').each(function (col_idx) {
                          
                            var inputElement = $(this).find('input, select');
                            if (inputElement.length > 0) {
                                item["col_" + (col_idx + 1)] = inputElement.val();
                            } else {
                                item["col_" + (col_idx + 1)] = $(this).text();
                            }
                        });
                        jsonObj.push(item);
                    });
                    
                    var allocation = $('#location').val();
                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            tableData: jsonObj,
                            allocation: allocation
                        },
                    dataType: "json",
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
                            location.reload()
                        }
                        $('#form_result').html(html);
                    }
                });

                }
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
               
                $('#form_result').html('');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                $.ajax({
                    url: '{!! route("joballocationedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#editlocation').val(data.result.location_id);
                        $('#editemployee').val(data.result.employee_id);
                        $('#editshift').val(data.result.shiftid);
                        $('#hidden_id').val(id);
                        $('#action_buttonedit').html('Update');
                        $('#action').val('2');
                        $('#formModal2').modal('show');
                    }
                })
            });

            $('#formTitleedit').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('joballocationupdate') }}";

                    var editlocation = $('#editlocation').val();
                    var editemployee = $('#editemployee').val();
                    var editshift = $('#editshift').val();
                    var hidden_id = $('#hidden_id').val();

                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            editemployee: editemployee,
                            editlocation: editlocation,
                            editshift: editshift,
                            hidden_id: hidden_id,
                        },
                    dataType: "json",
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
                            $('#formTitleedit')[0].reset();
                            location.reload()
                        }
                        $('#form_result2').html(html);
                    }
                });
            });

            $("#allocationtbl tbody").on("click", ".addRowBtn", function () {
                    var newRow = $("#allocationtbl tbody tr:last").clone();
                    newRow.find(".employee").each(function (index) {
                        $(this).select2('destroy');
                    });
                    newRow.find("input").val('');
                    newRow.find(".employee").val('').select2();
                    $("#allocationtbl tbody").find(".deletebtn").prop('disabled', false);
                    $(this).closest("tr").find(".addRowBtn").remove();
                    $("#allocationtbl tbody tr:last").after(newRow);
                    $(".employee").last().next().next().remove();
                  
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
                    url: '{!! route("joballocationdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload()
                    }
                })
            });

        });

          function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    function productDelete(ctl) {
    	$(ctl).parents("tr").remove();
    }
    </script>


@endsection

