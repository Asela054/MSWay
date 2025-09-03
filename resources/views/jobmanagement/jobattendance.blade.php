
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
                            @can('Job-Attendance-create')
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Attendance</button>
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
                                                <th>Date</th>
                                                <th>Location Name</th>
                                                <th>Shift</th>
                                                <th>On Time</th>
                                                <th>Off Time</th>
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
                            <h5 class="modal-title" id="staticBackdropLabel">Add Attendance</h5>
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
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Job Location</label>
                                                <select name="location" id="location"
                                                    class="form-control form-control-sm " style="width: 100%;" required>
                                                    <option value="">Select Job Location</option>
                                                    @foreach($locations as $location)
                                                    <option value="{{$location->id}}">{{$location->location_name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Shift</label>
                                                <select name="shift" id="shift"
                                                    class="form-control form-control-sm title" required>
                                                    <option value="">Select Shift</option>
                                                    @foreach($shifts as $shift)
                                                    <option value="{{$shift->id}}">{{$shift->shift_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="attendancedate" id="attendancedate">
                                            </div>
                                            <div class="col-3">
                                                <button style="margin-top:30px;" type="button" name="searchbtn" id="searchbtn"
                                                    class="btn btn-primary btn-sm "><i class="fas fa-search"></i>&nbsp;Search</button>
                                            </div>
                                        </div>
                                       <br>

                                       <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Empolyee Name</th>
                                                <th>On Time</th>
                                                <th>Off Time</th>
                                                <th style="white-space: nowrap;">Action</th>
                                                <th class="d-none">allocation id</th>
                                            </tr>
                                        </thead>
                                        <tbody id="emplistbody">
                                            
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
                            <h5 class="modal-title" id="staticBackdropLabel">Edit Job Attendance</h5>
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
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Employees</label>
                                                   <select name="employee" id="employee" class="form-control form-control-sm" style="width:100%" disabled>
                                                      <option value="">Select Employees</option>
                                                @foreach($employees as $employee)
                                                <option value="{{$employee->id}}">
                                                    {{$employee->emp_name_with_initial}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="attendancedateedit" id="attendancedateedit" disabled>
                                            </div>
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">On Time</label>
                                                <input type="datetime-local" id="empontime" name="empontime" class="form-control form-control-sm"   required>
                                            </div>
                                            <div class="col-3">
                                                <label class="small font-weight-bold text-dark">Off Time</label>
                                                <input type="datetime-local" id="empofftime" name="empofftime" class="form-control form-control-sm"  required>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group mt-3">
                                            <button type="submit" name="action_buttonedit" id="action_buttonedit" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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

            $('#create_record').click(function(){
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
                    "url": "{!! route('jobattendancelist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'attendance_date', name: 'attendance_date' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'shift_name', name: 'shift_name' },
                    { data: 'on_time', name: 'on_time' },
                    { data: 'off_time', name: 'off_time' },
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

            $('#searchbtn').click(function () {
                   var attlocation = $('#location').val();
                    var shift = $('#shift').val();
                    var attendancedate = $('#attendancedate').val();
                    
                    $.ajax({
                        method: "POST",
                        dataType: "json",
                        data: {
                            _token: '{{ csrf_token() }}',
                            attlocation: attlocation,
                            shift: shift,
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
                    url: '{!! route("jobattendanceedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#employee').val(data.result.employee_id);
                        $('#attendancedateedit').val(data.result.attendance_date);
                        $('#empontime').val(data.result.on_time);
                        $('#empofftime').val(data.result.off_time);
                        $('#hidden_id').val(id);
                        $('#action_buttonedit').html('Update');
                        $('#action').val('2');
                        $('#formModal2').modal('show');
                    }
                })
            });

            $('#formTitleedit').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('jobattendanceupdate') }}";

                    var attendancedateedit = $('#attendancedateedit').val();
                    var editemployee = $('#employee').val();
                    var empontime = $('#empontime').val();
                    var empofftime = $('#empofftime').val();
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
                    newRow.find(".employee").val('');
                    $("#allocationtbl tbody").find(".deletebtn").prop('disabled', false);
                    $(this).closest("tr").find(".addRowBtn").remove();
                    $("#allocationtbl tbody tr:last").after(newRow);
                    newRow.find(".employee").select2();
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
                    url: '{!! route("jobattendancedelete") !!}',
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

              // select all record 
        $('#selectAll').click(function (e) {
            $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
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

