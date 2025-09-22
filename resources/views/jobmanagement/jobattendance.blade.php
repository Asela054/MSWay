
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
                        <span>Location Attendance</span>
                    </h1>
                </div>
            </div>
        </div>


        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body  p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-end mb-2">
                                    <button type="button" class="btn btn-primary btn-sm px-3 mr-2 mb-2" name="create_record" id="create_record">
                                        <i class="fas fa-plus mr-2 d-none d-sm-inline"></i>
                                        <span class="d-none d-sm-inline">Attendance of a Location</span>
                                        <i class="fas fa-map-marker-alt d-sm-none"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm px-3 mr-2 mb-2" name="create_record_employee" id="create_record_employee">
                                        <i class="fas fa-plus mr-2 d-none d-sm-inline"></i>
                                        <span class="d-none d-sm-inline">Attendance of a Employee</span>
                                        <i class="fas fa-user d-sm-none"></i>
                                    </button>
                                </div>
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
                                                <th>DATE</th>
                                                <th>LOCATION NAME</th>
                                                <th>ON TIME</th>
                                                <th>OFF TIME</th>
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
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Attendance for Allocated Employees</h5>
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
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Location</label>
                                        <select name="location" id="location"
                                            class="form-control form-control-sm " style="width: 100%;" required>
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" class="form-control form-control-sm"
                                            name="attendancedate" id="attendancedate">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <button style="margin-top:30px;" type="button" name="searchbtn" id="searchbtn"
                                            class="btn btn-primary btn-sm "><i class="fas fa-search"></i>&nbsp;Search</button>
                                    </div>
                                </div>
                                <br>
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display"
                                        id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th style="white-space: nowrap;">Empolyee Name</th>
                                                <th>On Time</th>
                                                <th>Off Time</th>
                                                <th style="white-space: nowrap;">Action</th>
                                                <th class="d-none">allocation id</th>
                                            </tr>
                                        </thead>
                                        <tbody id="emplistbody">
                                        </tbody>
                                    </table>
                                </div>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Location Attendance</h5>
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
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Employees</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm" style="width:100%" disabled>
                                                <option value="">Select Employees</option>
                                        @foreach($employees as $employee)
                                        <option value="{{$employee->emp_id}}">
                                            {{$employee->emp_name_with_initial}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" class="form-control form-control-sm"
                                            name="attendancedateedit" id="attendancedateedit" disabled>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Location</label>
                                        <select name="locationedit" id="locationedit"
                                            class="form-control form-control-sm " style="width: 100%;" required>
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">On Time</label>
                                        <input type="datetime-local" id="empontime" name="empontime" class="form-control form-control-sm"   required>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Off Time</label>
                                        <input type="datetime-local" id="empofftime" name="empofftime" class="form-control form-control-sm"  required>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_buttonedit" id="action_buttonedit" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Update</button>
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

    {{-- Single Employee Attendance Model --}}

    <div class="modal fade" id="formModal_single_attendace" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Attendance for a Employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_resultsingle"></span>
                            <form method="post" id="formTitlesingle" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="row">
                                      <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Employee</label>
                                        <select name="employee_single" id="employee_single"
                                            class="form-control form-control-sm " style="width: 100%;" required>
                                            <option value="">Select Location</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Location</label>
                                        <select name="locationsingle" id="locationsingle"
                                            class="form-control form-control-sm " style="width: 100%;" required>
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->location}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <label class="small font-weight-bold text-dark">Date</label>
                                        <input type="date" class="form-control form-control-sm" name="singleattendancedate" id="singleattendancedate">
                                    </div>

                                    <div class="col-sm-12 col-md-8">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6">
                                                <label class="small font-weight-bold text-dark">On Time</label>
                                                <input type="datetime-local" id="singleempontime" name="singleempontime" class="form-control form-control-sm"
                                                    required>
                                            </div>

                                            <div class="col-sm-12 col-md-6">
                                                <label class="small font-weight-bold text-dark">Off Time</label>
                                                <input type="datetime-local" id="singleempofftime" name="singleempofftime" class="form-control form-control-sm"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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


@endsection

