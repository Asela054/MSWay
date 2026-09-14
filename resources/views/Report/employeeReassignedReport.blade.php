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
                    <span>Employee Reassigned Report</span>
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
                                class="fas fa-filter mr-1"></i> Filter Records</button><br><br>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner" >
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                    <tr>
                                        <th>EMPLOYEE ID</th>
                                        <th>EMPLOYEE</th>
                                        <th>NIC</th>
                                        <th>LOCATION</th>
                                        <th>DEPARTMENT</th>
                                        <th>JOB CATEGORY</th>
                                        <th>JOIN DATE</th>
                                        <th>RESIGNED DATE</th>
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
                                 <label class="small font-weight-bolder text-dark">Company</label>
                                 <select name="company" id="company" class="form-control form-control-sm" required>
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
    $('#employeedetailsreport').addClass('navbtnactive');

    let company    = $('#company');
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

    load_dt('', '');

    function load_dt(company, department) {
        $('#emptable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "buttons": [
                {
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Reassigned Report',
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
                    title: 'Employee Reassigned Report',
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
                url: scripturl + "/rpt_employee_reassigned.php",
                type: "POST",
                data: { 'company': company, 'department': department },
            },
            columns: [
                { data: 'emp_id' },
                { data: 'employee_display' },
                { data: 'emp_national_id' },
                { data: 'location' },
                { data: 'dept_name' },
                { data: 'category' },
                {
                    data: 'emp_join_date',
                    render: function(data) {
                        if (!data || data === '0000-00-00') return '';
                        var parts = data.split('-');
                        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : data;
                    }
                },
                {
                    data: 'resignation_date',
                    render: function(data) {
                        if (!data || data === '0000-00-00') return '';
                        var parts = data.split('-');
                        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : data;
                    }
                },
            ],
            "bDestroy": true,
            "order": [[ 2, "asc" ], [ 0, "desc" ]],

            "drawCallback": function(settings) {
                var api       = this.api();
                var rows      = api.rows({ page: 'current' }).nodes();
                var lastNic   = null;

                api.rows({ page: 'current' }).data().each(function(row, i) {
                    var nic = row.emp_national_id || '';
                    if (nic !== lastNic) {
                        $(rows[i]).before(
                            '<tr class="reassign-group-header">' +
                                '<td colspan="8">' +
                                    '<i class="fas fa-id-card mr-2 text-primary"></i>' +
                                    '<strong>NIC: ' + nic + '</strong>' +
                                    ' &nbsp;—&nbsp; ' + row.employee_display +
                                '</td>' +
                            '</tr>'
                        );
                        lastNic = nic;
                    }
                });
            }
        });
    }

    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        let dept = $('#department').val();
        let company = $('#company').val();
        load_dt(company, dept);
        closeOffcanvasSmoothly();
    });

    $('#btn-reset').on('click', function () {
        $('#formFilter')[0].reset();
        $('#company').val(null).trigger('change');
        $('#department').val(null).trigger('change');
        load_dt('', '');
    });

});

