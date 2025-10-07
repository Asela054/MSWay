@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Pay Grade</span>
                </h1>
            </div>
        </div>
    </div>    
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Grade</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="paygradetable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>PAY GRADE</th>
                                    <th class="text-right">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paygrade as $paygrades)
                                <tr>
                                    <td>{{$paygrades->id}}</td>
                                    <td>{{$paygrades->pay_grade}}</td>
                                    <td class="text-right">
                                            <button name="edit" id="{{$paygrades->id}}" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                            <button type="submit" name="delete" id="{{$paygrades->id}}" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                @endforeach
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Pay Grade</h5>
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
                                    <label class="small font-weight-bold text-dark">Pay Grade</label>
                                    <input type="text" name="paygrade" id="paygrade" class="form-control form-control-sm" />
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
    <!-- Modal Area End -->
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#employeemaster').addClass('navbtnactive');

    $('#paygradetable').DataTable(
    {
        "destroy": true,
        "processing": true,
        "serverSide": false, 
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Pay Grade Details',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Pay Grade Details', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Pay Grade Details',
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
    });

    $('#create_record').click(function () {
        $('.modal-title').text('Add Pay Grade');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset(); 
        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addPayGrade') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('PayGrade.update') }}";
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
                    $('#formTitle')[0].reset();
                    $('#formModal').modal('hide'); 
                    actionreload(actionJSON);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
                const actionObj = {
                    icon: 'fas fa-warning',
                    title: '',
                    message: 'Something went wrong!',
                    url: '',
                    target: '_blank',
                    type: 'danger'
                };
                const actionJSON = JSON.stringify(actionObj, null, 2);
                action(actionJSON);
            }
        });
    });


    $(document).on('click', '.edit', async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "PayGrade/" + id + "/edit",
            dataType: "json",
            success: function (data) {
                $('#paygrade').val(data.result.pay_grade);
                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Pay Grade');
                $('#action_button').val('Edit');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
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
                url: "PayGrade/destroy/" + user_id,
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
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                }
            })
        }
    });

});
</script>

@endsection