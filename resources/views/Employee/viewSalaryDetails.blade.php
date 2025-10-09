@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                        <span>Salary</span>
                    </h1>
                </div>
            </div>
    </div>    
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-lg-9 col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>BASIC SALARY</th>
                                        <th>BR 01</th>
                                        <th>BR 02</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ number_format($employee->basic_salary, 2) }}</td>
                                        <td>{{ number_format($employee->br1, 2) }}</td>
                                        <td>{{ number_format($employee->br2, 2) }}</td>
                                        <td><strong>{{ number_format($employee->total, 2) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @include('layouts.employeeRightBar')
                </div>
            </div>
        </div>        
    </div>
</main>
@endsection

@section('script')
<script>
    $('#employee_menu_link').addClass('active');
    $('#employee_menu_link_icon').addClass('active');
    $('#employeeinformation').addClass('navbtnactive');
    $('#view_salary_link').addClass('active');

    $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Salary  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Salary Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'portrait', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Salary  Information',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-print mr-2"></i> Print',
                customize: function(win) {
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                },
            },
        ],
        "order": [
            [0, "desc"]
        ],
        }
        );
</script>
@endsection