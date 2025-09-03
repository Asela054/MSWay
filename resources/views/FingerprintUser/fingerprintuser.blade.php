@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.attendant&leave_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-10">
                        <form class="form" method="POST">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="col-3">
                                    <label class="small font-weight-bold text-dark">Location</label>
                                    <select name="device" id="device" class="form-control form-control-sm" required>
                                        <option value="">Select</option>
                                        @foreach($device as $devices)
                                        <option data-fname="{{$devices->name}}"
                                            value="{{$devices->ip}}">{{$devices->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label class="small font-weight-bold text-dark">&nbsp;</label><br>
                                    @can('finger-print-user-create')
                                        <button type="button" name="getuserdata" id="getuserdata" class="btn btn-outline-primary btn-sm getuserdata"><i class="fas fa-search mr-2"></i>Get data</button>
                                    @endcan
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-2">
                        <br>
                        <a href="exportFPUser" class="btn btn-success btn-sm fa-pull-right"><i class="fas fa-file-excel mr-2"></i>Export data</a>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="fpusertable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Card No</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                    <th>Location</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->userid}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->cardno}}</td>
                                    <td>{{$user->role}}</td>
                                    <td>{{$user->password}}</td>
                                    <td>{{$user->location}}</td>
                                    <td class="text-right">
                                        @can('finger-print-user-edit')
                                            <button name="edit" id="{{$user->id}}" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        @endcan
                                        @can('finger-print-user-delete')
                                            <button type="submit" name="delete" id="{{$user->userid}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
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
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Location</h5>
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
                                    <label class="small font-weight-bold text-dark">ID</label>
                                    <input type="text" name="id" id="id" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Name</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">cardno</label>
                                    <input type="text" name="cardno" id="cardno" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Role</label>
                                    <select name="role" id="role" class="form-control form-control-sm">
                                        <option value="">Please Select</option>
                                        @foreach($title as $titles)
                                        <option value="{{$titles->id}}">{{$titles->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">password</label>
                                    <input type="text" name="password" id="password" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">FP Location</label>
                                    <select name="devices" id="devices" class="form-control form-control-sm shipClass">
                                        <option value="">Please Select</option>
                                        @foreach($device as $devices)
                                        <option value="{{$devices->ip}}">{{$devices->name}}</option>
                                        @endforeach
                                    </select>
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
    <div class="modal fade" id="getuserdataModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                            <h4 class="font-weight-normal">Please check the devices connection and confirm?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="comfirm_users" id="comfirm_users" class="btn btn-danger px-3 btn-sm">Confirm</button>
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

$(document).ready(function() {

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');

    $('#fpusertable').DataTable({
        "order": [
            [5, "asc"]
        ]
    });

    $('#create_record').click(function () {
        $('.modal-title').text('Add Fingerprint User');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            // action_url = "{{ route('addJobCategory') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('FingerprintUser.update') }}";
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
                    location.reload();
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "FingerprintUser/" + id + "/edit",
            dataType: "json",
            success: function (data) {

                $('#id').val(data.result.id);
                $('#userid').val(data.result.userid);
                $('#name').val(data.result.name);
                $('#cardno').val(data.result.cardno);
                $('#uid').val(data.result.uid);
                $('#role').val(data.result.role);
                $('#password').val(data.result.password);
                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Fingerprint User');
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
            url: "FingerprintUser/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
            }
        })
    });

});

$(document).on('click', '.getuserdata', function () {



    var device = $('#device').val();
    if (device != '') {
        $('#getuserdataModal').modal('show');


    } else {
        alert('Select Location');
    }
});
$('#comfirm_users').click(function () {
    var device = $('#device').val();
    var _token = $('input[name="_token"]').val();
    $.ajax({
        url: "FingerprintUser/getdeviceuserdata",
        method: "POST",
        data: {
            device: device,
            _token: _token
        },
        dataType: "json",
        beforeSend: function () {
            $('#comfirm_users').text('Procesing...');
        },
        success: function (data) {
            var html = '';
            if (data.errors) {
                html = '<div class="alert alert-danger">' + data.errors + '</div>';
                $('#comfirm_users').text('confirm');
            }
            if (data.success) {
                html = '<div class="alert alert-success">' + data.success + '</div>';
                location.reload()
            }
            $('#confirm_result').html(html);
        },
    })
 });



</script>

@endsection