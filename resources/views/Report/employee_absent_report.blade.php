
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
                    <span>Employee Absent Report</span>
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

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="emptable">
                                <thead>
                                <tr>
                                    <th>EMP ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DATE</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
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

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                  aria-labelledby="offcanvasRightLabel">
                  <div class="offcanvas-header">
                      <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options
                      </h2>
                      <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                          <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                      </button>
                  </div>
                  <div class="offcanvas-body">
                      <ul class="list-unstyled">
                          <form class="form-horizontal" id="formFilter">
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Department</label>
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
                              <li>
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Date From*</label>
                                       <input type="date" name="selectdatefrom" id="selectdatefrom" class="form-control form-control-sm" required>
                                  </div>
                              </li>
                              <li class="div_date_range">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Date To*</label>
                                       <input type="date" name="selectdateto" id="selectdateto" class="form-control form-control-sm" required>
                                  </div>
                              </li>
                              <li>
                                  <div class="col-md-12 d-flex justify-content-between">

                                      <button type="button" class="btn btn-danger btn-sm filter-btn px-3"
                                          id="btn-reset">
                                          <i class="fas fa-redo mr-1"></i> Reset
                                      </button>
                                        <button type="submit" class="btn btn-primary btn-sm filter-btn px-3"
                                          id="btn-filter">
                                          <i class="fas fa-search mr-2"></i>Search
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
    $('#employeereportmaster').addClass('navbtnactive');

    $('#department').select2({ width: '100%' });

    showInitialMessage()

    function load_dt(department,selectdateto,selectdatefrom){

         $('#emptable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Employee Absent Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Employee Absent Reports', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Employee Absent Reports',
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
                "url": "{{url('/get_absent_employees')}}",
                "data": {'selectdatefrom':selectdatefrom,
                         'selectdateto':selectdateto,
                         'department':department
                },
            },
            columns: [
                { data: 'emp_id' },
                { data: 'employee_display' },
                 { data: 'date' },
                { data: 'departmentname' },
                { data: 'location' },
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
         e.preventDefault();
        let selectdatefrom = $('#selectdatefrom').val();
        let selectdateto = $('#selectdateto').val();
        let department = $('#department').val();

        load_dt(department,selectdateto,selectdatefrom);
        closeOffcanvasSmoothly();
    });

});

function showInitialMessage() {
        $('#emptable tbody').html(
            '<tr>' +
            '<td colspan="5" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }
</script>

@endsection