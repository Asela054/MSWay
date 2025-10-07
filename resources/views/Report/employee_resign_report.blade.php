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
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                class="fas fa-filter mr-1"></i> Filter
                            Records</button><br><br>
                    </div>
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

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
             <div class="offcanvas-header">
                 <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                 <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                     <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                 </button>
             </div>
             <div class="offcanvas-body">
                 <ul class="list-unstyled">
                     <form class="form-horizontal" id="formFilter">
                         <li class="mb-3">
                            <div class="col-md-12">
                             <label class="small font-weight-bolder text-dark">Department*</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
                            </select>
                            </div>
                         </li>
                          <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date* </label>
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}"
                                           required>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date*</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}" required>
                                </div>
                            </li>
                         <li>
                             <div class="col-md-12 d-flex justify-content-between">
                                 <button type="submit" class="btn btn-primary btn-md filter-btn px-3" id="btn-filter">
                                     <i class="fas fa-search mr-2"></i>Search
                                 </button>
                                 <button type="button" class="btn btn-danger btn-md filter-btn px-3" id="btn-reset">
                                     <i class="fas fa-redo mr-1"></i> Reset
                                 </button>
                             </div>
                         </li>
                     </form>
                 </ul>
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
         closeOffcanvasSmoothly();
    });


      $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#department').val(null).trigger('change');
             });
} );
</script>

@endsection