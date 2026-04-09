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
                        <span>Users</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12 text-right">
                                <button type="button" class="btn btn-primary btn-sm px-4" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Create User</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <span>{{ $message }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <table class="table table-striped table-sm text-uppercase" style="width: 100%"  id="userstable">
                                <thead>
                                <tr>
                                    <th>Emp ID</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                               
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
                    <h6 class="modal-title" id="staticBackdropLabel">Add New User</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div id="form_result"></div>
                            <form method="post" id="formTitle">
                             {{ csrf_field() }}

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Name*</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="Name" required>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Email*</label>
                                <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="Email" required>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Password</label>
                                <input type="password" name="password" id="password" class="form-control form-control-sm" placeholder="Password">
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Confirm Password</label>
                                <input type="password" name="confirm-password" id="confirm-password" class="form-control form-control-sm" placeholder="Confirm Password">
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Role*</label>
                                <select name="roles[]" id="role" class="form-control form-control-sm" required multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-dark">Accessible Companies</label>
                                <select name="company[]" id="company" class="form-control form-control-sm" multiple></select>
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
        $('#user_link').addClass('navbtnactive');

        let company = $('#company');

        company.select2({
            placeholder: 'Select Companies',
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

        $('#userstable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Users  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Users  Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Users  Information',
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
            url: scripturl + "/userslist.php",
            type: "POST",
            data: {},
        },
        columns: [
            { 
                data: 'emp_id', 
                name: 'emp_id'
            },
            { 
                data: 'company_name', 
                name: 'company_name'
            },
            { 
                data: 'name', 
                name: 'name'
            },
            { 
                data: 'email', 
                name: 'email'
            },
            { 
                data: 'roles', 
                name: 'roles'
                // render: function(data, type, row) {
                //     if (data && data.length > 0) {
                //         return data.map(role => `<span class="badge badge-info mr-1">${role}</span>`).join(' ');
                //     }
                //     return '';
                // }
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

                //   buttons += '<a class="btn btn-info btn-sm  mr-1" href="/users/' + row.id + '"><i class="fa fa-eye"></i></a>';
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

    $(document).on('click', '.delete', async function() {
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            id = $(this).attr('id');
            $.ajax({
                url: "users/destroy/" + id,
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

    
    // In create_record click
    $('#create_record').click(function(){
        $('.modal-title').text('Create New User');
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
            action_url = "{{ route('users.store') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('users.update') }}";
        }

        $('#form_result').html('');

        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if (data.errors) {
                    var errorHtml = '<div class="alert alert-danger alert-sm p-2 mb-2">' +
                                        '<ul class="mb-0 pl-3">';
                    $.each(data.errors, function(i, error) {
                        errorHtml += '<li class="small">' + error + '</li>';
                    });
                    errorHtml += '</ul></div>';
                    $('#form_result').html(errorHtml);

                    // Scroll modal body to top to show errors
                    $('.modal-body').animate({ scrollTop: 0 }, 'fast');
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
                    $('#form_result').html('');
                    actionreload(actionJSON);
                }
            },
            error: function(xhr) {
                // Handle Laravel 422 validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<div class="alert alert-danger alert-sm p-2 mb-2">' +
                                        '<ul class="mb-0 pl-3">';
                    $.each(errors, function(field, messages) {
                        $.each(messages, function(i, message) {
                            errorHtml += '<li class="small">' + message + '</li>';
                        });
                    });
                    errorHtml += '</ul></div>';
                    $('#form_result').html(errorHtml);
                    $('.modal-body').animate({ scrollTop: 0 }, 'fast');
                } else {
                    $('#form_result').html(
                        '<div class="alert alert-danger alert-sm p-2 mb-2">' +
                            '<span class="small">Something went wrong. Please try again.</span>' +
                        '</div>'
                    );
                    $('.modal-body').animate({ scrollTop: 0 }, 'fast');
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
                    url: "{{ route('users.edit', ':id') }}".replace(':id', id),
                    dataType: "json",
                    success: function(data) {
                        $('#name').val(data.result.name);
                        $('#email').val(data.result.email);
                        $('#role').val(data.result.role).trigger('change');
                        $('#hidden_id').val(data.result.id);
                        $('.modal-title').text('Edit User');
                        $('#action_button').html('Edit');
                        $('#action').val('Edit');

                        // Populate Select2 companies
                        $('#company').empty();
                        if (data.result.companies && data.result.companies.length > 0) {
                            $.each(data.result.companies, function(i, item) {
                                var option = new Option(item.text, item.id, true, true);
                                $('#company').append(option);
                            });
                            $('#company').trigger('change');
                        }

                        $('#form_result').html(''); 
                        $('#formModal').modal('show');
                    }
                });
            }
        });

    });
</script>
@endsection
