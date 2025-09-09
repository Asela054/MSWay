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
                                <label class="small font-weight-bold text-dark">Product</label>
                                <select name="product" id="product" class="form-control form-control-sm">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->productname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Task</label>
                                <select name="task" id="task" class="form-control form-control-sm">
                                    <option value="">Select Task</option>
                                    @foreach ($tasks as $task)
                                        <option value="{{ $task->id }}">{{ $task->taskname }}</option>
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
                                    <th>Employee ID</th>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Task</th>
                                    <th>Product</th>
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
    $('#dailytask').addClass('navbtnactive');

     let employee_f = $('#employee_f');

       employee_f.select2({
            placeholder: 'Select...',
            width: '100%',
            allowClear: true,
            ajax: {
                url: '{{url("employee_list_task")}}',
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

        function load_dt(employee, task, product, from_date, to_date) {
            $('#dataTable').DataTable({
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                processing: true,
                serverSide: true,
                ajax: {
                    url: scripturl + '/employee_task_product_list.php',
                    type: 'POST',
                    data: { 
                        employee: employee, 
                        task: task,
                        product: product,
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                columns: [
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name', name: 'emp_name' },
                    { data: 'date', name: 'date' },
                    { data: 'task', name: 'task' },
                    { data: 'product', name: 'product' }
                ],
                "bDestroy": true,
                "order": [
                    [2, "desc"] // Order by date column
                ]
            });
        }

        load_dt('', '', '', '', '');

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let employee = $('#employee_f').val();
            let task = $('#task').val();
            let product = $('#product').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(employee, task, from_date, to_date);
        });


});
</script>


@endsection