@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.production&task_nav_bar')
               
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Product: {{$products->productname}}</h5>
                            @can('product-create')
                                <button type="button" class="btn btn-outline-primary btn-sm" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Machine</button>
                            @endcan
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Machine</th>
                                    <th>Semi Price</th>
                                    <th>Full Price</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($product_machines as $pm)
                                    <tr>
                                        <td>{{$pm->id}}</td>
                                        <td>{{$pm->machine}}</td>
                                        <td>{{$pm->semi_price}}</td>
                                        <td>{{$pm->full_price}}</td>
                                        <td class="text-right">
                                            @can('product-edit')
                                                <button name="edit" id="{{$pm->id}}" class="edit btn btn-outline-primary btn-sm" type="button"><i class="fas fa-pencil-alt"></i></button>
                                            @endcan
                                            @can('product-delete')
                                                <button type="button" name="delete" id="{{$pm->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
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
                        <h5 class="modal-title" id="staticBackdropLabel">Add Machine</h5>
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
                                        <label class="small font-weight-bold text-dark">Machine</label>
                                        <select name="machine" id="machine" class="form-control form-control-sm">
                                            <option value="">Select Machine</option>
                                            @foreach ($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Semi Finished Price</label>
                                        <input type="number" step="any" name="semi_price" id="semi_price" class="form-control form-control-sm" />
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Full Finished Price</label>
                                        <input type="number" step="any" name="full_price" id="full_price" class="form-control form-control-sm" />
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="action" id="action" value="Add" />
                                    <input type="hidden" name="product_id" id="product_id" value="{{$id}}" />
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

             $('#production_menu_link').addClass('active');
             $('#production_menu_link_icon').addClass('active');
            $('#dailyprocess').addClass('navbtnactive');

            $('#dataTable').DataTable();

            $('#create_record').click(function(){
                $('.modal-title').text('Add New Machine');
                $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
                $('#action').val('Add');
                $('#form_result').html('');
                $('#formTitle')[0].reset();

                $('#formModal').modal('show');
            });

            $('#formTitle').on('submit', function(event){
                event.preventDefault();
                var action_url = '';

                if ($('#action').val() == 'Add') {
                    action_url = "{{ route('addMachine') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('Machine.update') }}";
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
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                        $('#form_result').html(html);
                    },
                    error: function(xhr, status, error) {
                        var html = '<div class="alert alert-danger">An error occurred: ' + error + '</div>';
                        $('#form_result').html(html);
                    }
                });
            });

            $(document).on('click', '.edit', function () {
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "{{ url('Machine') }}/" + id + "/edit",
                    dataType: "json",
                    success: function (data) {
                        $('#machine').val(data.result.machine_id);
                        $('#semi_price').val(data.result.semi_price);
                        $('#full_price').val(data.result.full_price);
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Machine');
                        $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Update');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Error occurred while fetching data');
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
                    url: "{{ url('Machine/destroy') }}/" + user_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {
                        setTimeout(function () {
                            $('#confirmModal').modal('hide');
                            $('#ok_button').text('OK');
                            alert('Data Deleted');
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        $('#ok_button').text('OK');
                        alert('Error occurred while deleting');
                    }
                })
            });
        });
    </script>

@endsection