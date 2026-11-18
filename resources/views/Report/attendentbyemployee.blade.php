<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
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
                    <span>Attendance Report</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid  mt-2 p-0 p-2">
            <div class="card ">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Options</button><br>
                    </div>
                    <div class="col-md-12">
                        <hr class="border-dark">
                    </div>
                    <div class="response">
                    </div>
                    {{ csrf_field() }}
                </div>
            </div>
            @include('layouts.filter_menu_offcanves')
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
                        department: department.val(),
                        location: location.val()
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


        let today = new Date().toISOString().split('T')[0];
        $('#from_date').val(today);
        $('#to_date').val(today);
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt('', '', '', from_date, to_date);

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
        closeOffcanvasSmoothly();

    });


    function load_dt(department, employee, location, from_date, to_date) {
        $('.response').html('');

        $.ajax({
            url: "{{ route('get_attendance_by_employee_data') }}",
            method: "POST",
            data: {
                department: department,
                employee: employee,
                location: location,
                from_date: from_date,
                to_date: to_date,
                _token: '{{csrf_token()}}'
            },
            success: function (res) {
                let html = '';

                html += `
            <div class="row mb-2">
                <div class="col-md-4">
                <button id="export_excel" class="btn btn-sm btn-success d-none"><i class="fas fa-file-excel mr-2"></i>Export To Excel</button>
                </div>
                <div class="col-md-4">
                <label class="mr-2">
                    <badge class="badge badge-pill " style="border: solid 1px black"> &nbsp; </badge> : Present
                </label>
                <label class="mr-2">
                    <badge class="badge badge-pill " style="background-color: #ffeaea"> &nbsp; </badge> : Absent
                </label>
                <label class="mr-2">
                    <badge class="badge badge-pill " style="background-color: rgb(247, 200, 200)"> &nbsp; </badge> : Incomplete
                </label>
                </div>
            </div>
            <table class="table table-sm table-hover" id="attendance_report_table">
                <thead>
                    <tr>
                        <th>EMP ID</th>
                        <th>NAME</th>
                        <th>DEPARTMENT</th>
                        <th>DESIGNATION</th>
                        <th>DATE</th>
                        <th>DATE TYPE</th>
                        <th>CHECK IN</th>
                        <th>CHECK OUT</th>
                        <th>WORK HOURS</th>
                        <th>LATE HOURS</th>
                        <th>LOCATION</th>
                    </tr>
                </thead>
                <tbody>
            `;

                // Function to convert 24-hour format to 12-hour format
                function convertTo12HourFormat(time) {
                    if (!time || time === '-') return time;
                    const [hour, minute] = time.split(':');
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    const formattedHour = hour % 12 || 12;
                    return `${formattedHour}:${minute} ${ampm}`;
                }

                res.data.forEach(function (datalist) {
                    datalist.attendanceinfo.forEach(function (emp_data) {
                        let tr = '<tr>';
                        if (emp_data.workhours === '00:00:00') {
                            tr = '<tr style="background-color: rgb(247, 200, 200)">';
                        } else if (emp_data.workhours === '-') {
                            tr = '<tr style="background-color: #ffeaea">';
                        }

                        const checkInTime = convertTo12HourFormat(emp_data.timestamp);
                        const checkOutTime = convertTo12HourFormat(emp_data.lasttimestamp);

                        html += tr;
                        html += `<td>${emp_data.emp_id}</td>`;
                        html += `<td>${emp_data.emp_name_with_initial} - ${emp_data.calling_name}</td>`;
                        html += `<td>${emp_data.dept_name}</td>`;
                        html += `<td>${emp_data.title}</td>`;
                        html += `<td>${emp_data.date}</td>`;
                        html += `<td>${emp_data.day_type}</td>`;
                        html += `<td>${checkInTime}</td>`;
                        html += `<td>${checkOutTime}</td>`;
                        html += `<td>${emp_data.workhours}</td>`;
                        html += `<td>${emp_data.late_hours}</td>`;
                        html += `<td>${emp_data.location}</td>`;
                        html += '</tr>';
                    });
                });
                html += `
                </tbody>
            </table>
            `;

                $('.response').html(html);

                // Check if DataTable already exists and destroy it first
                if ($.fn.DataTable.isDataTable('#attendance_report_table')) {
                    $('#attendance_report_table').DataTable().destroy();
                }

                // Initialize DataTable with client-side processing
                $('#attendance_report_table').DataTable({
                    "processing": false, 
                    "serverSide": false, 
                    "searching": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + 
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [
                        {
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Attendance Reports',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            className: 'btn btn-danger btn-sm', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            action: function () {
                                generatePDF();
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Attendance Reports',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    "order": [[0, "asc"]]
                });
            }
        });
    }
});


