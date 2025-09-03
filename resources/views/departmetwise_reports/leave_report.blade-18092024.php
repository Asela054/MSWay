
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.reports_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            {{-- <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div> --}}
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
                                </select>
                            </div>
        
                            <div class="col-md-3 div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="table_outer">
                        <div class="daily_table table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="ot_report_dt">
                                <thead>
                                    <th>Department</th>
                                    <th>Total Leave</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                </div>
            </div>
        </div>

    </main>

    <div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">OT Breakdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" table-responsive">
                        <table class="table table-striped table-bordered table-sm small" id="empotview" style="width:100%">
                            <thead>
                                <th>Emp ID</th>
                                <th>Employee</th>
                                <th>Total Leave</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#departmentvisereport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });


            $('#formFilter').on('submit',function(e) {
                let department = $('#department').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                e.preventDefault();

                    $('#ot_report_dt').DataTable({
                    "lengthMenu": [
                        [10, 25, 50],
                        [10, 25, 50]
                    ],
                    dom: 'Blfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            title: 'Report on Department-wise Leave Total - Multi Offset HRM'
                        },
                        {
                            extend: 'pdf',
                            title: 'Report on Department-wise Leave Total - Multi Offset HRM',
                            customize: function (doc) {
                                doc.pageSize = 'LEGAL';
                                doc.pageOrientation = 'landscape';
                                doc.content[1].layout = 'auto';
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("departmentwise_generateleavereport") }}',
                        type: 'GET',
                        data: function (d) {
                            d.department = $('#department').val();
                            d.from_date = $('#from_date').val();
                            d.to_date = $('#to_date').val();
                        }
                    },
                    columns: [{
                            data: 'dept_name',
                            name: 'dept_name'
                        },
                        {
                            data: 'total_leave_count',
                            name: 'total_leave_count'
                        },
                        {
                            data: 'dept_id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return '<button id="' + data + '" class="btn btn-primary btn-sm btnEdit mr-1 view_more"><i class="fas fa-eye"></i></button>';
                            }
                        }
                    ]
                });

               
            });

            $(document).on('click','.view_more',function(e){
                
                var depid = $(this).attr('id');
                $('#empotview').DataTable({
                    "lengthMenu": [
                        [10, 25, 50],
                        [10, 25, 50]
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("departmentwise_gettotalleaveemployee") }}',
                        type: 'GET',
                        data: function (d) {
                            d.department = depid,
                            d.from_date = $('#from_date').val();
                            d.to_date = $('#to_date').val();
                        }
                    },
                    columns: [{
                            data: 'empid',
                            name: 'empid'
                        },
                        {
                            data: 'emp_name',
                            name: 'emp_name'
                        },
                        {
                            data: 'total_leave_count',
                            name: 'total_leave_count'
                        },
                    
                    ]
                });

                $('#view_more_modal').modal('show');
              
            });




        });
    </script>

@endsection