@section('script')

    <script>
        $(document).ready(function () {

            @can('Job-Attendance-edit')
                canEditattendace = true;
            @endcan
            @can('Job-Attendance-delete')
                candeleteattendace = true;
            @endcan

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#jobmanegment').addClass('navbtnactive');

            $(".employee").select2();
            $('#location').select2();


             $('#employee_single').select2({
                placeholder: 'Select...',
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


            $('#create_record').click(function(){
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });

            $('#create_record_employee').click(function(){
                $('#formModal_single_attendace').modal('show');
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
                            title: 'Location Attendance  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Location Attendance  Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Location Attendance   Information',
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
                     url: scripturl + '/location_attendance_list.php',
                     type: 'POST',
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'employee_display', name: 'employee_display' },
                    { data: 'attendance_date', name: 'attendance_date' },
                    { data: 'location', name: 'location' },
                    { data: 'on_time', name: 'on_time' },
                    { data: 'off_time', name: 'off_time' },
                    {
                        data: 'id',
                        name: 'action',
                        className: 'text-right',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var is_resigned = row.is_resigned;
                            var buttons = '';

                            if (canEditattendace) {
                                buttons += '<button name="edit" id="' + row.id +'" class="edit btn btn-primary btn-sm" type="submit"  data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>&nbsp;';
                            }
                            if (candeleteattendace) {
                                buttons += '<button name="delete" id="' + row.id +'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                            }
                            return buttons;
                        }
                    },
                ],
                "bDestroy": true,
                "order": [
                    [0, "desc"]
                ]
            });

            $('#searchbtn').click(function () {
                   var attlocation = $('#location').val();
                    var attendancedate = $('#attendancedate').val();
                    
                    $.ajax({
                        method: "POST",
                        dataType: "json",
                        data: {
                            _token: '{{ csrf_token() }}',
                            attlocation: attlocation,
                            attendancedate: attendancedate,
                        },
                        url: '{!! route("attendancegetemplist") !!}',
                        success: function (data) {
                           
                            var tblemployee = data.result;
                       $("#allocationtbl").prepend(tblemployee);
                        }
                    });


            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();

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
                    var shift = $('#shift').val();
                    var attendancedate = $('#attendancedate').val();

                    $.ajax({
                    url: '{!! route("jobattendancesave") !!}',
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            tableData: jsonObj,
                            allocation: allocation,
                            shift: shift,
                            attendancedate: attendancedate
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
                            $('#formTitle')[0].reset();
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
                    url: '{!! route("jobattendanceedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#employee').val(data.result.employee_id);
                        $('#attendancedateedit').val(data.result.attendance_date);
                        $('#empontime').val(data.result.on_time);
                        $('#empofftime').val(data.result.off_time);
                        $('#locationedit').val(data.result.location_id);
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

                var action_url = "{{ route('jobattendanceupdate') }}";

                    var attendancedateedit = $('#attendancedateedit').val();
                    var editemployee = $('#employee').val();
                    var empontime = $('#empontime').val();
                    var empofftime = $('#empofftime').val();
                    var locationedit = $('#locationedit').val();
                    var hidden_id = $('#hidden_id').val();

                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            editemployee: editemployee,
                            attendancedateedit: attendancedateedit,
                            empontime: empontime,
                            empofftime: empofftime,
                            locationedit: locationedit,
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
                             $('#formTitleedit')[0].reset();
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
                    newRow.find(".employee").val('');
                    $("#allocationtbl tbody").find(".deletebtn").prop('disabled', false);
                    $(this).closest("tr").find(".addRowBtn").remove();
                    $("#allocationtbl tbody tr:last").after(newRow);
                    newRow.find(".employee").select2();
                    $(".employee").last().next().next().remove();
            });

            // singleattendace
              $('#formTitlesingle').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('single_employeeattendance') }}";

                    var locationsingle = $('#locationsingle').val();
                    var employee_single = $('#employee_single').val();
                    var singleattendancedate = $('#singleattendancedate').val();
                    var singleempontime = $('#singleempontime').val();
                    var singleempofftime = $('#singleempofftime').val();

                    $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            employee_single: employee_single,
                            locationsingle: locationsingle,
                            singleattendancedate: singleattendancedate,
                            singleempontime: singleempontime,
                            singleempofftime: singleempofftime,
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
                             $('#formTitlesingle')[0].reset();
                            actionreload(actionJSON);
                        }
                    }
                });
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
                        url: '{!! route("jobattendancedelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {
                            id: user_id
                        },
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
          
              // select all record 
        $('#selectAll').click(function (e) {
            $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

        });

    function productDelete(ctl) {
    	$(ctl).parents("tr").remove();
    }
    </script>


@endsection

