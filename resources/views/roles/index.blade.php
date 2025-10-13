@extends('layouts.app')
@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.administrator_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-gears"></i></div>
                        <span>User Role</span>
                    </h1>
                </div>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
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
                                    <strong>Permission:</strong>
                                    <br>
                                   <div class="row">
                                        @foreach($permission as $value)
                                            <div class="col-4 mb-2">
                                                <label>
                                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name">
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            


                            <div class="form-group mt-3">
                                <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4">
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

                        buttons += '<button name="view" id="'+row.id+'" class="view btn btn-info btn-sm mr-1" type="button" data-toggle="tooltip" title="View"><i class="fa fa-eye"></i></button>';
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

            

            $('#create_record').click(function(){
                $('.modal-title').text('Create New Role');
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
                    action_url = "{{ route('roles.store') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('roles.update') }}";
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

            $(document).on('click', '.edit', async function() {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "{{ route('roles.edit', ':id') }}".replace(':id', id),
                        dataType: "json",
                        success: function (data) {
                            $('#name').val(data.role.name);
                            $('#hidden_id').val(data.role.id);
                            $('input[name="permission[]"]').prop('checked', false);

                            // Loop through returned permissions and check relevant ones
                            $.each(data.rolePermissions, function (index, permission_id) {
                                $(`input[name="permission[]"][value="${permission_id}"]`).prop('checked', true);
                            });
                            $('.modal-title').text('Edit Role');
                            $('#action_button').show();
                            $('#action_button').html('Edit');
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
                        }
                    })
                }
            });

            $(document).on('click', '.view', async function() {
               
                    var id = $(this).attr('id');
                    $('#form_result').html('');
                    $.ajax({
                        url: "{{ route('roles.show', ':id') }}".replace(':id', id),
                        dataType: "json",
                        success: function (data) {
                            $('#name').val(data.role.name);
                            $('#hidden_id').val(data.role.id);
                            $('input[name="permission[]"]').prop('checked', false);

                            // Loop through returned permissions and check relevant ones
                            $.each(data.rolePermissions, function (index, permission_id) {
                                $(`input[name="permission[]"][value="${permission_id}"]`).prop('checked', true);
                            });
                            $('.modal-title').text('Edit Role');
                            $('#action_button').hide();
                            $('#action').val('Edit');
                            $('#formModal').modal('show');
                        }
                    })
                
            });

            $(document).on('click', '.delete', async function() {
                var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    id = $(this).attr('id');
                    $.ajax({
                        url: "roles/destroy/" + id,
                        beforeSend: function () {
                            $('#ok_button').text('Deleting...');
                        },
                        success: function (data) {//alert(data);
                           if (data.errors) {
                            const actionObj = {
                                icon: 'fas fa-warning',
                                title: '',
                                message: data.errors,
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
                    })
                }
            });


            

        });
    </script>
@endsection
