@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.employee_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-9">
                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif
                            <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('assignedDeviceInsert') }}">
                                {{ csrf_field() }}
                                <div class="form-row mt-2">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Device Type</label>
                                        <select required class="form-control form-control-sm @if ($errors->has('device_type')) border-danger-soft @endif"
                                                id="device_type" name="device_type">
                                            <option @if(old('device_type') == '') selected @endif value="">Select</option>
                                            <option @if(old('device_type') == 'Laptop') selected @endif value="Laptop">Laptop</option>
                                            <option @if(old('device_type') == 'Dekstop') selected @endif value="Dekstop">Dekstop</option>
                                            <option @if(old('device_type') == 'Phone') selected @endif value="Phone">Mobile Phone</option>
                                            <option @if(old('device_type') == 'Tab') selected @endif value="Tab">Tab</option>
                                            <option @if(old('device_type') == 'Router') selected @endif value="Router">Router</option>
                                            <option @if(old('device_type') == 'Sim') selected @endif value="Sim">Sim</option>
                                        </select>
                                        @if ($errors->has('device_type')) <p class="text-danger">{{ $errors->first('device_type') }}</p> @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Model Number</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('model_number')) border-danger-soft @endif"
                                               id="model_number" name="model_number" type="text" value="{{old('model_number')}}">
                                        @if ($errors->has('model_number')) <p class="text-danger">{{ $errors->first('model_number') }}</p> @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Serial Number</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('serial_number')) border-danger-soft @endif"
                                               id="serial_number" name="serial_number" type="text" value="{{old('serial_number')}}">
                                        @if ($errors->has('serial_number')) <p class="text-danger">{{ $errors->first('serial_number') }}</p> @endif
                                    </div>
                                </div>
                                <div class="form-row mt-2">    
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Other Ref. Number</label>
                                        <input class="form-control form-control-sm @if ($errors->has('other_ref_number')) border-danger-soft @endif"
                                               id="other_ref_number" name="other_ref_number" type="text" value="{{old('other_ref_number')}}">
                                        @if ($errors->has('other_ref_number')) <p class="text-danger">{{ $errors->first('other_ref_number') }}</p> @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Assigned Date</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('assigned_date')) border-danger-soft @endif"
                                               id="assigned_date" name="assigned_date" type="date" value="{{old('assigned_date')}}">
                                        @if ($errors->has('assigned_date')) <p class="text-danger">{{ $errors->first('assigned_date') }}</p> @endif
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Return Date</label>
                                        <input class="form-control form-control-sm @if ($errors->has('returned_date')) border-danger-soft @endif"
                                               id="returned_date" name="returned_date" type="date" value="{{old('returned_date')}}">
                                        @if ($errors->has('returned_date')) <p class="text-danger">{{ $errors->first('returned_date') }}</p> @endif
                                    </div>
                                </div>
                                <div class="form-group mt-3 text-right">
                                    @can('employee-edit')
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                        <button type="reset" class="btn btn-outline-danger btn-sm mr-2"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                    @endcan
                                </div>
                                <input type="hidden" class="form-control form-control-sm" id="emp_id" name="emp_id" value="{{$id}}">
                            </form>
                            <hr class="border-dark">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Device Type</th>
                                    <th>Model Number</th>
                                    <th>Serial Number</th>
                                    <th>Other Reference Number</th>
                                    <th>Assigned Date</th>
                                    <th>Return Date</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($assigned_devices as $ad)
                                    <tr>
                                        <td><a href="">{{$ad->device_type}}</a></td>
                                        <td>{{$ad->model_number}}</td>
                                        <td>{{$ad->serial_number}}</td>
                                        <td>{{$ad->other_ref_number}}</td>
                                        <td>{{$ad->assigned_date}}</td>
                                        <td>{{$ad->returned_date}}</td>
                                        <td class="text-right">
                                            @can('employee-edit')
                                                @if($ad->status == 1)
                                                    <button 
                                                        class="btn btn-success btn-sm btn-status mr-1 mt-1" 
                                                        data-id="{{ $ad->id }}" 
                                                        data-status="1">
                                                        Active
                                                    </button>
                                                @elseif($ad->status == 2)
                                                    <button 
                                                        class="btn btn-secondary btn-sm btn-status mr-1 mt-1" 
                                                        data-id="{{ $ad->id }}" 
                                                        data-status="2">
                                                        Returned
                                                    </button>
                                                @endif
                                                <a href="#" class="btn btn-outline-primary btn-sm btn-edit mr-1 mt-1" data-id="{{$ad->id}}"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="#" class="btn btn-outline-danger btn-sm btn-delete mr-1 mt-1" data-id="{{$ad->id}}"><i class="far fa-trash-alt"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                            <hr class="border-dark">

                        </div>
                        @include('layouts.employeeRightBar')
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modelDependent" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Assigned Device</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formDependent" class="form-horizontal">
                                {{ csrf_field() }}
                                <div class="form-row">
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Device Type</label>
                                        <select class="form-control form-control-sm"
                                                id="edit_device_type" name="device_type">
                                            <option >Select</option>
                                            <option value="Laptop">Laptop</option>
                                            <option value="Dekstop">Dekstop</option>
                                            <option value="Phone">Mobile Phone</option>
                                            <option value="Tab">Tab</option>
                                            <option value="Router">Router</option>
                                            <option value="Sim">Sim</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Model Number</label>
                                        <input required class="form-control form-control-sm"
                                               id="edit_model_number" name="model_number" type="text">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Serial Number</label>
                                        <input required class="form-control form-control-sm"
                                               id="edit_serial_number" name="serial_number" type="text">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Other Ref. Number</label>
                                        <input class="form-control form-control-sm"
                                               id="edit_other_ref_number" name="other_ref_number" type="text">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Assigned Date</label>
                                        <input required class="form-control form-control-sm"
                                               id="edit_assigned_date" name="assigned_date" type="date">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small font-weight-bold text-dark">Return Date</label>
                                        <input class="form-control form-control-sm"
                                               id="edit_returned_date" name="returned_date" type="date">
                                    </div>
                                </div>
                                <input class="btn btn-primary btn-sm float-right mt-2" type="submit" value="Update"/>
                                <input type="hidden" name="ad_id" id="ad_id"/>
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

    <div class="modal fade" id="statusModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="statusModalLabel" aria-hidden="true">
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
                            <h5 class="font-weight-normal">Change device status?</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" id="status_ok_button" class="btn btn-primary px-3 btn-sm">Yes</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
        $('#dataTable').DataTable();

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
	    $('#view_assigned_devices_link').addClass('navbtnactive');

        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $('#form_result').html('');
            $.ajax({
                url: "../getAssignedDeviceDetail/"+id,
                dataType: "json",
                success: function (data) {
                    $('#edit_device_type').val(data.result.device_type);
                    $('#edit_model_number').val(data.result.model_number);
                    $('#edit_serial_number').val(data.result.serial_number);
                    $('#edit_other_ref_number').val(data.result.other_ref_number);
                    $('#edit_assigned_date').val(data.result.assigned_date);
                    $('#edit_returned_date').val(data.result.returned_date);
                    $('#ad_id').val(data.result.id);
                    $('#modelDependent').modal('show');
                }
            })
        });

        $('#formDependent').on('submit', function(event){
            event.preventDefault();
            var action_url = '../updateAssignedDevice';

            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {//alert(data);

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
                        $('#formDependent')[0].reset();
                        //$('#titletable').DataTable().ajax.reload();
                        location.reload()
                    }
                    $('#form_result').html(html);
                }
            });
        });

        let status_device_id = 0;
        $(document).on('click', '.btn-status', function () {
            status_device_id = $(this).data('id');
            $('#statusModal').modal('show');
        });

        $('#status_ok_button').click(function () {
            $.ajax({
                url: "../assignedDeviceStatusUpdate",
                method: "POST",
                data: {
                    id: status_device_id,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        let button = $('.btn-status[data-id="' + status_device_id + '"]');
                        if (response.new_status == 1) {
                            button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                        } else {
                            button.removeClass('btn-success').addClass('btn-secondary').text('Returned');
                        }
                        $('#statusModal').modal('hide');
                    }
                }
            });
        });

        let device_id = 0;
        $(document).on('click', '.btn-delete', function () {
            device_id = $(this).data('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function () {
            $.ajax({
                url: "../assignedDeviceDelete/"+device_id,
                beforeSend: function () {
                    $('#ok_button').text('Deleting...');
                },
                success: function (data) {//alert(data);
                    setTimeout(function () {
                        let html = '<div class="alert alert-success"> Success </div>';
                        $('#form_result').html(html);
                    }, 2000);
                    $('#confirmModal').modal('hide');
                    location.reload()
                }
            })
        });

    </script>
@endsection
