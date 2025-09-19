@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.corporate_nav_bar')
           
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                    @can('SalaryAdjustment-create')
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Salary Adjustment</button>
                    @endcan
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

                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Select Employee</label>
                                            <select name="employee" id="employee" class="form-control form-control-sm" >
                                                <option value="0">Select Employee</option>
                                                @foreach ($employees as $employee){
                                                    <option value="{{$employee->emp_id}}" >{{$employee->emp_name_with_initial}}</option>
                                                }  
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Job Category</label>
                                        <select id="job_category" name="job_category" class="form-control form-control-sm" required>
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
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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

    $('#employee').select2({ width: '100%' });

    $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('SalaryAdjustmentlist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'employee', name: 'employee'}, 
                    { data: 'category', name: 'category' },
                    { data: 'remuneration_name', name: 'remuneration_name' },
                    { 
                        data: 'allowance_type', 
                        name: 'allowance_type',
                        render: function (data, type, row) {
                            return data == 1 ? 'Daily' : 'Monthly';
                        }
                    },
                    { data: 'amount', name: 'amount' },
                    { data: 'allowleave', name: 'allowleave' },
                    {
                        data: 'approved_status',
                        name: 'approved_status',
                        render: function (data, type, row) {
                            if (data === 0) {
                                return '<span style="color: red;">Pending</span>';
                            } else {
                                return '<span style="color: green;">Approved</span>';
                            }
                        }
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

    $('#create_record').click(function(){
        $('.modal-title').text('Add Salary Adjustment');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#formTitle')[0].reset();
        $('#form_result').html('');

        $('#employee').prop('disabled', false).parent().show();
        $('#job_category').prop('disabled', false);
        $('#remuneration_name').prop('disabled', false);
        $('#allowance_type_0, #allowance_type_1').prop('disabled', false);
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
                    //$('#titletable').DataTable().ajax.reload();
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');

        $('#employee').prop('disabled', false).parent().show();
        $('#job_category').prop('disabled', false);
        $('#remuneration_name').prop('disabled', false);
        $('#allowance_type_0, #allowance_type_1').prop('disabled', false);
        $('#amount').prop('readonly', false);
        $('#allowleave').prop('readonly', false);

        $.ajax({
            url: "SalaryAdjustment/" + id + "/edit",
            dataType: "json",
            success: function (data) {

                $('#employee').val(data.result.emp_id);
                $('#job_category').val(data.result.job_id);
                $('#remuneration_name').val(data.result.remuneration_id);

                if(data.result.allowance_type == '1'){
                    $('#allowance_type_0').prop( "checked", true );
                    // $('.custom_work').css('display', 'none');
                }else if(data.result.allowance_type == '2') {
                    $('#allowance_type_1').prop( "checked", true );
                    // $('.custom_work').css('display', 'block');
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
    });

    $(document).on('click', '.approve', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "SalaryAdjustment/" + id + "/edit",
            dataType: "json",
            success: function (data) {

                $('#employee').val(data.result.emp_id).prop('disabled', true).parent().hide();
                $('#job_category').val(data.result.job_id).prop('disabled', true);
                $('#remuneration_name').val(data.result.remuneration_id).prop('disabled', true);

                if (data.result.allowance_type == '1') {
                    $('#allowance_type_0').prop("checked", true).prop('disabled', true);
                    $('#allowance_type_1').prop('disabled', true);
                } else if (data.result.allowance_type == '2') {
                    $('#allowance_type_1').prop("checked", true).prop('disabled', true);
                    $('#allowance_type_0').prop('disabled', true);
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

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "SalaryAdjustment/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {//alert(data);
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload()
            }
        })
    });



    
});
</script>


@endsection

                                

