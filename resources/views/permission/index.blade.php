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
                        <span>User Permission</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12 text-right">
                            @can('role-create')
                                <button type="button" class="btn btn-primary btn-sm px-4" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Create New Permission</button>

                            @endcan
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

                             <table class="table table-striped table-sm" width="100%" id="permissiontable">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Module</th>
                                    <th width="280px">Action</th>
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
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h6 class="modal-title" id="staticBackdropLabel"></h6>
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

                            
                                 <div class="row">
                                <!-- Permission Name -->
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            id="name" 
                                            class="form-control form-control-sm" 
                                            placeholder="Name" 
                                            value="{{ old('name') }}">
                                        
                                    </div>
                                </div>

                                <!-- Module List -->
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Permission:</strong><br>
                                        <input 
                                            list="modules" 
                                            name="module" 
                                            id="module" 
                                            class="form-control form-control-sm" 
                                            value="{{ old('module') }}">
                                        <datalist id="modules">
                                            @foreach($modules as $module)
                                                <option value="{{ $module->module }}"></option>
                                            @endforeach
                                        </datalist>
                                       
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
        // $('#permissiontable').DataTable({
        //     "destroy": true,
        //     "processing": true,
        //     "serverSide": true,
        //     dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
        //         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        //     "buttons": [{
        //             extend: 'csv',
        //             className: 'btn btn-success btn-sm',
        //             title: 'User Role Information',
        //             text: '<i class="fas fa-file-csv mr-2"></i> CSV',
        //         },
        //         { 
        //             extend: 'pdf', 
        //             className: 'btn btn-danger btn-sm', 
        //             title: 'User Role Information', 
        //             text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
        //             orientation: 'landscape', 
        //             pageSize: 'legal', 
        //             customize: function(doc) {
        //                 doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
        //             }
        //         },
        //         {
        //             extend: 'print',
        //             title: 'User Role Information',
        //             className: 'btn btn-primary btn-sm',
        //             text: '<i class="fas fa-print mr-2"></i> Print',
        //             customize: function(win) {
        //                 $(win.document.body).find('table')
        //                     .addClass('compact')
        //                     .css('font-size', 'inherit');
        //             },
        //         },
        //         // 'copy', 'csv', 'excel', 'pdf', 'print'
        //     ],
        //     "order": [
        //         [0, "desc"]
        //     ],
        //     ajax: {
        //         url: scripturl + "/permission_list.php",
        //         type: "POST",
        //     },
        //     columns: [
        //         { data: 'id', name: 'id' },
        //         { data: 'name', name: 'name' },
        //         { data: 'module', name: 'module' },
        //         { 
        //             data: 'emp_id',
        //             name: 'emp_id',
        //             render: function(data, type, full) {
        //                 return '<a class="btn btn-outline-primary btn-sm edit" ><i class="fa fa-pencil-alt"></i></a>';
        //             }
        //         }
        //     ],
        //     order: [[2, "desc"]], // last column
        //     destroy: true
        // });

        $('#permissiontable').DataTable({
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
                    url: scripturl + "/permission_list.php",
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
                        data: 'module', 
                        name: 'module'
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

            $('#administrator_menu_link').addClass('active');
            $('#administrator_menu_link_icon').addClass('active');
            $('#permissions_link').addClass('navbtnactive');


            $('#create_record').click(function(){
                $('.modal-title').text('Create New Permission');
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
                    action_url = "{{ route('permissions.store') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('permissions.update') }}";
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
                    url: "{{ route('permissions.edit', ':id') }}".replace(':id', id),
                    dataType: "json",
                    success: function (data) {
                        // Set form values
                        $('#name').val(data.permission.name);
                        $('#hidden_id').val(data.permission.id);
                        $('#module').val(data.permission.module);

                        // Update datalist dynamically
                        let datalist = $('#modules');
                        datalist.empty();
                        $.each(data.modules, function (index, item) {
                            datalist.append(`<option value="${item.module}"></option>`);
                        });

                        // Show modal
                        $('.modal-title').text('Edit Permission');
                        $('#action_button').show().html('Update');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    }
                });

                }
            });

            $(document).on('click', '.delete', async function() {
                var r = await Otherconfirmation("You want to remove this ? ");
                if (r == true) {
                    id = $(this).attr('id');
                    $.ajax({
                        url: "permissions/destroy/" + id,
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