function filterLabel(selector, fallback) {
    const $el = $(selector);
    if (!$el.length) return fallback;
    const val = $el.val();
    if (!val || val === '' || val === 'all') return fallback;
    const text = $el.find('option:selected').text();
    return (text && text.trim() !== '') ? text.trim() : val;
}

// "hh:mm" -> minutes   (e.g. "01:35" => 95)
function hhmmToMinutes(str) {
    if (!str || str === '-') return 0;
    const parts = String(str).split(':');
    if (parts.length < 2) return 0;
    return (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
}

// minutes -> "hh:mm"
function minutesToHHMM(minutes) {
    minutes = parseInt(minutes, 10) || 0;
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
}

// ---------------------------------------------------------------
// 2) jsPDF export  (OT report eke format ekatama)
// ---------------------------------------------------------------
function generatePDF() {
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const department = filterLabel('#department', 'All');
    const employee = filterLabel('#employee', 'All');
    const location = filterLabel('#location', 'All');
    const currentDate = new Date().toLocaleDateString();

    // DataTable instance (search/filter apply karapu rows withrai)
    const table = $('#attendance_report_table').DataTable();
    const tableData = table.rows({ search: 'applied' }).data();

    // Landscape A4
    const doc = new jsPDF('l', 'mm', 'a4');
    const pageWidth = doc.internal.pageSize.getWidth();

    // Title
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('Attendance Report', pageWidth / 2, 15, { align: 'center' });

    // Filter info
    doc.setFontSize(7);
    doc.setFont('helvetica', 'normal');

    let yPos = 25;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
    doc.text(`Department: ${department}`, 90, yPos);
    doc.text(`Location: ${location}`, 150, yPos);
    doc.text(`Generated on: ${currentDate}`, pageWidth - 15, yPos, { align: 'right' });

    yPos += 5;
    doc.text(`Employee: ${employee}`, 15, yPos);

    // Separator line
    yPos += 5;
    doc.setLineWidth(0.3);
    doc.line(15, yPos, pageWidth - 15, yPos);
    yPos += 5;

    // No data
    if (!tableData || tableData.length === 0) {
        doc.setFontSize(10);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters', pageWidth / 2, yPos + 20, { align: 'center' });
        doc.save('Attendance_Report_No_Data.pdf');
        return;
    }

    const headers = [[
        'EMP ID', 'NAME', 'DEPARTMENT', 'DESIGNATION', 'DATE', 'DATE TYPE',
        'CHECK IN', 'CHECK OUT', 'WORK HOURS', 'LATE HOURS', 'LOCATION'
    ]];

    const body = [];
    let rowCount = 0;
    let presentCount = 0;
    let absentCount = 0;
    let incompleteCount = 0;
    let totalLateMinutes = 0;

    // DataTable rows (HTML table eken build karapu nisa row ekak = cell array ekak)
    tableData.each(function (value) {
        const workHours = String(value[8]).trim();
        const lateHours = String(value[9]).trim();

        if (workHours === '-') {
            absentCount++;
        } else if (workHours === '00:00:00') {
            incompleteCount++;
        } else {
            presentCount++;
        }

        totalLateMinutes += hhmmToMinutes(lateHours);
        rowCount++;

        body.push([
            value[0] || '',    // EMP ID
            value[1] || '',    // NAME
            value[2] || '',    // DEPARTMENT
            value[3] || '',    // DESIGNATION
            value[4] || '',    // DATE
            value[5] || '',    // DATE TYPE
            value[6] || '',    // CHECK IN
            value[7] || '',    // CHECK OUT
            workHours,         // WORK HOURS
            lateHours,         // LATE HOURS
            value[10] || ''    // LOCATION
        ]);
    });

    // Table
    const margin = 4;
    const tableWidth = pageWidth - (2 * margin);

    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 6,
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
            0: { cellWidth: 16, halign: 'center' },   // EMP ID
            1: { cellWidth: 52, halign: 'left' },     // NAME
            2: { cellWidth: 36, halign: 'left' },     // DEPARTMENT
            3: { cellWidth: 36, halign: 'left' },     // DESIGNATION
            4: { cellWidth: 22, halign: 'center' },   // DATE
            5: { cellWidth: 20, halign: 'center' },   // DATE TYPE
            6: { cellWidth: 20, halign: 'center' },   // CHECK IN
            7: { cellWidth: 20, halign: 'center' },   // CHECK OUT
            8: { cellWidth: 20, halign: 'center' },   // WORK HOURS
            9: { cellWidth: 18, halign: 'center' },   // LATE HOURS
            10: { cellWidth: 29, halign: 'left' }     // LOCATION
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        tableWidth: tableWidth,
        showHead: 'everyPage',
        didParseCell: function (data) {
            // Screen eke wage Absent / Incomplete rows colour karanna
            if (data.section === 'body' && data.row.raw) {
                const wh = String(data.row.raw[8]).trim();
                if (wh === '-') {
                    data.cell.styles.fillColor = [255, 234, 234];      // Absent
                } else if (wh === '00:00:00') {
                    data.cell.styles.fillColor = [247, 200, 200];      // Incomplete
                }
            }
        },
        willDrawPage: function (data) {
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(6);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(0, 0, 0);
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, pageWidth - margin, 10, { align: 'right' });

            if (data.pageNumber > 1) {
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text('Attendance Report (Continued)', pageWidth / 2, 18, { align: 'center' });
            }
        }
    });

    // Summary + footer (last page)
    const totalPages = doc.internal.getNumberOfPages();
    if (totalPages > 0) {
        doc.setPage(totalPages);
        const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;

        if (finalY < doc.internal.pageSize.getHeight() - 40 && rowCount > 0) {
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(7);
            doc.setFont('helvetica', 'bold');
            doc.text('Report Summary:', margin, finalY);

            doc.setFont('helvetica', 'normal');
            doc.setFontSize(6);
            let summaryY = finalY + 7;

            doc.text(`Total Records: ${rowCount}`, margin, summaryY);
            doc.text(`Present: ${presentCount}`, margin + 60, summaryY);
            doc.text(`Absent: ${absentCount}`, margin + 130, summaryY);

            summaryY += 7;
            doc.text(`Incomplete: ${incompleteCount}`, margin, summaryY);
            doc.text(`Total Late Hours: ${minutesToHHMM(totalLateMinutes)}`, margin + 60, summaryY);
        }

        doc.setFontSize(6);
        doc.setTextColor(0, 0, 0);
        const generatedBy = $('#emp_name').val() || 'System User';
        const companyName = $('#company_name').val() || 'Company Name';
        const footerY = doc.internal.pageSize.getHeight() - 10;

        if (footerY > 20) {
            doc.text(`Generated by: ${generatedBy}`, margin, footerY);
            doc.text(`Date: ${currentDate}`, pageWidth / 2, footerY, { align: 'center' });
            doc.text(companyName, pageWidth - margin, footerY, { align: 'right' });
        }
    }

    // Save
    const safeFrom = String(fromDate).replace(/[^a-zA-Z0-9]/g, '_');
    const safeTo = String(toDate).replace(/[^a-zA-Z0-9]/g, '_');
    doc.save(`Attendance_Report_${safeFrom}_to_${safeTo}.pdf`);
}

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

@endsection

