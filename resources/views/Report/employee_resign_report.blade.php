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
                                    <th>EMPLOYEE ID</th>
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
                         <li class="mb-2">
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
                          <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date* </label>
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}"
                                           required>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date*</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}" required>
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

$(document).ready(function () {

    $('#report_menu_link').addClass('active');
    $('#report_menu_link_icon').addClass('active');
    $('#employeedetailsreport').addClass('navbtnactive');

    $('#department').select2({
        width: '100%'
    });

    load_dt('', '', '');

    function load_dt(department, from_date, to_date) {
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
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    action: function (e, dt, node, config) {
                        generatePDF();
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Reports',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function (win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            ajax: {
                "url": "{{url('/get_resign_employees')}}",
                "data": {
                    'department': department,
                    'from_date': from_date,
                    'to_date': to_date
                },
            },
            columns: [{
                    data: 'emp_id'
                },
                {
                    data: 'employee_display'
                },
                {
                    data: 'location'
                },
                {
                    data: 'department_name'
                },
                {
                    data: 'emp_birthday'
                },
                {
                    data: 'emp_mobile'
                },
                {
                    data: 'emp_national_id'
                },
                {
                    data: 'emp_gender'
                },
                {
                    data: 'emp_address'
                },
                {
                    data: 'title'
                },
                {
                    data: 'emp_permanent_date'
                },
                {
                    data: 'resignation_date'
                },
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
            "order": [
                [0, "desc"]
            ],
        });
    }


    $('#formFilter').on('submit', function (e) {
        e.preventDefault();
        let department = $('#department').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, from_date, to_date);
        closeOffcanvasSmoothly();
    });


    $('#btn-reset').on('click', function () {
        $('#formFilter')[0].reset();
        $('#department').val(null).trigger('change');

        load_dt('', '', '');
         closeOffcanvasSmoothly();
    });
});

