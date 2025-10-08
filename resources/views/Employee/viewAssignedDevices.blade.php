@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                        <span>Assigned Devices</span>
                    </h1>
                </div>
            </div>
        </div>    
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-lg-9 col-md-12">
                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif
                            <form id="PdetailsForm" class="form-horizontal" method="POST" action="{{ route('assignedDeviceInsert') }}">
                                {{ csrf_field() }}
                                <div class="form-row mt-2">
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Device Type</label>
                                        <select required class="form-control form-control-sm @if ($errors->has('device_type')) border-danger-soft @endif"
                                                id="device_type" name="device_type">
                                            <option @if(old('device_type') == '') selected @endif value="">Select</option>
                                            <option @if(old('device_type') == 'Laptop') selected @endif value="Laptop">Laptop</option>
                                            <option @if(old('device_type') == 'Desktop') selected @endif value="Desktop">Desktop</option>
                                            <option @if(old('device_type') == 'Phone') selected @endif value="Phone">Mobile Phone</option>
                                            <option @if(old('device_type') == 'Tab') selected @endif value="Tab">Tab</option>
                                            <option @if(old('device_type') == 'Router') selected @endif value="Router">Router</option>
                                            <option @if(old('device_type') == 'Sim') selected @endif value="Sim">Sim</option>
                                        </select>
                                        @if ($errors->has('device_type')) <p class="text-danger small mb-0">{{ $errors->first('device_type') }}</p> @endif
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Model Number</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('model_number')) border-danger-soft @endif"
                                            id="model_number" name="model_number" type="text" value="{{old('model_number')}}">
                                        @if ($errors->has('model_number')) <p class="text-danger small mb-0">{{ $errors->first('model_number') }}</p> @endif
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Serial Number</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('serial_number')) border-danger-soft @endif"
                                            id="serial_number" name="serial_number" type="text" value="{{old('serial_number')}}">
                                        @if ($errors->has('serial_number')) <p class="text-danger small mb-0">{{ $errors->first('serial_number') }}</p> @endif
                                    </div>
                                </div>
                                <div class="form-row mt-2">    
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Other Ref. Number</label>
                                        <input class="form-control form-control-sm @if ($errors->has('other_ref_number')) border-danger-soft @endif"
                                            id="other_ref_number" name="other_ref_number" type="text" value="{{old('other_ref_number')}}">
                                        @if ($errors->has('other_ref_number')) <p class="text-danger small mb-0">{{ $errors->first('other_ref_number') }}</p> @endif
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Assigned Date</label>
                                        <input required class="form-control form-control-sm @if ($errors->has('assigned_date')) border-danger-soft @endif"
                                            id="assigned_date" name="assigned_date" type="date" value="{{old('assigned_date')}}">
                                        @if ($errors->has('assigned_date')) <p class="text-danger small mb-0">{{ $errors->first('assigned_date') }}</p> @endif
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-12 mb-2">
                                        <label class="small font-weight-bold text-dark">Return Date</label>
                                        <input class="form-control form-control-sm @if ($errors->has('returned_date')) border-danger-soft @endif"
                                            id="returned_date" name="returned_date" type="date" value="{{old('returned_date')}}">
                                        @if ($errors->has('returned_date')) <p class="text-danger small mb-0">{{ $errors->first('returned_date') }}</p> @endif
                                    </div>
                                </div>
                                <div class="form-group mt-3 text-right">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                        <button type="reset" class="btn btn-danger btn-sm mr-2"><i class="far fa-trash-alt"></i>&nbsp;Clear</button>
                                </div>
                                <input type="hidden" class="form-control form-control-sm" id="emp_id" name="emp_id" value="{{$id}}">
                            </form>
                            <hr class="border-dark">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                    <thead>
                                    <tr>
                                        <th class="text-nowrap">DEVICE TYPE</th>
                                        <th class="text-nowrap">MODEL NUMBER</th>
                                        <th class="text-nowrap">SERIAL NUMBER</th>
                                        <th class="text-nowrap d-none d-md-table-cell">OTHER REF NUMBER</th>
                                        <th class="text-nowrap">ASSIGNED DATE</th>
                                        <th class="text-nowrap d-none d-lg-table-cell">RETURN DATE</th>
                                        <th class="text-nowrap text-right">STATUS</th>
                                        <th class="text-nowrap text-right">ACTION</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($assigned_devices as $ad)
                                        <tr>
                                            <td class="text-nowrap">{{$ad->device_type}}</td>
                                            <td>{{$ad->model_number}}</td>
                                            <td>{{$ad->serial_number}}</td>
                                            <td class="d-none d-md-table-cell">{{$ad->other_ref_number}}</td>
                                            <td class="text-nowrap">{{$ad->assigned_date}}</td>
                                            <td class="text-nowrap d-none d-lg-table-cell">{{$ad->returned_date}}</td>
                                            <td class="text-center text-nowrap">
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
                                                    @endif </td>
                                                    <td class="text-right text-nowrap">
                                                    <a href="#" class="btn btn-primary btn-sm btn-edit mr-1 mt-1" data-id="{{$ad->id}}"><i class="fas fa-pencil-alt"></i></a>
                                                    <a href="#" class="btn btn-danger btn-sm btn-delete mr-1 mt-1" data-id="{{$ad->id}}"><i class="far fa-trash-alt"></i></a>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Edit Assigned Device</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row">
                    <div class="col">
                        <span id="form_result"></span>
                        <form method="post" id="formDependent" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Device Type</label>
                                    <select class="form-control form-control-sm"
                                            id="edit_device_type" name="device_type">
                                        <option>Select</option>
                                        <option value="Laptop">Laptop</option>
                                        <option value="Desktop">Desktop</option>
                                        <option value="Phone">Mobile Phone</option>
                                        <option value="Tab">Tab</option>
                                        <option value="Router">Router</option>
                                        <option value="Sim">Sim</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Model Number</label>
                                    <input required class="form-control form-control-sm"
                                           id="edit_model_number" name="model_number" type="text">
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Serial Number</label>
                                    <input required class="form-control form-control-sm"
                                           id="edit_serial_number" name="serial_number" type="text">
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Other Ref. Number</label>
                                    <input class="form-control form-control-sm"
                                           id="edit_other_ref_number" name="other_ref_number" type="text">
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Assigned Date</label>
                                    <input required class="form-control form-control-sm"
                                           id="edit_assigned_date" name="assigned_date" type="date">
                                </div>
                                <div class="col-md-6 col-12 mb-2">
                                    <label class="small font-weight-bold text-dark">Return Date</label>
                                    <input class="form-control form-control-sm"
                                           id="edit_returned_date" name="returned_date" type="date">
                                </div>
                            </div>
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-danger btn-sm mr-2" data-dismiss="modal">Cancel</button>
                                <input class="btn btn-primary btn-sm" type="submit" value="Update"/>
                            </div>
                            <input type="hidden" name="ad_id" id="ad_id"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2 border-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-3 py-4">
                <div class="row">
                    <div class="col text-center">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5 class="font-weight-normal">Are you sure you want to remove this data?</h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2 border-0 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-4" data-dismiss="modal">Cancel</button>
                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger btn-sm px-4">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header p-2 border-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-3 py-4">
                <div class="row">
                    <div class="col text-center">
                        <i class="fas fa-question-circle text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="font-weight-normal">Change device status?</h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2 border-0 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-4" data-dismiss="modal">No</button>
                <button type="button" id="status_ok_button" class="btn btn-primary btn-sm px-4">Yes</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
    <script>
        $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Assign device  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Assign device Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Assign device  Information',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-print mr-2"></i> Print',
                customize: function(win) {
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                },
            },
        ],
        "order": [
            [0, "desc"]
        ],
        }
        );

        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#employeeinformation').addClass('navbtnactive');
	    $('#view_assigned_devices_link').addClass('active');

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
