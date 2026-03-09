@extends('layouts.app')

@section('content')

<main> 
     <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.production&task_nav_bar_opma')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-ballot-check"></i></div>
                        <span>Machine Downtime Log</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div> <div class="col-12">
                        <hr class="border-dark">
                    </div>

                    <div class="col-12">
                            <button type="button" class="btn btn-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Downtime Log</button>
                    </div><br><br>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>MACHINE</th>
                                    <th>TYPE</th>
                                    <th>DATE</th>
                                    <th>FROM</th>
                                    <th>TO</th>
                                    <th class="text-right">ACTION</th>
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

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
              <div class="offcanvas-header">
                  <h2 class="offcanvas-title font-weight-bolderer" id="offcanvasRightLabel">Records Filter Options</h2>
                  <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                      <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                  </button>
              </div>
              <div class="offcanvas-body">
                  <ul class="list-unstyled">
                      <form class="form-horizontal" id="formFilter">
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Machine</label>
                                <select name="filtermachine" id="filtermachine" class="form-control form-control-sm">
                                    <option value="">Select Machine</option>
                                     @foreach($machines as $machine)
                                            <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                        @endforeach
                                </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Type</label>
                                    <select name="product" id="product" class="form-control form-control-sm">
                                        <option value="">Select Product</option>
                                         @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                                        @endforeach
                                    </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> From Date* </label>
                                  <input type="date" id="from_date" name="from_date"
                                      class="form-control form-control-sm" placeholder="yyyy-mm-dd"
                                      value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark"> To Date*</label>
                                  <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                      placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
                              </div>
                          </li>
                          <li>
                              <div class="col-md-12 d-flex justify-content-between">
                                 
                                  <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                      <i class="fas fa-redo mr-1"></i> Reset
                                  </button>
                                   <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
                                      <i class="fas fa-search mr-2"></i>Search
                                  </button>
                              </div>
                          </li>
                      </form>
                  </ul>
              </div>
        </div>


    </div>


    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Style</h5>
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
                                    <label class="small font-weight-bolder text-dark">Machine*</label>
                                    <select class="form-control form-control-sm" id="machine" name="machine" required>
                                           <option value="">Select Machine</option>
                                        @foreach($machines as $machine)
                                            <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                 <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Type*</label>
                                    <select class="form-control form-control-sm" id="type" name="type" required>
                                           <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Date*</label>
                                    <input type="date" name="date" id="date" class="form-control form-control-sm" required />
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">From*</label>
                                   <input type="datetime-local" name="fromtime" id="fromtime"  class="form-control form-control-sm"  value="{{ date('Y-m-d\TH:i') }}"  required/>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">To*</label>
                                    <input type="datetime-local" name="totime" id="totime"  class="form-control form-control-sm"  value="{{ date('Y-m-d\TH:i') }}" />
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
</main>
              
@endsection

@section('script')

<script>
$(document).ready(function(){

    $('#production_menu_link_opma').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailyprocess_opma').addClass('navbtnactive');

    function load_dt(machine, product, from_date, to_date){
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Machine Downtime  Information',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Machine Downtime Information', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'portrait', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Machine Downtime  Information',
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
                url: scripturl + "/Opma_Production/downtimelog_list.php",
                type: "POST",
                data:  {machine :machine, 
                            product: product,
                            from_date: from_date,
                            to_date: to_date},
            },
            columns: [
                { 
                    data: 'id', 
                    name: 'id'
                },
                { 
                    data: 'machine', 
                    name: 'machine'
                },
                { 
                    data: 'type', 
                    name: 'type'
                },
                { 
                    data: 'date', 
                    name: 'date'
                },
                { 
                    data: 'fromtime', 
                    name: 'fromtime'
                },
                { 
                    data: 'totime', 
                    name: 'totime'
                },
                {
                    data: 'id',
                    name: 'action',
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var buttons = '';

                        buttons += '<button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm  mr-1" type="submit" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                        buttons += '<button type="submit" name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Remove"><i class="far fa-trash-alt"></i></button>';

                        return buttons;
                    }
                }
            ],
            drawCallback: function(settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    load_dt('', '','', '');

    $('#formFilter').on('submit', function (e) {
        e.preventDefault();
        let machine = $('#filtermachine').val();
        let product = $('#product').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(machine, product, from_date, to_date);
        closeOffcanvasSmoothly();
    });
 
    $('#create_record').click(function () {
        $('.modal-title').text('Add Product');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formModal').modal('show');
        $('#sizes').val(null).trigger('change');
    });


    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        var action_url = '';


        if ($('#action').val() == 'Add') {
            action_url = "{{ route('opma_timechanginginsert') }}";
        }

        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('opma_timechangingupdate') }}";
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
            }
        });
    });

     $(document).on('click', '.edit', async function () {
         var r = await Otherconfirmation("You want to Edit this ? ");
         if (r == true) {
             var id = $(this).attr('id');
             $('#form_result').html('');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{!! route("opma_timechangingedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                 success: function (data) {
                     $('#date').val(data.result.date);
                     $('#type').val(data.result.type_id);
                     $('#machine').val(data.result.machine_id);
                     $('#fromtime').val(data.result.fromtime);
                     $('#totime').val(data.result.totime);

                     $('#hidden_id').val(id);
                     $('.modal-title').text('Edit Downtime Log');
                     $('#action_button').html('Edit');
                     $('#action').val('Edit');
                     $('#formModal').modal('show');
                 }
             })
         }
     });

    var user_id;

    $(document).on('click', '.delete', async function () {
        user_id = $(this).attr('id');
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                url: '{!! route("opma_timechangingdelete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
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
</script>

@endsection