function generatePDF() {
    // Get current filter values for PDF header
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const department = $('#department').val() || 'All';
    const employee = $('#employee').val() || 'All';
    const location = $('#location').val() || 'All';
    const currentDate = new Date().toLocaleDateString();
    
    // Get DataTable instance
    const table = $('#emptable').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();
    
    // Initialize PDF in landscape mode
    const doc = new jsPDF('l', 'mm', 'a4');
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 10;
    
    // Add report title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Employee Resign Report', pageWidth / 2, 15, { align: 'center' });
    
    // Add filter information
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    
    let yPos = 25;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, margin, yPos);
    doc.text(`Department: ${department}`, margin + 80, yPos);
    doc.text(`Location: ${location}`, margin + 160, yPos);
    doc.text(`Generated on: ${currentDate}`, pageWidth - margin, yPos, { align: 'right' });
    
    if (employee !== 'All') {
        yPos += 5;
        doc.text(`Employee: ${employee}`, margin, yPos);
    }
    
    // Add line separator
    yPos += 8;
    doc.setLineWidth(0.3);
    doc.line(margin, yPos, pageWidth - margin, yPos);
    yPos += 5;
    
    // Prepare data with better text formatting
    const body = [];
    let rowCount = 0;
    let totalWorkDaysCount = 0;
    
    if (!tableData || tableData.length === 0) {
        doc.setFontSize(10);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters', pageWidth / 2, yPos + 20, { align: 'center' });
        doc.save('Employee_Resign_Report_No_Data.pdf');
        return;
    }
    
    tableData.each(function(value, index) {
        // Format dates
        let dob = value.emp_birthday || '';
        if (dob && dob !== '0000-00-00') {
            const parts = dob.split('-');
            if (parts.length === 3) dob = `${parts[2]}/${parts[1]}/${parts[0]}`;
        } else {
            dob = '';
        }
        
        let permanentDate = value.emp_permanent_date || '';
        if (permanentDate && permanentDate !== '0000-00-00') {
            const parts = permanentDate.split('-');
            if (parts.length === 3) permanentDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
        } else {
            permanentDate = '';
        }
        
        let resignationDate = value.resignation_date || '';
        if (resignationDate && resignationDate !== '0000-00-00' && resignationDate !== 'N/A') {
            const parts = resignationDate.split('-');
            if (parts.length === 3) resignationDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
        } else {
            resignationDate = 'N/A';
        }
        
        // Calculate work days
        let workDaysCount = 'N/A';
        if (value.emp_permanent_date && value.resignation_date && 
            value.emp_permanent_date !== '0000-00-00' && 
            value.resignation_date !== '0000-00-00' &&
            value.resignation_date !== 'N/A') {
            const permDate = new Date(value.emp_permanent_date);
            const resignDate = new Date(value.resignation_date);
            const days = Math.ceil((resignDate - permDate) / (1000 * 3600 * 24));
            if (!isNaN(days) && days >= 0) {
                workDaysCount = days.toString();
                totalWorkDaysCount += days;
            }
        }
        
        // Clean and truncate long text for better display
        let employeeName = (value.employee_display || '').substring(0, 35);
        let address = (value.emp_address || '').substring(0, 45);
        let jobCategory = (value.title || '').substring(0, 25);
        let locationName = (value.location || '').substring(0, 20);
        let departmentName = (value.department_name || '').substring(0, 20);
        
        body.push([
            value.emp_id || '',
            employeeName,
            locationName,
            departmentName,
            dob,
            value.emp_mobile || '',
            value.emp_national_id || '',
            value.emp_gender || '',
            address,
            jobCategory,
            permanentDate,
            resignationDate,
            workDaysCount
        ]);
        rowCount++;
    });
    
    // Define headers with clean names
    const headers = [[
        'EMPLOYEE ID', 'EMPLOYEE', 'LOCATION', 'DEPT', 'DOB',
        'MOBILE', 'NIC', 'GENDER', 'ADDRESS',
        'JOB CATEGORY', 'PERM DATE', 'RESIGN DATE', 'DAYS'
    ]];
    
    // Use autoTable with optimized settings
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 5,
            cellPadding: { top: 2, bottom: 2, left: 2, right: 2 },
            overflow: 'ellipsize',
            valign: 'middle',
            lineColor: [0, 0, 0],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 6,
            cellPadding: { top: 3, bottom: 3, left: 2, right: 2 }
        },
        columnStyles: {
            0: { cellWidth: 12, halign: 'center' },   // EMPLOYEE ID
            1: { cellWidth: 'auto', halign: 'left' },  // EMPLOYEE - auto size
            2: { cellWidth: 20, halign: 'left' },      // LOCATION
            3: { cellWidth: 20, halign: 'left' },      // DEPT
            4: { cellWidth: 14, halign: 'center' },    // DOB
            5: { cellWidth: 16, halign: 'center' },    // MOBILE
            6: { cellWidth: 18, halign: 'center' },    // NIC
            7: { cellWidth: 12, halign: 'center' },    // GENDER
            8: { cellWidth: 'auto', halign: 'left' },  // ADDRESS - auto size
            9: { cellWidth: 22, halign: 'left' },      // JOB CATEGORY
            10: { cellWidth: 16, halign: 'center' },   // PERM DATE
            11: { cellWidth: 16, halign: 'center' },   // RESIGN DATE
            12: { cellWidth: 12, halign: 'right' }     // DAYS
        },
        bodyStyles: {
            textColor: [0, 0, 0],
            fontSize: 7
        },
        alternateRowStyles: {
            fillColor: [248, 248, 248]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        showHead: 'everyPage',
        didParseCell: function(data) {
            // Style N/A values
            if (data.cell.text === 'N/A') {
                data.cell.styles.textColor = [255, 0, 0];
                data.cell.styles.fontStyle = 'italic';
            }
            // Right align days column
            if (data.column.index === 12 && data.cell.text !== 'N/A') {
                data.cell.styles.halign = 'right';
                data.cell.styles.fontStyle = 'bold';
            }
        },
        willDrawPage: function(data) {
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, pageWidth - margin, 10, { align: 'right' });
            
            if (data.pageNumber > 1) {
                doc.setFontSize(10);
                doc.setFont('helvetica', 'bold');
                doc.text('Employee Resign Report (Continued)', pageWidth / 2, 18, { align: 'center' });
            }
        },
        didDrawPage: function(data) {
            // Add bottom border on each page
            doc.setDrawColor(200, 200, 200);
            doc.setLineWidth(0.2);
            doc.line(margin, doc.internal.pageSize.getHeight() - 12, pageWidth - margin, doc.internal.pageSize.getHeight() - 12);
        }
    });
    
    // Add summary on last page
    const totalPages = doc.internal.getNumberOfPages();
    doc.setPage(totalPages);
    let finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 12 : 150;
    
    // Check if we need a new page for summary
    if (finalY > doc.internal.pageSize.getHeight() - 60) {
        doc.addPage();
        finalY = 20;
    }
    
    // Footer on last page
    const generatedBy = $('#emp_name').val() || 'System User';
    const companyName = $('#company_name').val() || 'Company Name';
    const footerY = doc.internal.pageSize.getHeight() - 8;
    
    doc.setFontSize(6);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(100, 100, 100);
    doc.text(`Generated by: ${generatedBy}`, margin, footerY);
    doc.text(`Date: ${currentDate}`, pageWidth / 2, footerY, { align: 'center' });
    doc.text(companyName, pageWidth - margin, footerY, { align: 'right' });
    
    // Save the PDF
    const safeDept = department.replace(/[^a-zA-Z0-9]/g, '_') || 'Report';
    const fileName = `Employee_Resign_Report_${safeDept}_${currentDate.replace(/[^0-9]/g, '')}.pdf`;
    doc.save(fileName);
}
</script>

@endsection