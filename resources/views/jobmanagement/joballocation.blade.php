
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                @include('layouts.attendant&leave_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                        <span>Allocation</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                                <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Allocate Employees</button>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>EMPLOYEE NAME</th>
                                                <th>LOCATION NAME</th>
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

    </main>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Allocation</h5>
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
                                <div class="col-sm-12 col-md-6">
                                    <label class="small font-weight-bolderer text-dark">Location*</label>
                                    <select name="location" id="location" class="form-control form-control-sm " style="width: 100%;" required>
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Empolyee Name</th>
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
                                                <option value="{{$employee->emp_id}}">
                                                    {{$employee->emp_name_with_initial}}</option>
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
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Allocation</h5>
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
                                    <div class="col-sm-12 col-md-6">
                                         <label class="small font-weight-bolder text-dark">Location</label>
                                        <select name="editlocation" id="editlocation"
                                            class="form-control form-control-sm " style="width: 100%;" readonly>
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div><br>
                                    <div class="col-sm-12 col-md-6">
                                         <label class="small font-weight-bolder text-dark">Employee</label>
                                        <select name="editemployee" id="editemployee"
                                            class="form-control form-control-sm" style="width:100%">
                                            <option value="">Select Employees</option>
                                            @foreach($employees as $employee)
                                            <option value="{{$employee->emp_id}}">
                                                {{$employee->emp_name_with_initial}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_buttonedit" id="action_buttonedit"
                                        class="btn btn-primary btn-sm fa-pull-right px-4"><i
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
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();
                $('#formModal').modal('show');
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
                        title: 'Job Allocation  Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Job Allocation Information', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Job Allocation  Information',
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
                    "url": "{!! route('joballocationslist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'location', name: 'location' },
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
                                 actionreload(actionJSON);
                             }
                    }
                });

                }
            });

            $(document).on('click', '.edit',async function () {
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
                    url: '{!! route("joballocationedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#editlocation').val(data.result.location_id);
                        $('#editemployee').val(data.result.employee_id);
                        $('#hidden_id').val(id);
                        $('#action_buttonedit').html('Update');
                        $('#action').val('2');
                        $('#formModal2').modal('show');
                    }
                })
                }
            });

            $('#formTitleedit').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('joballocationupdate') }}";

                    var editlocation = $('#editlocation').val();
                    var editemployee = $('#editemployee').val();
                    var hidden_id = $('#hidden_id').val();

                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            editemployee: editemployee,
                            editlocation: editlocation,
                            hidden_id: hidden_id,
                        },
                    dataType: "json",
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
                                 actionreload(actionJSON);
                             }
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

            $(document).on('click', '.delete',async function () {
                 var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                     user_id = $(this).attr('id');
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
                        }
                    })
                }
               
            });
          
           
        });
    function productDelete(ctl) {
    	$(ctl).parents("tr").remove();
    }
    </script>


@endsection

