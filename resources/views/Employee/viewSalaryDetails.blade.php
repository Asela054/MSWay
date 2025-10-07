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
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
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
    $('#view_salary_link').addClass('active');

    $('#dataTable').DataTable();
</script>
@endsection