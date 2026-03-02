@extends('layouts.app')

@section('content')

<main>
    <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('ProductionEmployee.production_nav_bar')
        </div>
    <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-hard-hat"></i></div>
                    <span>Production Detail</span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap text-uppercase" style="width: 100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>DEPARTMENT</th>
                                            <th>MEN'S INCENTIVE</th>
                                            <th>WOMEN'S INCENTIVE</th>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Production Detail</h5>
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
                                        <label class="small font-weight-bold text-dark">Company</label>
                                        <select name="company" id="company_f" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Department*</label>
                                        <select name="department" id="department_f" class="form-control form-control-sm" required>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Men's Incentive*</label>
                                        <input type="number" name="men_incentive" id="men_incentive" class="form-control form-control-sm" placeholder="Men's Incentive" step="0.01" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Women's Incentive*</label>
                                        <input type="number" name="women_incentive" id="women_incentive" class="form-control form-control-sm" placeholder="Women's Incentive" step="0.01" required>
                                    </div>
                                </div>
                                <div class="form-row mb-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-dark">Remarks</label>
                                        <input type="text" name="remark" id="remark" class="form-control form-control-sm" placeholder="Remarks">
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
    <!-- Modal Area End -->
</main>
@endsection

@section('script')
<script>
$(document).ready(function(){

    $('#production_employee_menu_link').addClass('active');
    $('#production_employee_menu_link_icon').addClass('active');
    $('#production_employee').addClass('navbtnactive');

    let company_f = $('#company_f');
    let department_f = $('#department_f');

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
                    company: company_f.val()
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
                title: 'Production Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Production Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Production Information',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-print mr-2"></i> Print',
                customize: function(win) {
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                },
            },
        ],
        "order": [
            [0, "desc"]
        ],
        ajax: {
            "url": "{!! route('ProductionDetaillist') !!}",
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'department_name', name: 'department_name' },
            { data: 'men_incentive', name: 'men_incentive' },
            { data: 'women_incentive', name: 'women_incentive' },
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

    $('#create_record').click(function(){
        $('.modal-title').text('Add Production Detail');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();

        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addProductionDetail') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('ProductionDetail.update') }}";
        }

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
        
        $('#company_f').empty().trigger('change');
        $('#department_f').empty().trigger('change');
        
        $.ajax({
            url: "ProductionDetail/" + id + "/edit",
            dataType: "json",
            success: function (data) {
                $('#company_f').append('<option value="' + data.result.company_id + '" selected>' + data.result.company_name + '</option>').trigger('change');
                
                $('#department_f').append('<option value="' + data.result.department_id + '" selected>' + data.result.department_name + '</option>').trigger('change');
                
                $('#men_incentive').val(data.result.men_incentive);
                $('#women_incentive').val(data.result.women_incentive);
                $('#remark').val(data.result.remark);

                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Production Detail');
                $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            }
            })
        }
    });

    var user_id;

    $(document).on('click', '.delete', async function () {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
                url: "{{ url('ProductionDetail/destroy/') }}/" + user_id,
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
</script>


@endsection

                                