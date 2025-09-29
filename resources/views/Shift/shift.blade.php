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
                    <div class="page-header-icon"><i class="fa-light fa-business-time"></i></div>
                    <span>Employee Shifts </span>
                </h1>
            </div>
        </div>
    </div>
      <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Location</label>
                            <select name="location" id="location_f" class="form-control form-control-sm">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm">
                            </select>
                        </div>

                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn " id="btn-filter"> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <span id="response"></span>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                         <table class="table table-striped table-bordered table-sm small nowrap w-100" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Employee Name </th>
                                    <th>Department</th>
                                    <th>Shift </th>
                                    <th>Start Time</th>                                                
                                    <th>End Time</th>   
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form id="formTitle" method="post">
                                {{ csrf_field() }}

                                <div class="form-row pb-2">
                                    <label class="control-label col-md-1">Id: </label>
                                    <div class="col-md-2">
                                        <input type="text" name="uid" id="uid" class="form-control form-control-sm" readonly />
                                    </div>
                                    <label class="control-label col-md-2">Name: </label>
                                    <div class="col-md-7">
                                        <input type="text" name="uname" id="uname" class="form-control form-control-sm" readonly />
                                    </div>

                                </div>
                                <div class="form-row">
                                    <label class="control-label col-md-4">Shift</label>

                                    <div class="col-md-8">
                                        <select name="shift" id="shift" class="form-control form-control-sm">
                                            <option value="">Please Select</option>
                                            @foreach($shifttype as $shifttypes)
                                            <option value="{{$shifttypes->id}}">{{$shifttypes->shift_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <br />
                                <div class="form-group float-right" >
                                    <input type="hidden" name="action" id="action" value="Edit" />
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <input type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm"
                                        value="Edit" />
                                </div>
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
    <!-- Modal Area End -->

</main>
              
@endsection


@section('script')

<script>

$(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#shift_link').addClass('navbtnactive');

    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("company_list_sel2")}}',
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

    department_f.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val()
                }
            },
            cache: true
        }
    });

    employee_f.select2({
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

    location_f.select2({
        placeholder: 'Select...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("location_list_sel2")}}',
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

    function load_dt(department, employee, location, from_date, to_date){
        $('#dataTable').DataTable({
          "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Customer  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Location Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Customer  Information',
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
             url: scripturl + "/shift_list.php",
            type: "POST",
            data: {},
        },
            columns: [
            { 
                data: 'emp_name_with_initial', 
                name: 'emp_name_with_initial'
            },
            { 
                data: 'departmentname', 
                name: 'departmentname'
            },
            { 
                data: 'shift_name', 
                name: 'shift_name'
            },
            { 
                data: 'onduty_time', 
                name: 'onduty_time'
            },
            { 
                data: 'offduty_time', 
                name: 'offduty_time'
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var is_resigned = row.is_resigned;
                    var buttons = '';

                   
                   buttons += '<button name="edit" id="'+row.emp_id+'" ' +
    'data-id="'+row.emp_id+'" ' +
    'data-emp_name_with_initial="'+row.emp_name_with_initial+'" ' +
    'data-shift_name="'+row.shift_name+'" ' +
    'data-onduty_time="'+row.onduty_time+'" ' +
    'data-offduty_time="'+row.offduty_time+'" ' +
    'data-shift_type_id="'+row.shift_type_id+'" ' +
    'class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit">' +
    '<i class="fas fa-pencil-alt"></i></button>';

                    buttons += '<button type="submit" name="delete" id="'+row.emp_id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                    return buttons;
                }
            }
        ],
          drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
        });
    }

    load_dt('', '', '', '', '');

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department_f').val();
        let employee = $('#employee_f').val();
        let location = $('#location_f').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
    });

});

$(document).ready(function () {
    $('#create_record').click(function () {
        $('.modal-title').text('Apply Leave');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';
        action_url = "{{ route('Shift.update') }}";

        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
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
    });



    $(document).on('click', '.edit', function () {
        var id = $(this).data('id');
        var empname = $(this).data('emp_name_with_initial');
        var shift = $(this).data('shift_type_id');

        $('#formModal').modal('show');
        $('#uid').val(id);
        $('#uname').val(empname);
    });

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).data('id');
        $('#confirmModal').modal('show');

    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "Shift/destroy/" + user_id,
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
    });

});
</script>

@endsection