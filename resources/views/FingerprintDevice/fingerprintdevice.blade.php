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
                    <div class="col-12">
                        @can('finger-print-device-create')
                            <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Device</button>
                        @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="divicestable">
                            <thead>
                                <tr>
                                    <th>Id </th>
                                    <th>IP</th>
                                    <th>Name</th>
                                    <th>Serial No</th>
                                    <th>EMI No</th>
                                    <th>Connection No</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($device as $devices)
                                <tr>
                                    <td>{{$devices->id}}</td>
                                    <td>{{$devices->ip}}</td>
                                    <td>{{$devices->name}}</td>
                                    <td>{{$devices->sno}}</td>
                                    <td>{{$devices->emi}}</td>
                                    <td>{{$devices->conection_no}}</td>
                                    <td>{{$devices->location}}</td>
                                    <td> @if($devices->status=='1') Activated @else Deactivated @endif </td>
                                    <td class="text-right">
                                        @can('finger-print-device-edit')
                                            <button name="edit" id="{{$devices->id}}" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        @endcan
                                        @can('finger-print-device-delete')
                                            <button type="submit" name="delete" id="{{$devices->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
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
                                    <label class="small font-weight-bold text-dark">IP</label>
                                    <input type="text" name="ip" id="ip" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Name</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Location</label>
                                    <select name="location" id="location" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        @foreach($location as $locations)
                                        <option value="{{$locations->id}}">{{$locations->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Sno</label>
                                    <input type="text" name="sno" id="sno" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">EMI</label>
                                    <input type="text" name="emi" id="emi" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Connection No</label>
                                    <input type="text" name="connectionno" id="connectionno" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Status</label><br>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="status1" name="status" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="status1">Active</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="status2" name="status" class="custom-control-input" value="0">
                                        <label class="custom-control-label" for="status2">Deactive</label>
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

    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
    $('#attendantmaster').addClass('navbtnactive');

    $('#divicestable').DataTable();

    $('#create_record').click(function () {
        $('.modal-title').text('Add Fingerprint Device');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');

        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addFingerprintDevice') }}";
        }


        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('FingerprintDevice.update') }}";
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
                    // $('#titletable').DataTable().ajax.reload();
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
            url: "FingerprintDevice/" + id + "/edit",
            dataType: "json",
            success: function (data) {
                $('#ip').val(data.result.ip);
                $('#name').val(data.result.name);
                $('#location').val(data.result.location);
                $('#sno').val(data.result.sno);
                $('#emi').val(data.result.emi);
                $('#connectionno').val(data.result.conection_no);
                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Fingerprint Device');
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
            url: "FingerprintDevice/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#user_table').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload();
            }
        })
    });

});
</script>

@endsection