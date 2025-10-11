<?php $page_stitle = 'Report on Employees Resignation - Multi Offset HRM'; ?>
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
                    <span>Job Allocation Report</span>
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
                        <div class="center-block fix-width scroll-inner" id="emptablesection">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                <tr>
                                    <th>EMPLOYEE</th>
                                    <th>DATE</th>
                                    <th>LOCATION</th>
                                    <th>ON TIME</th>
                                    <th>OFF TIME</th>
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
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Location</label>
                                 <select name="company" id="company" class="form-control form-control-sm">
                                     <option value="">Please Select</option>
                                     @foreach ($locations as $location){
                                     <option value="{{$location->id}}">{{$location->location}}</option>
                                     }
                                     @endforeach
                                 </select>
                             </div>
                         </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">Employee</label>
                                  <select name="employee" id="employee_f" class="form-control form-control-sm">
                                      <option value="">Please Select</option>
                                      @foreach ($employees as $employee){
                                      <option value="{{$employee->id}}">{{$employee->emp_name_with_initial}}</option>
                                      }
                                      @endforeach
                                  </select>
                              </div>
                          </li>

                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark"> From Date* </label>
                                 <input type="date" id="from_date" name="from_date" class="form-control form-control-sm"
                                     placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
                             </div>
                         </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark"> To Date*</label>
                                 <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                     placeholder="yyyy-mm-dd" value="{{date('Y-m-d') }}" required>
                             </div>
                         </li>
                         <li>
                             <div class="col-md-12 d-flex justify-content-between">
                                
                                 <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                     <i class="fas fa-redo mr-1"></i> Reset
                                 </button>
                                  <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="btn-filter">
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
    $('#departmentvisereport').addClass('navbtnactive');
    $('#employee_f').select2({ width: '100%' });
    $('#company').select2({ width: '100%' });

load_dt('','', '','');

    function load_dt(location,from_date, to_date,employee_f) {
    $('#emptable').DataTable({
         "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Job Allocation Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Job Allocation Reports', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Job Allocation Reports',
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
            url: scripturl + "/rpt_joballocation_list.php",
            type: "POST",
            data : {'location': location,
                'from_date': from_date,
                'to_date': to_date,
                'employee_f': employee_f},
        },
        columns: [
                        {
                            "data": "employee_display",
                            "name": "employee_display",
                        },
                          {
                            "data": "attendance_date",
                            "name": "attendance_date",
                        },
                          {
                            "data": "location",
                            "name": "location",
                        },
                          {
                            "data": "on_time",
                            "name": "on_time",
                        },
                          {
                            "data": "off_time",
                            "name": "off_time",
                        }
        ],
        "bDestroy": true,
        "order": [[ 1, "desc" ]],
    });
}


    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let location = $('#company').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let employee_f = $('#employee_f').val();

        load_dt(location,from_date, to_date,employee_f);
         closeOffcanvasSmoothly();
    });



} );
</script>

@endsection