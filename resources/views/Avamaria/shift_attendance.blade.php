@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.shift_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                         <span>Roster Change</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                           <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Change Roster Shift</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter Options</button>
                    </div><br><br>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%" id="jobtable">
                            <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
                                    <th>DATE</th>
                                    <th>SHIFT</th> 
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
         @include('layouts.filter_menu_offcanves')
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Roster Attendance</h5>
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
                                 <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Employee*</label>
                                    <select name="employee" id="employee_single" class="form-control form-control-sm" readonly>
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                                <input type="hidden" name="employee" id="employee_hidden" disabled />
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Date</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" placeholder="YYYY-MM-DD" readonly/>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Shift</label>
                                    <select name="shift" id="shift" class="form-control form-control-sm">
                                        <option value="">Shift</option>
                                        @foreach($shifts as $shift)
                                        <option value="{{$shift->id}}">{{$shift->shift_name}} - {{$shift->shift_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">In Time*</label>
                                        <input type="datetime-local" name="in_time_s" id="in_time_s" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" required/>
                                    </div>

                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bold text-dark">Out Time*</label>
                                        <input type="datetime-local" name="out_time_s" id="out_time_s" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM"/>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title">Add Roster Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="add_result"></div>
                    <form id="addForm">
                        {{ csrf_field() }}
                        <div class="form-group mb-1">
                            <label class="small font-weight-bolder text-dark">Employee*</label>
                            <select name="employee" id="employee_new" class="form-control form-control-sm" required>
                                <option value="">Select Employee</option>
                            </select>
                        </div>
                        <div class="form-group mb-1">
                            <label class="small font-weight-bolder text-dark">Date*</label>
                            <input type="date" name="date" id="date_new" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group mb-1">
                            <label class="small font-weight-bolder text-dark">Shift</label>
                            <select name="shift" id="shift_new" class="form-control form-control-sm">
                                <option value="">Shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{$shift->id}}">{{$shift->shift_name}} - {{$shift->shift_code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row mb-1">
                            <div class="col-sm-12 col-md-6">
                                <label class="small font-weight-bold text-dark">In Time*</label>
                                <input type="datetime-local" name="in_time" id="in_time_new" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label class="small font-weight-bold text-dark">Out Time*</label>
                                <input type="datetime-local" name="out_time" id="out_time_new" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" id="add_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
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
$(document).ready(function(){

        $('#shift_menu_link').addClass('active');
        $('#shift_menu_link_icon').addClass('active');
        $('#monthlyshifts_change_view').addClass('navbtnactive');

    $('#jobtable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Roster Change Details',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Roster Change Details', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Roster Change Details',
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
            url: scripturl + '/Avemaria/rosterattendace_list.php',
            type: 'POST',
        },
        columns: [
            { data: 'emp_id', name: 'emp_id' },
            { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
            { data: 'dep_name', name: 'dep_name' },
            { data: 'date', name: 'date' },
            {
                data: 'shift_name',
                name: 'shift_name',
                render: function (data, type, row) {
                    return [data, row.shift_code].filter(Boolean).join(' - ');
                }
            },
            { data: 'on_timestamp', name: 'on_timestamp' },
            { data: 'off_timestamp', name: 'off_timestamp' },
            {
            data: 'id',
            name: 'action',
            className: 'text-right',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                var buttons = '';

                    buttons += '<button name="edit"  id="'+row.id+'" class="edit btn btn-primary btn-sm"   style="margin:1px;" type="submit" data-toggle="tooltip"  title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                    // buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm"  style="margin:1px;" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                

                return buttons;
            }
        }
        ],
        "bDestroy": true,
        "order": [
            [3, "desc"]
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

     let company = $('#company');
     let department = $('#department');
     let employee = $('#employee');
     let location = $('#location');

    let employee_f = $('#employee_f');

    company.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("company_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    department.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val()
                }
            },
            cache: true
        }
    });

    employee.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company.val(),
                    department: department.val()
                }
            },
            cache: true
        }
    });

    location.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("location_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    $('#employee_new').select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        dropdownParent: $('#addModal'),
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function (params) {
                return { term: params.term || '', page: params.page || 1 };
            },
            cache: true
        }
    });

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

    $(document).on('click', '.edit', async function () {
        var id = $(this).attr('id');
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r != true) {
            return;
        }

        $('#form_result').html('');
        $.ajax({
            url: '{{ url("roster_attendance_transfer") }}/' + id + '/edit',
            dataType: 'json',
            success: function (data) {
                var row = data.result;
            var name = row.emp_name_with_initial || row.calling_name || '';
                var option = new Option(row.emp_id + ' - ' + name, row.emp_id, true, true);

                $('#employee_single').empty().append(option).trigger('change').prop('disabled', true);
                $('#employee_hidden').val(row.emp_id).prop('disabled', false);

                $('#date').val(row.date);
                $('#shift').val(row.roster_shift_id);
                $('#in_time_s').val(row.on_timestamp);
                $('#out_time_s').val(row.off_timestamp);
                $('#hidden_id').val(row.id);

                $('.modal-title').text('Edit Roster Attendance');
                $('#action_button').html('<i class="fas fa-save"></i>&nbsp;Update');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            }
        });
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();

        var button = $('#action_button');
        button.prop('disabled', true);
        $('#form_result').empty();

        $.ajax({
            url: "{{ route('rosterAttendanceTransfer.update') }}",
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.errors) {
                    var box = $('<div class="alert alert-danger small p-2"></div>');
                    $.each(data.errors, function (i, message) {
                        box.append($('<div></div>').text(message));
                    });
                    $('#form_result').html(box);
                    return;
                }

                $('#formModal').modal('hide');
                actionreload(JSON.stringify({
                    icon: 'fas fa-save',
                    title: '',
                    message: data.success,
                    url: '',
                    target: '_blank',
                    type: 'success'
                }));
            },
            error: function (xhr) {
                alert('Update karanna bari una (' + xhr.status + ')');
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });

    var fetchRequest = null;

    $('#create_record').click(function () {
        $('#addForm')[0].reset();
        $('#employee_new').val(null).trigger('change');
        $('#add_result').empty();
        $('#add_button').prop('disabled', false);
        $('#addModal').modal('show');
    });

    $('#employee_new, #date_new').on('change', function () {
        var employee = $('#employee_new').val();
        var date = $('#date_new').val();

        if (fetchRequest) {
            fetchRequest.abort();
        }
        $('#shift_new, #in_time_new, #out_time_new').val('');
        $('#add_result').empty();
        $('#add_button').prop('disabled', false);

        if (!employee || !date) {
            return;
        }

        $('#add_button').prop('disabled', true);

        fetchRequest = $.ajax({
            url: "{{ route('rosterAttendanceTransfer.fetch') }}",
            data: { employee: employee, date: date },
            dataType: 'json',
            success: function (data) {
            if (data.errors) {
                    showAddErrors(data.errors);
                    return;
                }
                $('#shift_new').val(data.result.shift_id);
                $('#in_time_new').val(data.result.on_time);
                $('#out_time_new').val(data.result.off_time);
                $('#add_button').prop('disabled', false);
            },
            error: function (xhr, status) {
                if (status !== 'abort') {
                    showAddErrors(['Data load karanna bari una (' + xhr.status + ')']);
                }
            }
        });
    });

    $('#addForm').on('submit', function (event) {
        event.preventDefault();
        $('#add_button').prop('disabled', true);

        $.ajax({
            url: "{{ route('rosterAttendanceTransfer.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.errors) {
                    showAddErrors(data.errors);
                    return;
                    }
                $('#addModal').modal('hide');
                actionreload(JSON.stringify({
                    icon: 'fas fa-save', title: '', message: data.success,
                    url: '', target: '_blank', type: 'success'
                }));
            },
        });
    });

    
});

function showAddErrors(errors) {
    const actionObj = {
        icon: 'fas fa-warning',
        title: '',
        message: [].concat(errors).join(', '),
        url: '',
        target: '_blank',
        type: 'danger'
    };
    const actionJSON = JSON.stringify(actionObj, null, 2);
    action(actionJSON);
}
</script>

@endsection