@extends('layouts.app')
@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.administrator_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                           <div class="col-12 text-right">
                             @can('role-create')
                                <button type="button" class="btn btn-primary btn-sm px-4" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Role User</button>
                            @endcan
                        </div>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <span>{{ $message }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 table-responsive">

                            <table class="table table-striped table-sm" width="100%" id="roletable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th width="280px">Action</th>
                                </tr>
                               </thead>
                            </table>

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

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title" id="staticBackdropLabel">Add Company</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle">
                             {{ csrf_field() }}

                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="Name">
                            </div>

                            <div class="form-group">
                                <strong>Email:</strong>
                                <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <strong>Password:</strong>
                                <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Password">
                            </div>

                            <div class="form-group">
                                <strong>Confirm Password:</strong>
                                <input type="password" name="confirm-password" id="confirm-password" class="form-control form-control-sm" placeholder="Confirm Password">
                            </div>

                            <div class="form-group">
                                <strong>Role:</strong>
                                <select name="roles[]" id="role" class="form-control form-control-sm" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm float-end px-4">
                                    <i class="fas fa-plus"></i>&nbsp;Add
                                </button>
                            </div>

                            <input type="hidden" name="action" id="action" value="Add">
                            <input type="hidden" name="hidden_id" id="hidden_id">
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>

@endsection

@section('script')
    <script>
        $(document).ready(function(){

            $('#administrator_menu_link').addClass('active');
            $('#administrator_menu_link_icon').addClass('active');
            $('#roles_link').addClass('navbtnactive');


            $('#roletable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'User Role Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'User Role Information', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'User Role Information',
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
                    url: scripturl + "/roleslist.php",
                    type: "POST",
                    data: {},
                },
                columns: [
                    { 
                        data: 'id', 
                        name: 'id'
                    },
                    { 
                        data: 'name', 
                        name: 'name'
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

                        buttons += '<a class="btn btn-info btn-sm  mr-1" href="/users/' + row.id + '"><i class="fa fa-eye"></i></a>';
                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                        buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm  mr-1" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                            return buttons;
                        }
                    }
                ],
                drawCallback: function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $(document).on('click', '.delete', function () {
                id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function () {
                $.ajax({
                    url: "users/destroy/" + id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        const actionObj = {
                            icon: 'fas fa-trash-alt',
                            title: '',
                            message: 'User Remove Successfully',
                            url: '',
                            target: '_blank',
                            type: 'danger'
                        };
                        const actionJSON = JSON.stringify(actionObj, null, 2);
                        actionreload(actionJSON);
                    }
                })
            });

            $('#create_record').click(function(){
                $('.modal-title').text('Create New User');
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });


        });
    </script>
@endsection
