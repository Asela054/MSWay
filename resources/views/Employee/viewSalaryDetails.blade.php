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
                    <div class="col-9">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Basic Salary</th>
                                    <th>BR 01</th>
                                    <th>BR 02</th>
                                    <th>Total Salary</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ number_format($employee->basic_salary, 2) }}</td>
                                    <td>{{ number_format($employee->br1, 2) }}</td>
                                    <td>{{ number_format($employee->br2, 2) }}</td>
                                    <td>{{ number_format($employee->total, 2) }}</td>
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
    $('#view_salary_link').addClass('navbtnactive');

    $('#dataTable').DataTable();
</script>
@endsection