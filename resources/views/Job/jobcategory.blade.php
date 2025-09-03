@extends('layouts.app')

@section('content')

<main>
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-clipboard-list"></i></div>
                    <span>Job Category</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-4">
         <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        @can('job-category-create')
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Job Category</button>
                        @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <table class="table table-striped table-bordered table-sm small" id="jobcategorytable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Job Category</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobcategory as $jobcategories)
                                <tr>
                                    <td>{{$jobcategories->id}}</td>
                                    <td>{{$jobcategories->category}}</td>
                                    <td class="text-right">
                                        @can('job-category-edit')
                                            <button name="edit" id="{{$jobcategories->id}}" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        @endcan
                                        @can('job-category-delete')
                                            <button type="submit" name="delete" id="{{$jobcategories->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                        @endcan
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

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Job Category</h5>
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
                                    <label class="small font-weight-bold text-dark">Job Category</label>
                                    <input type="text" name="jobcategory" id="jobcategory" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mt-3">
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

    $('#job_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
    $('#job_collapse').addClass('show');
    $('#job_category_link').addClass('active');

    $('#jobcategorytable').DataTable();

    $('#create_record').click(function () {
        $('.modal-title').text('Add Job Category');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addJobCategory') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('JobCategory.update') }}";
        }


        $.ajax({
            url: action_url,
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
                    $('#formTitle')[0].reset();
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "JobCategory/" + id + "/edit",
            dataType: "json",
            success: function (data) {
                $('#jobcategory').val(data.result.category);
                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Job Category');
                $('#action_button').val('Edit');
                $('#action').val('Edit');
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
            url: "JobCategory/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    location.reload()
                }, 2000);
            }
        })
    });
});
</script>

@endsection