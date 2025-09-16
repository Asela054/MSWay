@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.attendant&leave_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                    <span>FingerPrint User</span>
                </h1>
            </div>
        </div>
    </div>  

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-sm-8 col-md-10">
                        <form class="form" method="POST">
                            {{ csrf_field() }}
                            <div class="form-row mb-1">

                                <div class="col-sm-6 col-md-3">
                                    <label class="small font-weight-bold text-dark">Location*</label>
                                    <select name="device" id="device" class="form-control form-control-sm" required>
                                        <option value="">Select</option>
                                        @foreach($device as $devices)
                                        <option data-fname="{{$devices->name}}"
                                            value="{{$devices->ip}}">{{$devices->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <label class="small font-weight-bold text-dark">&nbsp;</label><br>
                                 
                                        <button type="button" name="getuserdata" id="getuserdata" class="btn btn-primary btn-sm getuserdata"><i class="fas fa-search mr-2"></i>Get data</button>
                                  
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-4 col-md-2">
                        <br>
                        <a href="exportFPUser" class="btn btn-success btn-sm fa-pull-right"><i class="fas fa-file-excel mr-2"></i>Export data</a>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap w-100" id="fpusertable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>USER ID</th>
                                    <th>NAME</th>
                                    <th>CARD NO</th>
                                    <th>ROLE</th>
                                    <th>PASSWORD</th>
                                    <th>LOCATION</th>
                                    <th class="text-right">ACTION</th>
                                </tr>
                            </thead>
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

                                <div class="form-row mb-1">
                                    <div class="col-sm-8 col-md-4">
                                        <label class="small font-weight-bold text-dark">ID*</label>
                                        <input type="text" name="id" id="id" class="form-control form-control-sm" required/>
                                    </div>
                                    <div class="col-sm-8 col-md-8">
                                        <label class="small font-weight-bold text-dark">Name*</label>
                                        <input type="text" name="name" id="name" class="form-control form-control-sm" required/>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col-sm-8 col-md-6">
                                        <label class="small font-weight-bold text-dark">Card No*</label>
                                        <input type="text" name="cardno" id="cardno"
                                            class="form-control form-control-sm" required/>
                                    </div>
                                    <div class="col-sm-8 col-md-6">
                                        <label class="small font-weight-bold text-dark">Role*</label>
                                        <select name="role" id="role" class="form-control form-control-sm" required>
                                            <option value="">Please Select</option>
                                            @foreach($title as $titles)
                                            <option value="{{$titles->id}}">{{$titles->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row mb-1">
                                    <div class="col-sm-8 col-md-6">
                                        <label class="small font-weight-bold text-dark">Password*</label>
                                        <input type="text" name="password" id="password"
                                            class="form-control form-control-sm" required/>
                                    </div>
                                    <div class="col-sm-8 col-md-6">
                                        <label class="small font-weight-bold text-dark">FP Location*</label>
                                        <select name="devices" id="devices"
                                            class="form-control form-control-sm shipClass" required>
                                            <option value="">Please Select</option>
                                            @foreach($device as $devices)
                                            <option value="{{$devices->ip}}">{{$devices->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                    <button type="button" name="comfirm_users" id="comfirm_users" class="btn btn-primary px-3 btn-sm">Confirm</button>
                    <button type="button" class="btn btn-danger px-3 btn-sm" data-dismiss="modal">Cancel</button>
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

    @can('finger-print-user-edit')
        canEditfingerprint = true;
    @endcan
    @can('finger-print-user-delete')
        canDeletefingerprint = true;
    @endcan

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');

      $('#fpusertable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Finger Print User Details',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Finger Print User Details', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Finger Print User Details',
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
            url: scripturl + "/fingerprintuserlist.php",
            type: "POST",
            data: {},
        },
        columns: [
            { 
                data: 'id', 
                name: 'id'
            },
            { 
                data: 'userid', 
                name: 'userid'
            },
            { 
                data: 'name', 
                name: 'name'
            },
            { 
                data: 'cardno', 
                name: 'cardno'
            },
            { 
                data: 'role', 
                name: 'role'
            },
            { 
                data: 'password', 
                name: 'password'
            },
            { 
                data: 'location', 
                name: 'location'
            },
            {
                data: 'id',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';

                    if (canEditfingerprint) {
                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    }

                    if (canDeletefingerprint) {
                        buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';
                    }

                    return buttons;
                }
            }
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
      });

    $('#create_record').click(function () {
        $('.modal-title').text('Add Fingerprint User');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $(document).on('click', '.getuserdata', function () {
        var device = $('#device').val();
        if (device != '') {
            $('#getuserdataModal').modal('show');
        } 
         else {
        Swal.fire({
            position: "top-end",
            icon: 'warning',
            title: 'Please select a Location First',
            showConfirmButton: false,
            timer: 2500
        });
    }

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

    $(document).on('click', '.edit', async function () {
         var r = await Otherconfirmation("You want to Edit this ? ");
        if (r == true) {
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
        }
    });

    var user_id;

    $(document).on('click', '.delete',async function () {
          var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            user_id = $(this).attr('id');
            $.ajax({
            url: "FingerprintUser/destroy/" + user_id,
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