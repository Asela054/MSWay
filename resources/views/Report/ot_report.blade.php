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
                    <span>O.T. Report</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Records</button><br><br>
                        </div>

                       <div class="col-md-12 table_outer">
                            <div class="daily_table table-responsive">
                                <table class="table table-striped table-bordered table-sm small" id="ot_report_dt">
                                    <thead>
                                    <tr id="dt_head">
                                        <th>EMP ID</th> 
                                        <th>EMPLOYEE</th>
                                        <th>DATE</th> 
                                        <th>FROM</th> 
                                        <th>TO</th>
                                        <th>HOURS</th>
                                        <th>DOUBLE HOURS</th>
                                        <th>IS HOLIDAY</th> 
                                        <th>LOCATION</th> 
                                        <th>DEPARTMENT</th> 
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                       
                        <div class="month_table table-responsive ">
                            <table class="table table-striped table-bordered table-sm small" id="ot_report_monthly_dt">
                                <thead>
                                <tr id="dt_head_month">
                                    <th>EMP ID</th> 
                                    <th>EMPLOYEE</th>
                                    <th>MONTH</th> 
                                    <th>WORK DAYS</th> 
                                    <th>LEAVE DAYS</th>
                                    <th>NO PAY DAYS</th>
                                    <th>O.T. HOURS</th> 
                                    <th>DOUBLE O.T. HOURS</th>
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
                    {{ csrf_field() }}
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
                                      <label class="small font-weight-bolder text-dark">Company</label>
                                      <select name="company" id="company" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Department</label>
                                      <select name="department" id="department" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Location</label>
                                      <select name="location" id="location" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Employee</label>
                                      <select name="employee" id="employee" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>

                              <li class="mb-3">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Type*</label>
                                      <select name="type" id="type" class="form-control form-control-sm">
                                          <option value="">Please Select Type</option>
                                          <option value="1">Month Wise</option>
                                          <option value="2">Date Range Wise</option>
                                      </select>
                                  </div>
                              </li>

                              <li class="div_date_range">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">From Date</label>
                                      <div class="input-group input-group-sm mb-3">
                                          <input type="date" id="from_date" name="from_date"
                                              class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                      </div>
                                  </div>
                              </li>
                              <li class="div_date_range">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">To Date </label>
                                      <div class="input-group input-group-sm mb-3">
                                          <input type="date" id="to_date" name="to_date"
                                              class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                      </div>
                                  </div>
                              </li>
                              <li id="div_month">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Month</label>
                                      <div class="input-group input-group-sm mb-3">
                                          <input type="month" id="month" name="selectedmonth"
                                              class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                      </div>
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
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeereportmaster').addClass('navbtnactive');


            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');

            showInitialMessage()

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
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

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
                        }
                    },
                    cache: true
                }
            });

            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val()
                        }
                    },
                    cache: true
                }
            });


            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_sel2")}}',
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

              $('.daily_table').css('display','none');
            $('.div_date_range').addClass('d-none');
            $('#div_month').addClass('d-none');

            $('#type').on('change', function () {
                let $type = $(this).val();
                if ($type == 1) {

                    $('.div_date_range').addClass('d-none');
                    $('#div_month').removeClass('d-none');

                } else {
                    $('#div_month').addClass('d-none');
                    $('.div_date_range').removeClass('d-none');
                }
            });

            load_dt('');
            function load_dt(department, employee, location, from_date, to_date, type, month){

                if(type == 2){

                    $('.month_table').css('display','none');
                    $('.daily_table').css('display','block');
                    $('#ot_report_dt').DataTable({
                            "destroy": true,
                            "processing": true,
                            "serverSide": true,
                            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'O.T. (Daily) Reports',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'O.T. (Daily)  Reports', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'O.T. (Daily)  Reports',
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
                            //"url": "{{url('/ot_report_list')}}",
                            "url": "{{url('/ot_approved_list')}}",
                            "data": {'department':department,
                                'employee':employee,
                                'location': location,
                                'from_date': from_date,
                                'to_date': to_date,
                                'type': type,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'employee_display' },
                            { data: 'date' },
                            { data: 'from' },
                            { data: 'to' },
                            { data: 'hours' },
                            { data: 'double_hours' },
                            { data: 'is_holiday' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }
                else if(type == 1){

                    $('.month_table').css('display','block');
                    $('.daily_table').css('display','none');

               

                    $('#ot_report_monthly_dt').DataTable({
                         "destroy": true,
                            "processing": true,
                            "serverSide": true,
                            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'O.T. (Monthly) Reports',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'O.T. (Monthly)  Reports', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'O.T. (Monthly)  Reports',
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
                            "url": "{{url('/ot_report_list_month')}}",
                            "data": {
                                'department':department,
                                'employee':employee,
                                'location': location,
                                'from_date': from_date,
                                'to_date': to_date,
                                'type': type,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'employee_display' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'leave_days' },
                            { data: 'no_pay_days' },
                            { data: 'normal_rate_otwork_hrs' },
                            { data: 'double_rate_otwork_hrs' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }


            }

            $('#formFilter').on('submit',function(e) {
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let type = $('#type').val();
                let month = $('#month').val();

                e.preventDefault();

                load_dt(department, employee, location, from_date, to_date, type, month);
                closeOffcanvasSmoothly();
            });

            $(document).on('click','.view_more',function(e){
                let emp_id = $(this).data('id');
                let date = $(this).data('date');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{route('ot_report_list_view_more')}}",
                    type: "POST",
                    dataType: "json",
                    data: {
                        'emp_id': emp_id,
                        'date': date,
                    },
                    success: function(res) {
                        //json
                        let ot_breakdown = res.ot_breakdown;
                        let att_data = res.att_data;

                        let ot_breakdown_html = '';
                        ot_breakdown_html += '<table class="table table-sm mb-3">';

                        ot_breakdown_html += '<tr>';
                        ot_breakdown_html += '<th>Employee</th>';
                        ot_breakdown_html += '<td> '+att_data.employee+' </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '</tr>';

                        ot_breakdown_html += '<tr>';
                        ot_breakdown_html += '<th>Check In Time</th>';
                        ot_breakdown_html += '<td> '+att_data.check_in_time+' </td>';
                        ot_breakdown_html += '<td> Check Out Time</td>';
                        ot_breakdown_html += '<td> '+att_data.check_out_time+' </td>';
                        ot_breakdown_html += '</tr>';

                        ot_breakdown_html += '</table>';


                        ot_breakdown_html += '<table class="table table-sm table-bordered table-striped">';
                        ot_breakdown_html += '<thead>';
                        ot_breakdown_html += '<tr>';
                        ot_breakdown_html += '<th>Date</th>';
                        ot_breakdown_html += '<th>Day</th>';
                        ot_breakdown_html += '<th>From</th>';
                        ot_breakdown_html += '<th>To</th>';
                        ot_breakdown_html += '<th>Normal Hours</th>';
                        ot_breakdown_html += '<th>Double Hours</th>';
                        ot_breakdown_html += '<th>Is Holiday</th>';

                        ot_breakdown_html += '</tr>';
                        ot_breakdown_html += '</thead>';

                        ot_breakdown_html += '<tbody>';

                        $.each(ot_breakdown, function(key, value) {
                            ot_breakdown_html += '<tr>';
                            ot_breakdown_html += '<td>'+value.date+'</td>';
                            ot_breakdown_html += '<td>'+value.day_name+'</td>';
                            ot_breakdown_html += '<td>'+value.from+'</td>';
                            ot_breakdown_html += '<td>'+value.to+'</td>';
                            ot_breakdown_html += '<td>'+value.hours+'</td>';
                            ot_breakdown_html += '<td>'+value.double_hours+'</td>';
                            ot_breakdown_html += '<td>'+value.is_holiday+'</td>';
                            ot_breakdown_html += '</tr>';
                        })

                        ot_breakdown_html += '<tr>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '<td> '+res.normal_rate_otwork_hrs+' </td>';
                        ot_breakdown_html += '<td> '+ res.double_rate_otwork_hrs +' </td>';
                        ot_breakdown_html += '<td> </td>';
                        ot_breakdown_html += '</tr>';


                        ot_breakdown_html += '</tbody>';
                        ot_breakdown_html += '</table>';

                        $('#view_more_modal').modal('show');
                        $('#view_more_modal .viewRes').html(ot_breakdown_html);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                    }
                });
            });

        });

        function showInitialMessage() {
        $('#ot_report_monthly_dt tbody').html(
            '<tr>' +
            '<td colspan="10" class="text-center py-5">' + // Changed colspan to 9 to match your columns
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

