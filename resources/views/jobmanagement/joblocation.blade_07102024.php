
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @can('Job-Location-create')
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Job Locaton</button>
                            @endcan
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                <div class="center-block fix-width scroll-inner">
                                    <table class="table table-striped table-bordered table-sm small nowrap display"
                                        style="width: 100%" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>Location Name</th>
                                                <th>Contact No</th>
                                                <th>Address</th>
                                                <th>Altitude</th>
                                                <th>Longitude</th>
                                                <th>Completed</th>
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
        </div>
    </main>


            <!-- Modal Area Start -->
            <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header p-2">
                            <h5 class="modal-title" id="staticBackdropLabel">Add Job Category</h5>
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
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">Location Name</label>
                                                <input type="text" name="locationname" id="locationname" class="form-control form-control-sm" required />
                                            </div>
                                            
                                            <div class="col-6">
                                                <label class="small font-weight-bold text-dark">Address</label>
                                                <input type="text" name="address" id="address" class="form-control form-control-sm" required />
                                            </div>                                   
                                        </div>
                                        <div class="form-row mb-1">
                                            <div class="col-4">
                                                <label class="small font-weight-bold text-dark">Contact No</label>
                                                <input type="text" name="contactno" id="contactno" class="form-control form-control-sm" required />
                                            </div> 
                                            <div class="col-4">
                                                <label class="small font-weight-bold text-dark">Altitude</label>
                                                <input type="text" name="altitude"  id="altitude" class="form-control form-control-sm" required />
                                            </div>
                                            <div class="col-4">
                                                <label class="small font-weight-bold text-dark">Longitude</label>
                                            <input type="text" name="longitude" id="longitude" class="form-control form-control-sm" required />
                                            </div>
                                           
                                        </div>
                                        {{-- <div id="map" style="height: 350px;"></div> --}}
                                        <div class="form-group mt-3">
                                            {{-- <button type="button" id="getCoordinatesBtn" class="btn btn-primary btn-sm mt-2">Get Coordinates</button> --}}
                                            <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                        </div>
                                        <input type="hidden" name="action" id="action" value="1" />
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


@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#jobmanegment').addClass('navbtnactive');

          

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Job Category');
                $('#action_button').html('Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });


            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{!! route('joblocationslist') !!}",
                   
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'contactno', name: 'contactno' },
                    { data: 'location_address', name: 'location_address' },
                    { data: 'altitude', name: 'altitude' },
                    { data: 'longitude', name: 'longitude' },
                    { data: 'jobcomplete_status', name: 'jobcomplete_status' },
                    {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<div style="text-align: right;">' + data + '</div>';
                    }
                },
                ],
                "bDestroy": true,
                "order": [
                    [0, "desc"]
                ]
            });

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();
                var action_url = "{{ route('joblocationsave') }}";

                var locationname = $('#locationname').val();
                var address = $('#address').val();
                var contactno = $('#contactno').val();
                var altitude = $('#altitude').val();
                var longitude = $('#longitude').val();

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                            _token: '{{ csrf_token() }}',
                            locationname: locationname,
                            address: address,
                            contactno: contactno,
                            altitude: altitude,
                            longitude: longitude
                        },
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
                            location.reload()
                        }
                        $('#form_result').html(html);
                    }
                });
            });

    
              // edit function
            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
               
                $('#form_result').html('');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })

                $.ajax({
                    url: '{!! route("joblocationsedit") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: id },
                    success: function (data) {
                        $('#locationname').val(data.result.location_name);
                        $('#contactno').val(data.result.contactno);
                        $('#address').val(data.result.location_address);
                        $('#altitude').val(data.result.altitude);
                        $('#longitude').val(data.result.longitude);
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Job Category');
                        $('#action_button').html('Update');
                        $('#action').val('2');
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
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                $.ajax({
                    url: '{!! route("joblocationsdelete") !!}',
                        type: 'POST',
                        dataType: "json",
                        data: {id: user_id },
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#dataTable').DataTable().ajax.reload();
                        }, 2000);
                        location.reload()
                    }
                })
            });
        });

          function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }
    </script>


@endsection

