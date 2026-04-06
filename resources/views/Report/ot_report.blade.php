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
                                    {{-- <th>LOCATION</th> --}}
                                    <th>DEPARTMENT</th>
                                    <th>MONTH</th> 
                                    <th>WORK DAYS</th> 
                                    <th>LEAVE DAYS</th>
                                    <th>NO PAY DAYS</th>
                                    <th>O.T. HOURS</th> 
                                    <th>O.T. HOURS RATE</th> 
                                    <th>O.T. HOURS AMOUNT</th> 
                                    <th>DOUBLE O.T. HOURS</th>
                                    <th>DOUBLE O.T. HOURS RATE</th>
                                    <th>DOUBLE O.T. HOURS AMOUNT</th>
                                    <th>TOTAL</th>
                                
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
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    className: 'btn btn-danger btn-sm',
                                    action: function (e, dt, node, config) {
                                        generatePDF();
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
                            { data: 'dept_name' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'leave_days' },
                            { data: 'no_pay_days' },
                            { data: 'normal_rate_otwork_hrs' },
                            { data: 'normal_rate_otwork_hrsrate' },
                            { data: 'normal_rate_otwork_amount' },
                            { data: 'double_rate_otwork_hrs' },
                            { data: 'double_rate_otwork_hrsrate' },
                            { data: 'double_rate_otwork_amount' },
                            { data: 'otwork_amount_total' }
                            
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
            '<td colspan="15" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }


        // Custom PDF generation function

 function generatePDF() {
    // Get current filter values for PDF header
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const department = $('#department').val() || 'All';
    const employee = $('#employee').val() || 'All';
    const location = $('#location').val() || 'All';
    const month = $('#month').val() || 'Not specified';
    const currentDate = new Date().toLocaleDateString();
    
    // Get DataTable instance
    const table = $('#ot_report_monthly_dt').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();
    
    // Initialize PDF in landscape mode for better fit (more columns)
    const doc = new jsPDF('l', 'mm', 'a4');
    
    // Add report title
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('O.T. (Monthly) Report', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });
    
    // Add filter information
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');
    
    // FIXED: Put date range and month on the same line
    let yPos = 25;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
    doc.text(`Month: ${month}`, 90, yPos);  // Position month to the right on same line
    doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });
    
    // Add a line separator
    yPos += 10;  // Reduced from 27 to 10 for tighter spacing
    doc.setLineWidth(0.3);
    doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
    yPos += 5;
    
    // Rest of your code remains the same...
    // Prepare table data and calculate totals
    const headers = [[
        'EMP ID', 'EMPLOYEE', 'DEPARTMENT', 'MONTH', 
        'WORK DAYS', 'LEAVE DAYS', 'NO PAY DAYS', 'O.T. HOURS', 
        'O.T. RATE', 'O.T. AMOUNT', 'D.O.T. HOURS', 'D.O.T. RATE', 
        'D.O.T. AMOUNT', 'TOTAL'
    ]];
    
    const body = [];
    let totalWorkDays = 0;
    let totalLeaveDays = 0;
    let totalNoPayDays = 0;
    let totalOtHours = 0;
    let totalOtAmount = 0;
    let totalDoubleOtHours = 0;
    let totalDoubleOtAmount = 0;
    let totalOverallAmount = 0;
    let rowCount = 0;
    
    // Check if there's data
    if (!tableData || tableData.length === 0) {
        doc.setFontSize(10);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters', doc.internal.pageSize.getWidth() / 2, yPos + 20, { align: 'center' });
        doc.save('OT_Monthly_Report_No_Data.pdf');
        return;
    }
    
    // Get all data from filtered rows
    tableData.each(function(value, index) {
        const workDays = parseFloat(value.work_days) || 0;
        const leaveDays = parseFloat(value.leave_days) || 0;
        const noPayDays = parseFloat(value.no_pay_days) || 0;
        const otHours = parseFloat(value.normal_rate_otwork_hrs) || 0;
        const otRate = parseFloat(value.normal_rate_otwork_hrsrate) || 0;
        const otAmount = parseFloat(value.normal_rate_otwork_amount) || 0;
        const doubleOtHours = parseFloat(value.double_rate_otwork_hrs) || 0;
        const doubleOtRate = parseFloat(value.double_rate_otwork_hrsrate) || 0;
        const doubleOtAmount = parseFloat(value.double_rate_otwork_amount) || 0;
        const totalAmount = parseFloat(value.otwork_amount_total) || 0;
        
        totalWorkDays += workDays;
        totalLeaveDays += leaveDays;
        totalNoPayDays += noPayDays;
        totalOtHours += otHours;
        totalOtAmount += otAmount;
        totalDoubleOtHours += doubleOtHours;
        totalDoubleOtAmount += doubleOtAmount;
        totalOverallAmount += totalAmount;
        rowCount++;
        
        // Format month value (if it's in date format)
        let monthDisplay = value.month || '';
        if (monthDisplay && monthDisplay.includes('-')) {
            const dateParts = monthDisplay.split('-');
            if (dateParts.length === 2) {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const monthNum = parseInt(dateParts[1]);
                monthDisplay = `${monthNames[monthNum-1]} ${dateParts[0]}`;
            }
        }
        
        const row = [
            value.emp_id || '',
            value.employee_display || '',
            value.dept_name || '',
            monthDisplay,
            workDays !== 0 ? workDays.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            leaveDays !== 0 ? leaveDays.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            noPayDays !== 0 ? noPayDays.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            otHours !== 0 ? otHours.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            otRate !== 0 ? otRate.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            otAmount !== 0 ? otAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            doubleOtHours !== 0 ? doubleOtHours.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            doubleOtRate !== 0 ? doubleOtRate.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            doubleOtAmount !== 0 ? doubleOtAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00',
            totalAmount !== 0 ? totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '0.00'
        ];
        body.push(row);
    });
    
    // Add footer row with totals
    if (body.length > 0) {
        const formattedTotalWorkDays = totalWorkDays.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalLeaveDays = totalLeaveDays.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalNoPayDays = totalNoPayDays.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalOtHours = totalOtHours.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalOtAmount = totalOtAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalDoubleOtHours = totalDoubleOtHours.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalDoubleOtAmount = totalDoubleOtAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const formattedTotalOverallAmount = totalOverallAmount.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        body.push([
            '', '', '', '', 'TOTALS:',
            formattedTotalWorkDays,
            formattedTotalLeaveDays,
            formattedTotalNoPayDays,
            formattedTotalOtHours,
            '',
            formattedTotalOtAmount,
            formattedTotalDoubleOtHours,
            '',
            formattedTotalDoubleOtAmount,
            formattedTotalOverallAmount
        ]);
    }
    
    // Calculate table width
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 4;
    const tableWidth = pageWidth - (2 * margin);
    
    // Generate table using autoTable
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 5,
            cellPadding: 1.5,
            overflow: 'linebreak',
            textAlign: 'left'
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 6
        },
        columnStyles: {
            0: { cellWidth: 18, halign: 'center' },   // EMP ID
            1: { cellWidth: 28, halign: 'left' },     // EMPLOYEE
            2: { cellWidth: 22, halign: 'left' },     // LOCATION
            3: { cellWidth: 25, halign: 'left' },     // DEPARTMENT
            4: { cellWidth: 18, halign: 'center' },   // MONTH
            5: { cellWidth: 18, halign: 'right' },    // WORK DAYS
            6: { cellWidth: 18, halign: 'right' },    // LEAVE DAYS
            7: { cellWidth: 18, halign: 'right' },    // NO PAY DAYS
            8: { cellWidth: 18, halign: 'right' },    // O.T. HOURS
            9: { cellWidth: 18, halign: 'right' },    // O.T. RATE
            10: { cellWidth: 22, halign: 'right' },   // O.T. AMOUNT
            11: { cellWidth: 18, halign: 'right' },   // D.O.T. HOURS
            12: { cellWidth: 18, halign: 'right' },   // D.O.T. RATE
            13: { cellWidth: 22, halign: 'right' },   // D.O.T. AMOUNT
            14: { cellWidth: 22, halign: 'right' }    // TOTAL
        },
        bodyStyles: {
            textAlign: 'left',
            fontSize: 5
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        tableWidth: tableWidth,
        showHead: 'everyPage',
        didParseCell: function(data) {
            // Style the totals row
            if (data.row.index === body.length - 1 && body.length > 0) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [220, 220, 220];
                data.cell.styles.textColor = [0, 0, 0];
                data.cell.styles.fontSize = 6;
                // Right align the "TOTALS:" text
                if (data.column.index === 4) {
                    data.cell.styles.halign = 'right';
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        },
        willDrawPage: function(data) {
            // Add company name and page number on each page
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(6);
            doc.setFont('helvetica', 'normal');
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - margin, 10, { align: 'right' });
            
            // Add report title on subsequent pages
            if (data.pageNumber > 1) {
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text('O.T. (Monthly) Report (Continued)', doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
            }
        }
    });
    
    // Add summary on last page
    const totalPages = doc.internal.getNumberOfPages();
    if (totalPages > 0) {
        doc.setPage(totalPages);
        const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;
        
        // Only add summary if there's enough space and data exists
        if (finalY < doc.internal.pageSize.getHeight() - 50 && rowCount > 0) {
            doc.setFontSize(7);
            doc.setFont('helvetica', 'bold');
            doc.text('Report Summary:', margin, finalY);
            
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(6);
            let summaryY = finalY + 7;
            
            doc.text(`Total Employees: ${rowCount}`, margin, summaryY);
            doc.text(`Total Work Days: ${totalWorkDays.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 60, summaryY);
            doc.text(`Total Leave Days: ${totalLeaveDays.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 130, summaryY);
            
            summaryY += 7;
            doc.text(`Total No Pay Days: ${totalNoPayDays.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, summaryY);
            doc.text(`Total O.T. Hours: ${totalOtHours.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 60, summaryY);
            doc.text(`Total Double O.T. Hours: ${totalDoubleOtHours.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 130, summaryY);
            
            summaryY += 7;
            doc.text(`Total O.T. Amount: ${totalOtAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin, summaryY);
            doc.text(`Total Double O.T. Amount: ${totalDoubleOtAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 60, summaryY);
            doc.text(`Grand Total: ${totalOverallAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`, margin + 130, summaryY);
        }
        
        // Add footer on last page
        doc.setFontSize(6);
        const generatedBy = $('#emp_name').val() || 'System User';
        const companyName = $('#company_name').val() || 'Company Name';
        const footerY = doc.internal.pageSize.getHeight() - 10;
        
        if (footerY > 20) { // Ensure footer doesn't overlap content
            doc.text(`Generated by: ${generatedBy}`, margin, footerY);
            doc.text(`Date: ${currentDate}`, doc.internal.pageSize.getWidth() / 2, footerY, { align: 'center' });
            doc.text(companyName, doc.internal.pageSize.getWidth() - margin, footerY, { align: 'right' });
        }
    }
    
    // Save the PDF
    const safeMonth = month.replace(/[^a-zA-Z0-9]/g, '_') || 'Report';
    const fileName = `OT_Monthly_Report_${safeMonth}_${currentDate.replace(/[^0-9]/g, '')}.pdf`;
    doc.save(fileName);
}
    </script>

@endsection

