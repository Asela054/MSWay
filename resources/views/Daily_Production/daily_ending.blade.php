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
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Machine</th>
                                    <th>Product</th>
                                    <th>Date</th>
                                    <th>Production Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td>1</td>
                                    <td>Machine 01</td>
                                    <td>Product 01</td>
                                    <td>2025-07-30</td>
                                    <td>Processing</td>
                                    <td class="text-right">
                                        
                                            <button name="edit" id="edit" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        
                                            <button type="submit" name="delete" id="delete" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                       
                                    </td>
                                </tr>
                                
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
                                    
                                        <div class="row col-6">
                                            <div class="col-12">
                                                <label class="small font-weight-bold text-dark">Product Type:</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="product_type" id="semi" value="Semi Completed">
                                                    <label class="form-check-label small font-weight-bold text-dark" for="semi" required>Semi Completed</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="product_type" id="full" value="Full Completed">
                                                    <label class="form-check-label small font-weight-bold text-dark" for="full" required >Full Completed</label>
                                                </div>
                                            </div>
                                        </div>
                                     <div class="col-6">
                                        <label class="small font-weight-bold text-dark">Processed Quntity</label>
                                        <input type="number" step="any" name="quntity" id="quntity" class="form-control form-control-sm" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Note</label>
                                        <input type="text" name="desription" id="desription" class="form-control form-control-sm"/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
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
                            <span id="form_result"></span>
                                <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}	
                                <div class="row">
                                    <div class="col-12">
                                        <label class="small font-weight-bold text-dark">Cancel description</label>
                                        <input type="text" name="cancel_desription" id="cancel_desription" class="form-control form-control-sm" required/>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group mt-3">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
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

    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#dailyprocess').addClass('navbtnactive');

    $('#dataTable').DataTable();



    $('#edit').click(function () {
        $('.modal-title').text('Finish Production');
        $('#action_button').val('Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formModal').modal('show');
    });

    $('#delete').click(function () {
        $('#cancelformModal').modal('show');
    });


           

            $('#formTitle').on('submit', function (event) {
                event.preventDefault();

                var tbody = $("#allocationtbl tbody");
                if (tbody.children().length > 0) {
                    var jsonObj = [];
                    $("#allocationtbl tbody tr").each(function () {
                        var item = {};

                        var empId = $(this).data('empid');
                        item = {
                            'col_1': empId,
                        };

                        jsonObj.push(item);
                    });


                    var machine = $('#machine').val();
                    var product = $('#product').val();
                    var production_date = $('#production_date').val();

                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            tableData: jsonObj,
                            departmentline: departmentline,
                            linedate: linedate
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

                }
            });

});
</script>


@endsection