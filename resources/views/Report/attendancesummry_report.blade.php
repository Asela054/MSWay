@extends('layouts.app')

@section('content')

<main>
       <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                   @include('layouts.reports_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-check"></i></div>
                         <span>Employee Attendance Summary Report</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
                <div class="row">
                    <div class="col-md-12">

                         <div class="row align-items-center mb-4">
                                <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div>
                                 <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-12 text-right">
                                    <button id="approve_att" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                                </div>
                            </div>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100" id="attendtable">
                                <thead>
                                <tr>
                                    <th>EMPID</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>MONTH</th>
                                    <th>DEPARTMENT</th>
                                    <th>ATTENDANCE DAYS</th>
                                    <th>WORKING DAYS</th>
                                    <th>WORKING HOURS</th>
                                    <th>NORMAL OT</th>
                                    <th>DOUBLE OT</th>
                                    <th>LEAVE DAYS</th>
                                    <th>NO PAY DAYS</th>
                                    <th>PAY CUT</th>
                                </tr>
                                </thead>
                                <tbody class="response"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                aria-labelledby="offcanvasRightLabel">
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
                                   <label class="small font-weight-bolder text-dark">Company</label>
                                    <select name="company" id="company" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                     <label class="small font-weight-bolder text-dark">Department</label>
                                    <select name="department" id="department" class="form-control form-control-sm" required>
                                    </select>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Month</label>
                                     <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                   <label class="small font-weight-bolder text-dark">Close Date</label>
                                 <input type="date" id="closedate" name="closedate" class="form-control form-control-sm" required>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    
                                    <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
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

   showInitialMessage();

   let currentFilters = {};

    function load_dt(company,department, month, closedate){
        
        $('#attendtable').DataTable({
           "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Attendance Summary Report',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    className: 'btn btn-danger btn-sm', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    action: function (e, dt, node, config) {
                        generatePDF();
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Attendance Summary Report',
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
            ajax: {
                "url": "{{url('/Rptattendance_list')}}",
                "data": {'company':company, 'department':department, 'month':month, 'closedate':closedate},
            },

            columns: [
                { data: 'uid', name: 'at1.uid' },
                { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                { data: 'date', name: 'at1.date' },
                { data: 'dept_name', name: 'departments.name' },
                { data: 'timestamp_days', name: 'timestamp_days' },
                 { data: 'work_days', name: 'work_days' },
                { data: 'working_hours', name: 'working_hours' },
                { data: 'normal_ot', name: 'normal_ot' },
                { data: 'double_ot', name: 'double_ot' },
                { data: 'leave_days', name: 'leave_days' },
                { data: 'no_pay_days', name: 'no_pay_days' },
                { data: 'pay_cut', name: 'pay_cut' },
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let company = $('#company').val();
        let month = $('#month').val();
        let closedate = $('#closedate').val();

        load_dt(company, department, month, closedate);
        closeOffcanvasSmoothly();
    });

        // Offcanvas toggle functionality - UPDATED
        $('[data-toggle="offcanvas"]').on('click', function () {
            var target = $(this).data('target');
            $(target).addClass('show');
            $('body').addClass('offcanvas-open');

            // Add backdrop
            $('<div class="offcanvas-backdrop fade show"></div>').appendTo('body');
        });

        // Close offcanvas when clicking on backdrop
        $(document).on('click', '.offcanvas-backdrop', function () {
            closeOffcanvasSmoothly();
        });
        // Close offcanvas when clicking on close button
        $('[data-dismiss="offcanvas"]').on('click', function () {
            closeOffcanvasSmoothly();
        });

        $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
            $('#employee').val(null).trigger('change');
            $('#location').val(null).trigger('change');
        });
              
        $('#month').on('change', function() {
            updateClosingDateConstraints();
        });
});


    function showInitialMessage() {
        $('.response').html(
            '<tr>' +
            '<td colspan="12" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }

    function closeOffcanvasSmoothly(offcanvasId = '#offcanvasRight') {
                const offcanvas = $(offcanvasId);
                const backdrop = $('.offcanvas-backdrop');

                // Add hiding class to trigger reverse animation
                offcanvas.addClass('hiding');
                backdrop.addClass('fading');

                // Remove elements after animation completes
                setTimeout(() => {
                    offcanvas.removeClass('show hiding');
                    backdrop.remove();
                    $('body').removeClass('offcanvas-open');
                }, 900); // Match this with your CSS transition duration
    }

    function updateClosingDateConstraints() {
        const monthInput = $('#month').val();
        const closeDateInput = $('#closedate');

        if (monthInput) {
            // Extract year and month from the month input
            const [year, month] = monthInput.split('-');

            // Calculate first and last day of the selected month
            const firstDay = `${year}-${month}-01`;
            const lastDay = new Date(year, month, 0).getDate(); // Last day of the month
            const lastDate = `${year}-${month}-${lastDay}`;

            // Set min and max attributes to restrict dates to the selected month
            closeDateInput.attr('min', firstDay);
            closeDateInput.attr('max', lastDate);

            // Update placeholder to show the valid date range
            closeDateInput.attr('placeholder', `${firstDay} to ${lastDate}`);

            // If current close date is outside the selected month, clear it
            const currentCloseDate = closeDateInput.val();
            if (currentCloseDate && (currentCloseDate < firstDay || currentCloseDate > lastDate)) {
                closeDateInput.val('');
            }

            // Enable the close date input
            closeDateInput.prop('disabled', false);

            // Auto-set close date to last day of month if not set
            if (!closeDateInput.val()) {
                closeDateInput.val(lastDate);
            }
        } else {
            // If no month selected, disable and clear close date
            closeDateInput.val('');
            closeDateInput.prop('disabled', true);
            closeDateInput.removeAttr('min');
            closeDateInput.removeAttr('max');
            closeDateInput.attr('placeholder', 'Select month first');
        }
    }

    function generatePDF() {
        const table = $('#attendtable').DataTable();
        const params = $.extend({}, table.ajax.params(), { start: 0, length: -1 });

        // Filter values for PDF header (ajax params walin)
        const month = params.month || 'Not specified';
        const closedate = params.closedate || 'Not specified';
        const department = $('#department option:selected').text().trim() || 'All';
        const company = $('#company option:selected').text().trim() || 'All';
        const currentDate = new Date().toLocaleDateString();

        $.ajax({
            url: table.ajax.url(),
            type: 'GET',
            data: params,
            success: function (response) {
                buildAttendancePDF(response.data || [], {
                    month: month,
                    closedate: closedate,
                    department: department,
                    company: company,
                    currentDate: currentDate
                });
            },
            error: function () {
                alert('Failed to load data for PDF export.');
            }
        });
    }

    function buildAttendancePDF(tableData, info) {
        const currentDate = info.currentDate;

        // Landscape A4
        const doc = new jsPDF('l', 'mm', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 2;

        // Title
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Employee Attendance Summary Report', pageWidth / 2, 15, { align: 'center' });

        // Filter info
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');

        let yPos = 25;
        doc.text('Month: ' + info.month + '   |   Close Date: ' + info.closedate, 15, yPos);
        doc.text('Generated on: ' + currentDate, pageWidth - 15, yPos, { align: 'right' });

        yPos += 5;
        doc.text('Company: ' + info.company + '   |   Department: ' + info.department, 15, yPos);

        // Separator line
        yPos += 8;
        doc.setLineWidth(0.3);
        doc.line(15, yPos, pageWidth - 15, yPos);
        yPos += 5;

        // Table headers
        const headers = [[
            'EMPID', 'EMPLOYEE NAME', 'MONTH', 'DEPARTMENT', 'ATTENDANCE DAYS',
            'WORKING DAYS', 'WORKING HOURS', 'NORMAL OT', 'DOUBLE OT',
            'LEAVE DAYS', 'NO PAY DAYS', 'PAY CUT'
        ]];

        // No data case
        if (!tableData || tableData.length === 0) {
            doc.setFontSize(8);
            doc.setTextColor(255, 0, 0);
            doc.text('No data available for the selected filters', pageWidth / 2, yPos + 20, { align: 'center' });
            doc.save('Attendance_Report_No_Data.pdf');
            return;
        }

        const body = [];
        let rowCount = 0;

        tableData.forEach(function (value) {
            body.push([
                value.uid || value.emp_id || '',
                value.emp_name_with_initial || '',
                value.date || '',
                value.dept_name || '',
                value.timestamp_days != null ? value.timestamp_days : 0,
                value.work_days != null ? value.work_days : 0,
                value.working_hours != null ? value.working_hours : 0,
                value.normal_ot != null ? value.normal_ot : 0,
                value.double_ot != null ? value.double_ot : 0,
                value.leave_days != null ? value.leave_days : 0,
                value.no_pay_days != null ? value.no_pay_days : 0,
                value.pay_cut != null ? value.pay_cut : 0
            ]);
            rowCount++;
        });

        doc.autoTable({
            startY: yPos,
            head: headers,
            body: body,
            theme: 'grid',
            styles: {
                fontSize: 6,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 6,
                cellPadding: 3
            },
            columnStyles: {
                0: { cellWidth: 15, halign: 'center' },   // EMPID
                1: { cellWidth: 50, halign: 'left' },     // EMPLOYEE NAME
                2: { cellWidth: 18, halign: 'center' },   // MONTH
                3: { cellWidth: 40, halign: 'left' },     // DEPARTMENT
                4: { cellWidth: 20, halign: 'center' },   // ATTENDANCE DAYS
                5: { cellWidth: 20, halign: 'center' },   // WORKING DAYS
                6: { cellWidth: 22, halign: 'center' },   // WORKING HOURS
                7: { cellWidth: 20, halign: 'center' },   // NORMAL OT
                8: { cellWidth: 20, halign: 'center' },   // DOUBLE OT
                9: { cellWidth: 20, halign: 'center' },   // LEAVE DAYS
                10: { cellWidth: 20, halign: 'center' },  // NO PAY DAYS
                11: { cellWidth: 20, halign: 'center' }   // PAY CUT
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            margin: { left: margin, right: margin },
            pageBreak: 'auto',
            tableWidth: 'auto',
            showHead: 'everyPage',
            willDrawPage: function (data) {
                // Company name + page number on every page
                const companyName = $('#company_name').val() || 'Company Name';
                doc.setFontSize(7);
                doc.setFont('helvetica', 'normal');
                doc.text(companyName, margin, 10);
                doc.text('Page ' + data.pageNumber, pageWidth - margin, 10, { align: 'right' });

                if (data.pageNumber > 1) {
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Employee Attendance Summary Report (Continued)', pageWidth / 2, 18, { align: 'center' });
                }
            }
        });

        // Summary + footer on last page
        const totalPages = doc.internal.getNumberOfPages();
        if (totalPages > 0) {
            doc.setPage(totalPages);
            const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;

            if (finalY < doc.internal.pageSize.getHeight() - 40 && rowCount > 0) {
                doc.setFontSize(8);
                doc.setFont('helvetica', 'bold');
                doc.text('Report Summary:', margin, finalY);

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.text('Total Employees: ' + rowCount, margin, finalY + 7);
            }

            doc.setFontSize(6);
            const generatedBy = $('#emp_name').val() || 'System User';
            const companyName = $('#company_name').val() || 'Company Name';
            const footerY = doc.internal.pageSize.getHeight() - 10;

            if (footerY > 20) {
                doc.text('Generated by: ' + generatedBy, margin, footerY);
                doc.text('Date: ' + currentDate, pageWidth / 2, footerY, { align: 'center' });
                doc.text(companyName, pageWidth - margin, footerY, { align: 'right' });
            }
        }

        // Save
        const safeDept = (info.department || 'Report').replace(/[^a-zA-Z0-9]/g, '_');
        const safeMonth = (info.month || '').replace(/[^0-9]/g, '');
        doc.save('Attendance_Report_' + safeDept + '_' + safeMonth + '.pdf');
    }
</script>

@endsection