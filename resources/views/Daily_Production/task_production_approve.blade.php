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
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee_f" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd" required>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn float-right" id="btn-filter" style="margin-top: 25px;"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button id="approve_att" class="btn btn-primary btn-sm">Approve All</button>
                            </div>
                        </div>

                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Employee ID</th>
                                    <th>Employee</th>
                                    <th>Task Amount</th>
                                    <th>Product Amount</th>
                                    <th class="d-none">Employee auto id</th>
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

       <div class="modal fade" id="approveconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Approve Production and Task Payments </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to Approve this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="approve_button" id="approve_button"
                        class="btn btn-warning px-3 btn-sm">Approve</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
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
    $('#taskapprove').addClass('navbtnactive');

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

        function load_dt(employee, from_date, to_date) {
            $('#dataTable').DataTable({
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                processing: true,
                serverSide: true,
                ajax: {
                    url:  "{{url('/productiontaskapprovegenerate')}}",
                    type: 'POST',
                    data: { 
                         _token: '{{ csrf_token() }}',
                        employee: employee, 
                        from_date: from_date,
                        to_date: to_date
                    },
                },
                columns: [
                     {
                        data: null,
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox"  class="row-checkbox selectCheck removeIt" data-id="' + row.emp_auto_id + '">';
                        }
                    },
                    { data: 'emp_id', name: 'emp_id' },
                    { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                    { data: 'task_total', name: 'task_total' },
                    { data: 'production_total', name: 'production_total' },
                    {
                        data: 'emp_auto_id',
                        name: 'emp_auto_id',
                        visible: false
                    }
                    
                ],
                "bDestroy": true,
                "order": [
                    [1, "desc"] // Order by date column
                ]
            });
        }

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let employee = $('#employee_f').val();
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt(employee, from_date, to_date);
        });


         var selectedRowIdsapprove = [];

    $('#approve_att').click(function () {
        selectedRowIdsapprove = [];
        $('#dataTable tbody .selectCheck:checked').each(function () {
            var rowData = $('#dataTable').DataTable().row($(this).closest('tr')).data();

            if (rowData) {
                selectedRowIdsapprove.push({
                    empid: rowData.emp_id, 
                    emp_name: rowData.emp_name_with_initial, 
                    task_total: rowData.task_total, 
                    production_total: rowData.production_total, 
                    emp_auto_id: rowData.emp_auto_id
                });
            }
        });

        if (selectedRowIdsapprove.length > 0) {
        console.log(selectedRowIdsapprove);
            $('#approveconfirmModal').modal('show');
        } else {
            
            alert('Select Rows to Final Approve!!!!');
        }
    });

    $('#approve_button').click(function () {
            $('#approve_button').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            var employee_f = $('#employee_f').val();
             var from_date = $('#from_date').val();
              var to_date = $('#to_date').val();

            $.ajax({
                url: '{!! route("approveproductiontask") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    dataarry: selectedRowIdsapprove,
                    employee_f: employee_f,
                    from_date: from_date,
                    to_date: to_date
                },
                success: function (data) {
                    $('#approve_button').html('Approve').prop('disabled', false);
                    setTimeout(function () {
                        $('#approveconfirmModal').modal('hide');
                        location.reload();
                    }, 500);

                    $('#selectAll').prop('checked', false);
                   
                }
            })
    });



    $('#selectAll').click(function (e) {
        $('#dataTable').closest('table').find('td input:checkbox').prop('checked', this.checked);
    });

});
</script>


@endsection