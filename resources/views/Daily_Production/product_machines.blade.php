@extends('layouts.app')

@section('content')

    <main>
         <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.production&task_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-ballot-check"></i></div>
                        <span>Product: {{$products->productname}}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-12">
                                <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Machine</button>
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
                                    <th>MACHINE</th>
                                    <th>SEMI PRICE</th>
                                    <th>FULL PRICE</th>
                                    <th class="text-right">ACTION</th>
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
                                                <button name="edit" id="{{$pm->id}}" class="edit btn btn-primary btn-sm  mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                           
                                                <button type="button" name="delete" id="{{$pm->id}}" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>
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
                                        <label class="small font-weight-bolder ">Machine *</label>
                                        <select name="machine" id="machine" class="form-control form-control-sm">
                                            <option value="">Select Machine</option>
                                            @foreach ($machines as $machine)
                                                <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bolder ">Semi Finished Price</label>
                                        <input type="number" step="any" name="semi_price" id="semi_price" class="form-control form-control-sm" />
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bolder ">Full Finished Price</label>
                                        <input type="number" step="any" name="full_price" id="full_price" class="form-control form-control-sm" />
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4">Add</button>
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
        <!-- Modal Area End -->
    </main>

@endsection


@section('script')

    <script>
        $(document).ready(function(){

             $('#production_menu_link').addClass('active');
             $('#production_menu_link_icon').addClass('active');
            $('#dailyprocess').addClass('navbtnactive');

            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Products  Information',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    { 
                        extend: 'pdf', 
                        className: 'btn btn-danger btn-sm', 
                        title: 'Products Information', 
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape', 
                        pageSize: 'legal', 
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Products  Information',
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
                drawCallback: function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

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
                    action_url = "{{ route('addProductMachine') }}";
                }
                if ($('#action').val() == 'Edit') {
                    action_url = "{{ route('productMachine.update') }}";
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
                                actionreload(actionJSON);
                            }
                    },
                    error: function(xhr, status, error) {
                        var html = '<div class="alert alert-danger">An error occurred: ' + error + '</div>';
                        $('#form_result').html(html);
                    }
                });
            });

            $(document).on('click', '.edit',async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    var id = $(this).attr('id');
                        $('#form_result').html('');
                        $.ajax({
                            url: "{{ url('productMachine') }}/" + id + "/edit",
                            dataType: "json",
                            success: function (data) {
                                $('#machine').val(data.result.machine_id);
                                $('#semi_price').val(data.result.semi_price);
                                $('#full_price').val(data.result.full_price);
                                $('#hidden_id').val(id);
                                $('.modal-title').text('Edit Machine');
                                $('#action_button').html('Update');
                                $('#action').val('Edit');
                                $('#formModal').modal('show');
                            }
                        })
                }
            });

            var user_id;

            $(document).on('click', '.delete',async function () {
                user_id = $(this).attr('id');
                  var r = await Otherconfirmation("You want to remove this ? ");
                    if (r == true) {
                        $.ajax({
                                url: "{{ url('productMachine/destroy') }}/" + user_id,
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
                                },
                            })
                    }
            });

        });
    </script>

@endsection