// generatePDF – matches the Employee Report PDF structure:
//   reads data directly from the DataTable (current filtered rows).
function generatePDF() {
    const department  = $('#department').val() || 'All';
    const currentDate = new Date().toLocaleDateString();

    // Get DataTable instance and its currently visible/filtered data
    const table     = $('#emptable').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();

    // Initialize PDF in landscape mode
    const doc       = new jsPDF('l', 'mm', 'a4');
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin    = 10;

    // Title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Employee Reassigned Report', pageWidth / 2, 15, { align: 'center' });

    // Sub-header: filter info
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    let yPos = 25;
    doc.text('Department: ' + department, margin, yPos);
    doc.text('Generated on: ' + currentDate, pageWidth - margin, yPos, { align: 'right' });

    // Separator line
    yPos += 8;
    doc.setLineWidth(0.3);
    doc.line(margin, yPos, pageWidth - margin, yPos);
    yPos += 5;

    // No data guard
    if (!tableData || tableData.length === 0) {
        doc.setFontSize(8);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters.', pageWidth / 2, yPos + 20, { align: 'center' });
        doc.save('Employee_Reassigned_Report_No_Data.pdf');
        return;
    }

    const headers = [[
        'EMP ID', 'EMPLOYEE', 'NIC', 'LOCATION', 'DEPARTMENT', 'JOB CATEGORY', 'JOIN DATE', 'RESIGNED DATE'
    ]];

    const body = [];
    let rowCount   = 0;
    let lastNicPdf = null;

    function formatDate(raw) {
        if (!raw || raw === '0000-00-00') return '';
        var parts = raw.split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : raw;
    }

    tableData.each(function(value) {
        var nic = value.emp_national_id || '';

        // Insert a group-header row whenever the NIC changes
        if (nic !== lastNicPdf) {
            body.push([{
                content: 'NIC: ' + nic + '   \u2014   ' + (value.employee_display || ''),
                colSpan: 8,
                styles: {
                    fillColor: [41, 128, 185],
                    textColor: 255,
                    fontStyle: 'bold',
                    fontSize: 7,
                    halign: 'left',
                    cellPadding: { top: 3, bottom: 3, left: 4, right: 4 }
                }
            }]);
            lastNicPdf = nic;
        }

        body.push([
            value.emp_id                          || '',
            (value.employee_display               || '').substring(0, 35),
            nic,
            (value.location                       || '').substring(0, 20),
            (value.dept_name                      || '').substring(0, 22),
            (value.category                       || '').substring(0, 25),
            formatDate(value.emp_join_date),
            formatDate(value.resignation_date)
        ]);
        rowCount++;
    });

    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 7,
            cellPadding: { top: 2, bottom: 2, left: 2, right: 2 },
            overflow: 'ellipsize',
            valign: 'middle',
            lineColor: [180, 180, 180],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [22, 82, 130],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 7,
            cellPadding: { top: 3, bottom: 3, left: 2, right: 2 }
        },
        columnStyles: {
            0: { cellWidth: 14,     halign: 'center' }, // EMP ID
            1: { cellWidth: 'auto', halign: 'left'   }, // EMPLOYEE
            2: { cellWidth: 22,     halign: 'center' }, // NIC
            3: { cellWidth: 24,     halign: 'left'   }, // LOCATION
            4: { cellWidth: 28,     halign: 'left'   }, // DEPARTMENT
            5: { cellWidth: 28,     halign: 'left'   }, // JOB CATEGORY
            6: { cellWidth: 20,     halign: 'center' }, // JOIN DATE
            7: { cellWidth: 20,     halign: 'center' }, // RESIGNED DATE
        },
        bodyStyles: {
            textColor: [30, 30, 30],
            fontSize: 7
        },
        alternateRowStyles: {
            fillColor: [245, 248, 252]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        showHead: 'everyPage',
        willDrawPage: function(data) {
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(100, 100, 100);
            doc.text(companyName, margin, 10);
            doc.text('Page ' + data.pageNumber, pageWidth - margin, 10, { align: 'right' });

            if (data.pageNumber > 1) {
                doc.setFontSize(10);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 0);
                doc.text('Employee Reassigned Report (Continued)', pageWidth / 2, 18, { align: 'center' });
            }
        },
        didDrawPage: function() {
            doc.setDrawColor(200, 200, 200);
            doc.setLineWidth(0.2);
            doc.line(margin, doc.internal.pageSize.getHeight() - 12,
                     pageWidth - margin, doc.internal.pageSize.getHeight() - 12);
        }
    });

    // Summary on the last page
    const totalPages = doc.internal.getNumberOfPages();
    doc.setPage(totalPages);
    let finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 12 : 150;

    if (finalY > doc.internal.pageSize.getHeight() - 50) {
        doc.addPage();
        finalY = 20;
    }

    if (rowCount > 0) {
        doc.setFontSize(8);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(0, 0, 0);
        doc.text('Report Summary:', margin, finalY);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7);
        doc.text('Total Records: ' + rowCount, margin, finalY + 7);
    }

    // Footer
    const generatedBy = $('#emp_name').val() || 'System User';
    const companyName = $('#company_name').val() || 'Company Name';
    const footerY     = doc.internal.pageSize.getHeight() - 8;

    doc.setFontSize(6);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(120, 120, 120);
    doc.text('Generated by: ' + generatedBy, margin, footerY);
    doc.text('Date: ' + currentDate, pageWidth / 2, footerY, { align: 'center' });
    doc.text(companyName, pageWidth - margin, footerY, { align: 'right' });

    const safeDept = department.replace(/[^a-zA-Z0-9]/g, '_') || 'All';
    doc.save('Employee_Reassigned_Report_' + safeDept + '_' + currentDate.replace(/[^0-9]/g, '') + '.pdf');
}
</script>

<style>
/* NIC group header rows in the DataTable */
#emptable tr.reassign-group-header td {
    background-color: #1652a0 !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 5px 10px;
    border-top: 2px solid #0d3d80;
}
#emptable tr.reassign-group-header td i {
    color: #a8c8ff;
}
</style>

@endsection