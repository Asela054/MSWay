@extends('layouts.app')

@section('content')
<main> 
  <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Employee Resign Report</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
            <div class="card-body p-0 p-2">
                <form class="form-horizontal" id="formFilter">
                    <div class="form-row mb-1">
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Date Range: From - To</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                       placeholder="yyyy-mm-dd">
                                <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i>Filter</button>
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
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                   <th>EMPLOYEE</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
                                    <th>DATE OF BIRTH</th>
                                    <th>MOBILE NO</th>
                                    <th>NIC</th>
                                    <th>GENDER</th>
                                    <th>PERMANENT ADDRESS</th>
                                    <th>JOB CATEGORY</th>
                                    <th>PERMANENT DATE</th>
                                    <th>RESIGNATION DATE</th>
                                    <th>WORK DAYS COUNT</th>
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
$(document).ready(function() {

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeedetailsreport').addClass('navbtnactive');

    $('#department').select2({
    width: '100%'
    });

    load_dt('','','');

    function load_dt(department,from_date,to_date) {
    $('#emptable').DataTable({
        "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Employee Resign Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Employee Resign Reports', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Employee Reports',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
        ajax: {
            "url": "{{url('/get_resign_employees')}}",
            "data": {'department': department,
                    'from_date': from_date,
                    'to_date': to_date
            },
        },
        columns: [
            { data: 'id' },
            { data: 'employee_display' },
            { data: 'location' },
            { data: 'department_name' },
            { data: 'emp_birthday' },
            { data: 'emp_mobile' },
            { data: 'emp_national_id' },
            { data: 'emp_gender' },
            { data: 'emp_address' },
            { data: 'title' },
            { data: 'emp_permanent_date' },
            { data: 'resignation_date' },
            {
                data: null,
                render: function (data, type, row) {
                    var permanentDate = new Date(row.emp_permanent_date);
                    var resignationDate = new Date(row.resignation_date);
                    var timeDifference = resignationDate - permanentDate;
                    var workingDays = Math.ceil(timeDifference / (1000 * 3600 * 24));
                    if (isNaN(workingDays) || workingDays < 0) {
                        return 'N/A';
                    }

                    return workingDays;
                }
            }
        ],
        "bDestroy": true,
        "order": [[ 0, "desc" ]],
    });
}


    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department,from_date,to_date);
    });



} );
</script>

@endsection