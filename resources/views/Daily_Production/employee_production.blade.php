@extends('layouts.app')

@section('content')

<main> 
    <div class="page-header shadow">
        <div class="container-fluid">
             @include('layouts.production&task_nav_bar')
           
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Machine</label>
                                <select name="machine" id="machine" class="form-control form-control-sm">
                                    <option value="">Select Machine</option>
                                    @foreach ($machines as $machine)
                                        <option value="{{ $machine->id }}">{{ $machine->machine }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Product</label>
                                <select name="product" id="product" class="form-control form-control-sm">
                                    <option value="">Select Product</option>
                                     @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->productname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Employee</th>
                                    <th>Machine</th>
                                    <th>Product</th>
                                    <th>Date</th>
                                    <th>Amount</th>
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
              
@endsection


@section('script')

<script>
$(document).ready(function(){

    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailyprocess').addClass('navbtnactive');

     let employee_f = $('#employee_f');

       employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_production")}}',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
       });

         function load_dt(machine, employee, product, from_date, to_date){
                $('#dataTable').DataTable({
                    lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    processing: true,
                    serverSide: true,
                    ajax: {
                         url: scripturl + '/employee_production_list.php',
                         type: 'POST',
                         data : 
                            {machine :machine, 
                            employee :employee, 
                            product: product,
                            from_date: from_date,
                            to_date: to_date},
                    },
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'emp_name', name: 'emp_name' },
                        { data: 'machine', name: 'machine' },
                        { data: 'product', name: 'product' },
                        { data: 'date', name: 'date' },
                        { data: 'amount', name: 'amount' }
                    ],
                    "bDestroy": true,
                    "order": [
                        [0, "desc"]
                    ]
                });
        }

        load_dt('', '', '', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let machine = $('#machine').val();
            let employee = $('#employee_f').val();
            let product = $('#product').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(machine, employee, product, from_date, to_date);
        });


});
</script>


@endsection