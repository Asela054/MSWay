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
                                    <th>Salary Component</th>
                                    <th>Pay Frequency</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Show Direct Deposit Details </th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{$employee->emp_sal_grade}}</td>
                                    <td>{{$employee->emp_sal_transaction_type}}</td>
                                    <td>{{$employee->emp_sal_currency}}</td>
                                    <td>{{$employee->emp_sal_basic_salary}}</td>
                                    <td>{{$employee->emp_sal_account}}</td>
                                    <td class="text-right">
                                        @can('employee-edit')
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pencil-alt"></i></button>
                                            <button class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <hr class="border-dark">
                        <form>
                            <div class="form-row">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Select File</label>
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Comment</label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                @can('employee-edit')
                                    <button type="submit" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-save"></i>&nbsp;Save</button>
                                @endcan
                            </div>
                        </form>
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
