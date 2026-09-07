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
                    <span>Employee Shifts & Job Categories</span>
                </h1>
            </div>
        </div>
    </div>
      <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right mr-2" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div><br><br>
                    <div class="col-sm-12 col-md-12">
                        <div class="d-flex flex-wrap justify-content-end mb-2">
                            <div class="col-sm-12 col-md-auto mb-1 px-1">
                                <button type="button" class="btn btn-primary btn-sm px-2 w-100" name="create_record_dept_wise" id="create_record_dept_wise">
                                    <i class="fas fa-plus mr-2"></i>Department Wise
                                </button>
                            </div>
                        </div>
                    </div>
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
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>DEPARTMENT</th>
                                    <th>SHIFT</th>
                                    <th>START TIME</th>                                                
                                    <th>END TIME</th>   
                                    <th>JOB CATEGORY</th>   
                                    <th class="text-right">ACTION</th>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Shift & Job Category</h5>
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
                                            <option value="{{$shifttypes->id}}">{{ $shifttypes->shift_name }} - {{ $shifttypes->shift_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br />
                                <div class="form-row">
                                    <label class="control-label col-md-4">Job Category</label>
                                    <div class="col-md-8">
                                        <select name="job_category" id="job_category" class="form-control form-control-sm">
                                            <option value="">Please Select</option>
                                            @foreach($jobcategories as $jobcategory)
                                            <option value="{{$jobcategory->id}}">{{$jobcategory->category}}</option>
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

    <div class="modal fade" id="formModal_dpt" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Assign Shift & Job Category - Department Wise</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <span id="form_result"></span>
                                <form method="post" id="formTitle_dpt" class="form-horizontal">
                                    {{ csrf_field() }}
                                    
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                                <label class="small font-weight-bold text-dark">Company <span class="text-danger">*</span> </label>
                                                <select name="company" id="company_dept_wise" class="form-control form-control-sm">
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-3">
                                                <label class="small font-weight-bold text-dark">Department <span class="text-danger">*</span> </label>
                                                <select name="department" id="department_dept_wise" class="form-control form-control-sm" required>
                                                </select>
                                            </div>
                                        <div class="col-sm-12 col-md-3">
                                            <label class="small font-weight-bold text-dark">&nbsp; </label> <br>
                                            <button type="button" name="search_button" id="search_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-search"></i>&nbsp;Search</button>
                                        </div>
                                        
                                    </div>
                                    <br>
                                    <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display" id="dpt_allocationtbl" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>EMP ID</th>
                                                <th>NAME</th>
                                                <th>SHIFT</th>
                                                <th>JOB CATEGORY</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dptemplistbody">
                                        </tbody>
                                    </table>
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="button" name="dptaction_button" id="dptaction_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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
    </div>
    <!-- Modal Area End -->

    <!-- Search Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                    <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <form class="form-horizontal" id="formFilter">
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Company</label>
                                <select name="company" id="company_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Location</label>
                                <select name="location" id="location_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </li>
                    </form>
                </ul>
            </div>
        </div>
    </div>

</main>
              
@endsection


@section('script')

<script>
$(document).ready(function () {

    $('#shift_menu_link').addClass('active');
    $('#shift_menu_link_icon').addClass('active');
    $('#shift_link').addClass('navbtnactive');

    // department wise salary advance modal
    let company_d = $('#company_dept_wise');
    let department_d = $('#department_dept_wise');

    company_d.select2({
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

    department_d.select2({
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
                    company: company_d.val()
                }
            },
            cache: true
        }
    });

    let company_f = $('#company_f');
    let department_f = $('#department_f');
    let employee_f = $('#employee_f');
    let location_f = $('#location_f');

    company_f.select2({
        placeholder: 'Select a Company',
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
        placeholder: 'Select a Department',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("department_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                    location: location_f.val()
                }
            },
            cache: true
        }
    });

    employee_f.select2({
        placeholder: 'Select a Employee',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("employee_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                    location: location_f.val(),
                    department: department_f.val()
                }
            },
            cache: true
        }
    });

    location_f.select2({
        placeholder: 'Select Location',
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{url("location_list_sel2")}}',
            dataType: 'json',
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    company: company_f.val(),
                }
            },
            cache: true
        }
    });

    function load_dt(company, department, employee, location){
   
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
                title: 'Shift  Information',
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
            [0, "asc"]
        ],
        ajax: {
             url: scripturl + "/shift_list.php",
            type: "POST",
            data: {
                company: company,
                department: department,
                employee: employee,
                location: location
            },
        },
            columns: [
            { 
                data: 'emp_id', 
                name: 'emp_id'
            },
            { 
                data: 'employee_display', 
                name: 'employee_display'
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
                data: 'category', 
                name: 'category'
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
    'data-job_category_id="'+(row.job_category_id || '')+'" ' +
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

        closeOffcanvasSmoothly('#offcanvasRight'); 
    }

    load_dt('', '', '', '');

    //Department-Wise Shift & Job Category Assign 

    $('#create_record_dept_wise').click(function () {
        $('#dptemplistbody').empty();
        $('#formModal_dpt').modal('show');
    });

    $('#search_button').click(function () {
        var department = $('#department_dept_wise').val();
        var company    = $('#company_dept_wise').val();

        if (!company || !department) {
            Swal.fire({ icon: 'warning', title: 'Please select Company and Department', timer: 2000, showConfirmButton: false });
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-1"></i> Searching...');

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: '{!! route("shift_dpt_allocation_list") !!}',
            data: {
                _token:     '{{ csrf_token() }}',
                company:    company,
                department: department,
            },
            success: function (data) {
                if (data.error) {
                    Swal.fire({ icon: 'error', title: data.error, timer: 2500, showConfirmButton: false });
                    return;
                }
                $('#dptemplistbody').html(data.html);
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Failed to load employees.', timer: 2500, showConfirmButton: false });
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-search"></i>&nbsp;Search');
            }
        });
    });

    $('#dptaction_button').click(function () {
        var $btn = $(this);
        var tbody = $('#dptemplistbody');

        if (tbody.children().length === 0 || tbody.find('td[colspan]').length > 0) {
            Swal.fire({ position: 'top-end', icon: 'warning', title: 'Table is empty. Please search first.', showConfirmButton: false, timer: 2500 });
            return;
        }

        var jsonObj = [];
        tbody.find('tr').each(function () {
            var $row         = $(this);
            var emp_id       = $row.data('emp-id');
            var shift_id     = $row.find('.shift-select').val();
            var job_cat_id   = $row.find('.job-cat-select').val();
            jsonObj.push({ emp_id: emp_id, shift_id: shift_id, job_category_id: job_cat_id });
        });

        if (jsonObj.length === 0) {
            Swal.fire({ position: 'top-end', icon: 'warning', title: 'No rows to save.', showConfirmButton: false, timer: 2500 });
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing');

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: '{!! route("shift_dpt_allocation_update") !!}',
            data: {
                _token:    '{{ csrf_token() }}',
                tableData: jsonObj,
            },
            success: function (data) {
                if (data.errors) {
                    const actionObj = { icon: 'fas fa-warning', title: '', message: data.errors, url: '', target: '_blank', type: 'danger' };
                    action(JSON.stringify(actionObj, null, 2));
                    $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
                    return;
                }
                if (data.success) {
                    const actionObj = { icon: 'fas fa-save', title: '', message: data.success, url: '', target: '_blank', type: 'success' };
                    actionreload(JSON.stringify(actionObj, null, 2));
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server error. Please try again.' });
                $btn.prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
            }
        });
    });

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let company = $('#company_f').val();
        let department = $('#department_f').val();
        let employee = $('#employee_f').val();
        let location = $('#location_f').val();

        load_dt(company, department, employee, location);
    });

    $('#btn-reset').off('click').on('click', function() {
        $('#formFilter')[0].reset();
        $('#company_f').val(null).trigger('change');
        $('#department_f').val(null).trigger('change');
        $('#employee_f').val(null).trigger('change');
        $('#location_f').val(null).trigger('change');
        load_dt('', '', '', '');
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
        var id          = $(this).data('id');
        var empname     = $(this).data('emp_name_with_initial');
        var shift       = $(this).data('shift_type_id');
        var job_cat     = $(this).data('job_category_id');

        $('#formModal').modal('show');
        $('#uid').val(id);
        $('#uname').val(empname);
        $('#shift').val(shift).trigger('change');
        $('#job_category').val(job_cat || '').trigger('change');
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