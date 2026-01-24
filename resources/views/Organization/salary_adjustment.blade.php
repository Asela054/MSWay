@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.corporate_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-building"></i></div>
                    <span>Salary Adjustments</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Salary Adjustment</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap display text-uppercase" style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Adjustment Type</th> 
                                                <th>Employee</th>
                                                <th>Job Category</th>
                                                <th>Addition/Deduction Type</th>
                                                <th>Allowance type</th>
                                                <th>Amount</th>
                                                <th>Allow Leaves</th>
                                                <th>Approved Status</th>
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
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Salary Adjustment</h5>
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

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Adjustment Type <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input adjustment_type" name="adjustment_type" id="adjustment_type_1" value="1" required>Employee Wise
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input adjustment_type" name="adjustment_type" id="adjustment_type_2" value="2" required>Job Category Wise
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-6 employee_div" style="display: none;">
                                        <label class="small font-weight-bold text-dark">Select Employee</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm">
                                            </select>
                                    </div>
                                    <div class="col-md-6 job_category_div" style="display: none;">
                                        <label class="small font-weight-bold text-dark">Job Category</label>
                                        <select id="job_category" name="job_category" class="form-control form-control-sm">
                                        <option value="">Select Job Category</option>
                                        @foreach ($job_categories as $job_category){
                                            <option value="{{$job_category->id}}" >{{$job_category->category}}</option>
                                        }  
                                        @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Addition/Deduction Type</label>
                                        <select id="remuneration_name" name="remuneration_name" class="form-control form-control-sm" required>
                                        <option value="">Select Remuneration</option>
                                        @foreach ($remunerations as $remuneration){
                                            <option value="{{$remuneration->id}}" >{{$remuneration->remuneration_name}}</option>
                                        }  
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Allowance Type</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input allowance_type" name="allowance_type" id="allowance_type_0" value="1" checked>Daily
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input allowance_type" name="allowance_type" id="allowance_type_1" value="2">Monthly
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input allowance_type" name="allowance_type" id="allowance_type_2" value="3">Custom
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Amount</label>
                                        <input type="number" name="amount" step="0.01" id="amount" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Allow Leaves</label>
                                        <input type="number" name="allowleave" step="0.01" id="allowleave" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="form-group mt-2">
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
$(document).ready(function(){

    $('#organization_menu_link').addClass('active');
    $('#organization_menu_link_icon').addClass('active');
    $('#salary_adjustmentlink').addClass('navbtnactive');

    let employee = $('#employee');

    employee.select2({
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
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Salary Adjustment  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                title: 'Salary Adjustment Information',
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape',
                pageSize: 'legal',
                customize: function (doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Salary Adjustment  Information',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-print mr-2"></i> Print',
                customize: function (win) {
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
            url: scripturl + "/salaryadjustmentlist.php",
            type: "POST",
            data: {},
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'adjustment_type', 
                name: 'adjustment_type',
                render: function (data, type, row) {
                    return data == 1 ? 'Employee Wise' : 'Job Category Wise';
                }
            },
            {
                data: 'employee_display',
                name: 'employee_display'
            },
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'remuneration_name',
                name: 'remuneration_name'
            },
            {
                data: 'allowance_type',
                name: 'allowance_type',
                render: function (data, type, row) {
                    return data == 1 ? 'Daily' : (data == 2 ? 'Monthly' : 'Custom');
                }
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'allowleave',
                name: 'allowleave'
            },
            {
                data: 'approved_status',
                name: 'approved_status',
                render: function (data, type, row) {
                    if(data === 0 || data === '0') {
                        return '<span style="color: red;">Pending</span>';
                    } else {
                        return '<span style="color: green;">Approved</span>';
                    }
                }
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var is_resigned = row.is_resigned;
                    var buttons = '';

                    if(row.approved_status == 0){
                        buttons += ' <button name="edit" id="' + row.id + '" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }

                    if(row.approved_status == 0){
                        buttons += ' <button name="approve" id="' + row.id + '" class="approve btn btn-success btn-sm mr-1"><i class="fas fa-level-up-alt"></i></button>';
                    }

                        buttons += '<button type="submit" name="delete" id="' + row.id + '" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                    return buttons;
                }
            }
        ],
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    $('#create_record').click(function(){
        $('.modal-title').text('Add Salary Adjustment');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#formTitle')[0].reset();
        $('#form_result').html('');

        $('.employee_div').css('display', 'none');
        $('.job_category_div').css('display', 'none');
        $('#adjustment_type_1').prop('checked', false);
        $('#adjustment_type_2').prop('checked', false);

        $('#employee').prop('disabled', false);
        $('#job_category').prop('disabled', false);
        $('#remuneration_name').prop('disabled', false);
        $('#allowance_type_0, #allowance_type_1, #allowance_type_2').prop('disabled', false);
        $('#amount').prop('readonly', false);
        $('#allowleave').prop('readonly', false);

        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addSalaryAdjustment') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('SalaryAdjustment.update') }}";
        }
        if ($('#action').val() == 'Approve') {
            action_url = "{{ route('SalaryAdjustment.approve_update') }}";
        }

        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);        
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

    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');

            $('#employee').prop('disabled', false).parent().show();
            $('#job_category').prop('disabled', false);
            $('#remuneration_name').prop('disabled', false);
            $('#allowance_type_0, #allowance_type_1, #allowance_type_2').prop('disabled', false);
            $('#amount').prop('readonly', false);
            $('#allowleave').prop('readonly', false);

            $.ajax({
                url: "SalaryAdjustment/" + id + "/edit",
                dataType: "json",
                success: function (data) {

                    if (data.result.adjustment_type == 1) {
                        $('#adjustment_type_1').prop("checked", true);
                        $('.employee_div').css('display', 'block');
                        $('.job_category_div').css('display', 'none');
                        $('#job_category').val(0);
                        
                        if(data.result.emp_id && data.result.emp_id != 0) {
                            var option = new Option(data.result.emp_name_with_initial, data.result.emp_id, true, true);
                            $('#employee').append(option).trigger('change');
                        }
                        
                    } else if (data.result.adjustment_type == 2) {
                        $('#adjustment_type_2').prop("checked", true);
                        $('.job_category_div').css('display', 'block');
                        $('.employee_div').css('display', 'none');
                        $('#employee').val(0);
                        $('#job_category').val(data.result.job_id);
                    }

                    $('#remuneration_name').val(data.result.remuneration_id);

                    if (data.result.allowance_type == '1') {
                        $('#allowance_type_0').prop("checked", true);
                    } else if (data.result.allowance_type == '2') {
                        $('#allowance_type_1').prop("checked", true);
                    } else if (data.result.allowance_type == '3') {
                        $('#allowance_type_2').prop("checked", true);
                    }

                    $('#amount').val(data.result.amount);
                    $('#allowleave').val(data.result.allowleave);

                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Salary Adjustment');
                    $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
            })
        }
    });

    $(document).on('click', '.approve', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "SalaryAdjustment/" + id + "/edit",
            dataType: "json",
            success: function (data) {

                if (data.result.adjustment_type == 1) {
                    $('#adjustment_type_1').prop("checked", true);
                    $('.employee_div').css('display', 'block');
                    $('.job_category_div').css('display', 'none');
                    
                    if(data.result.emp_id && data.result.emp_id != 0) {
                        var option = new Option(data.result.emp_name_with_initial, data.result.emp_id, true, true);
                        $('#employee').append(option).trigger('change');
                    }
                    $('#employee').prop('disabled', true).parent().show();
                    
                } else if (data.result.adjustment_type == 2) {
                    $('#adjustment_type_2').prop("checked", true);
                    $('.job_category_div').css('display', 'block');
                    $('.employee_div').css('display', 'none');
                    $('#job_category').val(data.result.job_id);
                }

                $('#job_category').prop('disabled', true);
                $('#remuneration_name').val(data.result.remuneration_id).prop('disabled', true);

                if (data.result.allowance_type == '1') {
                    $('#allowance_type_0').prop("checked", true).prop('disabled', true);
                    $('#allowance_type_1').prop('disabled', true);
                    $('#allowance_type_2').prop('disabled', true);
                } else if (data.result.allowance_type == '2') {
                    $('#allowance_type_1').prop("checked", true).prop('disabled', true);
                    $('#allowance_type_0').prop('disabled', true);
                    $('#allowance_type_2').prop('disabled', true);
                } else if (data.result.allowance_type == '3') {
                    $('#allowance_type_2').prop("checked", true).prop('disabled', true);
                    $('#allowance_type_0').prop('disabled', true);
                    $('#allowance_type_1').prop('disabled', true);
                }

                $('#amount').val(data.result.amount).prop('readonly', true);
                $('#allowleave').val(data.result.allowleave).prop('readonly', true);

                $('#hidden_id').val(id);
                $('.modal-title').text('Approve Salary Adjustment');
                $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Approve');
                $('#action').val('Approve');
                $('#formModal').modal('show');
            }
        })
    });

    var user_id;

    $(document).on('click', '.delete', async function() {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "SalaryAdjustment/destroy/" + user_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {//alert(data);
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

    $(document).on('change', '.adjustment_type', function (e) {
        let val = $(this).val();
        if(val == 1){
            $('.employee_div').css('display', 'block');
            $('.job_category_div').css('display', 'none');
            $('#job_category').val(0); 
            $('#employee').val('').prop('required', true);
            $('#job_category').prop('required', false);
        }else if(val == 2){
            $('.job_category_div').css('display', 'block');
            $('.employee_div').css('display', 'none');
            $('#employee').val(0); 
            $('#job_category').val('').prop('required', true);
            $('#employee').prop('required', false);
        }
    });

    
});
</script>


@endsection

                                

