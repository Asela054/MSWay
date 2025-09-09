@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.production&task_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Task</th>
                                    <th>Date</th>
                                    <th>Task Status</th>
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
        <div class="modal-dialog modal-dialog-centered  modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Finish Task</h5>
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
                                    
                                        <div class="row col-6">
                                            <div class="col-12">
                                                <label class="small font-weight-bold text-dark">Allowance Type:</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="task_type" id="semi" value="Hourly">
                                                    <label class="form-check-label small font-weight-bold text-dark" for="semi" required>Hourly</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="task_type" id="full" value="Daily">
                                                    <label class="form-check-label small font-weight-bold text-dark" for="full" required >Daily</label>
                                                </div>
                                            </div>
                                        </div>
                                     <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Number of days/hours</label>
                                        <input type="number" step="any" name="quntity" id="quntity" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Note</label>
                                        <input type="text" name="desription" id="desription" class="form-control form-control-sm"/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <!-- Modal Area Start -->
  <div class="modal fade" id="cancelformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Cancel Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result_cancel"></span>
                                <form method="post" id="cancelform" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="row">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Cancel description</label>
                                        <input type="text" name="cancel_desription" id="cancel_desription" class="form-control form-control-sm" required/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="cancel_id" id="cancel_id" />
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

    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailytask').addClass('navbtnactive');


     // DataTable initialization
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{!! route('taskendinglist') !!}",
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'task_name',
                name: 'task_name'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
            data: 'task_status',
            name: 'task_status',
            render: function(data, type, row) {
                var statusText = '';
                var statusClass = '';
                
                if (data == 1) {
                    statusText = 'Processing';
                    statusClass = 'text-warning';
                } else if (data == 2) {
                    statusText = 'Completed';
                    statusClass = 'text-success';
                } else{
                    statusText = 'Cancelled';
                    statusClass = 'text-danger'; 
                }
                
                return '<span class="' + statusClass + '">' + statusText + '</span>';
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


    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('.modal-title').text('Finish Task');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#hidden_id').val(id);
        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url:  '{!! route("taskendingfinish") !!}',
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {
                var html = '';
                if (data.errors) {
                    const errors = Array.isArray(data.errors) ? data.errors : [data.errors];
                    
                    html = '<div class="alert alert-danger">' +
                        errors.map(error => `<p>${error}</p>`).join('') +
                        '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

     $(document).on('click', '.delete', function () {
        var id = $(this).attr('id');
        $('#form_result_cancel').html('');
        $('#cancel_id').val(id);
        $('#cancelformModal').modal('show');
    });
    
    $('#cancelform').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url:  '{!! route("taskendingcancel") !!}',
            method: "POST",
            data: $(this).serialize(),
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
                    location.reload()
                }
                $('#form_result_cancel').html(html);
            }
        });
    });

});
</script>


@endsection