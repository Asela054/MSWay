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
                        <span>Daily Process Ending</span>
                    </h1>
                </div>
            </div>
        </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>MACHINE</th>
                                    <th>PRODUCT</th>
                                    <th>DATE</th>
                                    <th>PRODUCTION STATUS</th>
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
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Finish Production</h5>
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
                                <div class="row">
                                    
                                        <div class="row col-sm-12 col-md-6">
                                            <div class="col-12">
                                                <label class="small font-weight-bolder text-dark">Product Type:*</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="product_type" id="semi" value="Semi Completed">
                                                    <label class="form-check-label small font-weight-bolder text-dark" for="semi" required>Semi Product</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="product_type" id="full" value="Full Completed">
                                                    <label class="form-check-label small font-weight-bolder text-dark" for="full" required >Full Product</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="product_type" id="both" value="Semi & Full">
                                                    <label class="form-check-label small font-weight-bolder text-dark" for="both" required >Semi & Full</label>
                                                </div>
                                            </div>
                                        </div>
                                     <div class="col-sm-12 col-md-3 quantity-field hidden-field" id="semiQtyField">
                                        <label class="small font-weight-bolder text-dark">Semi Quntity*</label>
                                        <input type="number" step="any" name="semiquntity" id="semiquntity" class="form-control form-control-sm" required />
                                    </div>
                                     <div class="col-sm-12 col-md-3  quantity-field hidden-field" id="fullQtyField">
                                        <label class="small font-weight-bolder text-dark">Full Quntity*</label>
                                        <input type="number" step="any" name="fullquntity" id="fullquntity" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Note</label>
                                        <input type="text" name="desription" id="desription" class="form-control form-control-sm"/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                    <input type="hidden" name="hidden_id" id="hidden_id" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <!-- Modal Area Start -->
  <div class="modal fade" id="cancelformModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Cancel Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result_cancel"></span>
                                <form method="post" id="cancelform" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <label class="small font-weight-bolder text-dark">Cancel description*</label>
                                        <input type="text" name="cancel_desription" id="cancel_desription" class="form-control form-control-sm" required/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="cancel_id" id="cancel_id" />
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


     // DataTable initialization
    $('#dataTable').DataTable({
       "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Production  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Production Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Production  Information',
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
            url: scripturl + "/production_ending_list.php",
            type: "POST",
            data: {},
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'machine',
                name: 'machine'
            },
            {
                data: 'productname',
                name: 'productname'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
            data: 'production_status',
            name: 'production_status',
            render: function(data, type, row) {
                var statusText = '';
                var statusClass = '';
                
                if (data == 1) {
                    statusText = 'Processing';
                    statusClass = 'text-warning';
                } else if (data == 2) {
                    statusText = 'Completed';
                    statusClass = 'text-success';
                } else{
                    statusText = 'Cancelled';
                    statusClass = 'text-danger'; 
                }
                
                return '<span class="' + statusClass + '">' + statusText + '</span>';
            }
        },
            {
                data: 'production_status',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var buttons = '';

                    if(data == 1){
                        buttons += ' <button name="edit" id="'+row.id+'" class="edit btn btn-success btn-sm" type="button" title="Finish Production" data-toggle="tooltip"><i class="fas fa-check-circle"></i></button>';
                    }
                    if(data != 3){
                        buttons += ' <button name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" type="button" title="Cancel Production" data-toggle="tooltip"><i class="fas fa-times-circle"></i></button>';
                    }
                     
                     return buttons;
                }
            },
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });


    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('.modal-title').text('Finish Production');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#hidden_id').val(id);
        $('#formModal').modal('show');
    });

    $('#formTitle').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url:  '{!! route("productionendingfinish") !!}',
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

     $(document).on('click', '.delete',async function () {
         var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            var id = $(this).attr('id');
            $('#form_result_cancel').html('');
            $('#cancel_id').val(id);
            $('#cancelformModal').modal('show');
        }
    });
    
    $('#cancelform').on('submit', function (event) {
             event.preventDefault();
            $.ajax({
                url:  '{!! route("productionendingcancel") !!}',
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


     $('input[name="product_type"]').change(function () {
         const selectedValue = $(this).val();
         handleProductTypeChange(selectedValue);
     });

     function handleProductTypeChange(productType) {
         // Hide all quantity fields first using Bootstrap classes
         $('#semiQtyField, #fullQtyField').addClass('d-none').removeClass('d-block');

         // Clear all quantity inputs
         $('#semiquntity, #fullquntity').val('').removeAttr('required');

         // Show relevant fields based on selection
         switch (productType) {
             case 'Semi Completed':
                 $('#semiQtyField').removeClass('d-none').addClass('d-block');
                 $('#semiquntity').attr('required', 'required');
                 break;
             case 'Full Completed':
                 $('#fullQtyField').removeClass('d-none').addClass('d-block');
                 $('#fullquntity').attr('required', 'required');
                 break;
             case 'Semi & Full':
                 $('#semiQtyField, #fullQtyField').removeClass('d-none').addClass('d-block');
                 $('#semiquntity, #fullquntity').attr('required', 'required');
                 break;
         }
     }

     $('#semiQtyField, #fullQtyField').addClass('d-none');

});

  
       
</script>


@endsection