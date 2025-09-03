@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-building"></i></div>
                        <span>Occupation Major Groups</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add New</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">

                            <div class="form_result"></div>

                            <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                <thead>
                                <tr>
                                    <th>Name </th>
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
        <!-- Modal Area Start -->
        <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Occupation Major Group</h5>
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
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Name*</label>
                                            <input type="text" name="name" id="name" class="form-control form-control-sm" />
                                        </div>
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

            $('#organization_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#organization_collapse').addClass('show');
            $('#occupation_group_link').addClass('active');

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('occupation_group_list_dt') !!}",
                },
                columns: [
                    { data: 'name', name: 'name' },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "bDestroy": true,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Occupation Major Group');
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function(event){
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('OccupationGroup.store') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('OccupationGroup.update_manual') }}";
                }

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (data) {//alert(data);
                        let html = '<div class="alert alert-success">' + data.message + '</div>';
                        $('#formTitle')[0].reset();
                        $('#dataTable').DataTable().ajax.reload();
                        $('#form_result').html(html);
                        $('#formModal').modal('hide');
                    },
                    error:function (response) {

                        let name_error = response.responseJSON.name;
                        let html = '<div class="alert alert-danger">';
                        if (name_error) {
                            html += '<span>' + name_error[0] + '</span>';
                        }
                        html += '</div>';

                        $('#form_result').html(html);
                    }

                });
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: '{{ route("OccupationGroup.fetch_single") }}',
                    method: "get",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function (data) {
                        $('#name').val(data.occupation_group.name);
                        $('#hidden_id').val(data.occupation_group.id);
                        $('.modal-title').text('Edit Occupation Major Group');
                        $('#action_button').html('Edit');
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
                    url: "OccupationGroup/destroy/" + user_id,
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