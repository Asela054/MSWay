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
                    <span>Employee Report</span>
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
                        <div class="center-block fix-width scroll-inner" >
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                <tr>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>LOCATION</th>
                                    <th>DEPARTMENT</th>
                                    <th>DATE OF BIRTH</th>
                                    <th>MOBILE NO</th>
                                    <th>TELEPHONE</th>
                                    <th>NIC</th>
                                    <th>GENDER</th>
                                    <th>EMAIL</th>
                                    <th>PERMANENT ADDRESS</th>
                                    <th>TEMPORARY ADDRESS</th>
                                    <th>JOB CATEGORY</th>
                                    <th>JOB STATUS</th>
                                    <th>PERMANENT DATE</th>

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
                                 <select name="company" id="company" class="form-control form-control-sm">
                                 </select>
                             </div>
                         </li>
                         <li class="mb-2">
                             <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department</label>
                                 <select name="department" id="department" class="form-control form-control-sm"
                                     required>
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

    load_dt('');
    function load_dt(department){
        $('#emptable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Employee Reports',
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
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
            ajax: {
                  url: scripturl + "/rpt_employee.php",
                  type: "POST",
                  data: {'department':department},
            },
            columns: [
                { data: 'emp_id' },
                { data: 'employee_display' },
                { data: 'location' },
                { data: 'dept_name' },
                { data: 'emp_birthday' },
                { data: 'emp_mobile' },
                { data: 'emp_work_telephone' },
                { data: 'emp_national_id' },
                { data: 'emp_gender' },
                { data: 'emp_email' },
                { data: 'emp_address' },
                { data: 'emp_addressT' },
                { data: 'title' },
                { data: 'e_status' },
                { data: 'emp_permanent_date' }
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    }

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();

        load_dt(department);
        closeOffcanvasSmoothly();
    });


     $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
             });

} );

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
        
        // Initialize PDF in landscape mode for better fit
        const doc = new jsPDF('l', 'mm', 'a4');
        
        // Add report title
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Employee Report', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });
        
        // Add filter information
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        
        let yPos = 25;
        doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
        doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });
        
        // Add employee filter on next line if specified
        if (employee !== 'All') {
            yPos += 5;
            doc.text(`Employee: ${employee}`, 15, yPos);
        }
        
        // Add a line separator
        yPos += 8;
        doc.setLineWidth(0.3);
        doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
        yPos += 5;
        
        // Prepare table headers
        const headers = [[
            'EMPLOYEE ID', 'EMPLOYEE', 'LOCATION', 'DEPARTMENT', 'DOB',
            'MOBILE NO', 'TELEPHONE', 'NIC', 'GENDER', 'EMAIL',
            'ADDRESS', 'TEMP ADDRESS', 'CATEGORY',
            'STATUS', 'PERMANENT'
        ]];
        
        const body = [];
        let rowCount = 0;
        
        // Check if there's data
        if (!tableData || tableData.length === 0) {
            doc.setFontSize(8);
            doc.setTextColor(255, 0, 0);
            doc.text('No data available for the selected filters', doc.internal.pageSize.getWidth() / 2, yPos + 20, { align: 'center' });
            doc.save('Employee_Report_No_Data.pdf');
            return;
        }
        
        // Get all data from filtered rows
        tableData.each(function(value, index) {
            // Format date of birth if exists
            let dob = value.emp_birthday || '';
            if (dob && dob !== '0000-00-00') {
                const dateParts = dob.split('-');
                if (dateParts.length === 3) {
                    dob = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                }
            } else {
                dob = '';
            }
            
            // Format permanent date if exists
            let permanentDate = value.emp_permanent_date || '';
            if (permanentDate && permanentDate !== '0000-00-00') {
                const dateParts = permanentDate.split('-');
                if (dateParts.length === 3) {
                    permanentDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                }
            } else {
                permanentDate = '';
            }
            
            const row = [
                value.emp_id || '',
                value.employee_display || '',
                value.location || '',
                value.dept_name || '',
                dob,
                value.emp_mobile || '',
                value.emp_work_telephone || '',
                value.emp_national_id || '',
                value.emp_gender || '',
                value.emp_email || '',
                value.emp_address || '',
                value.emp_addressT || '',
                value.title || '',
                value.e_status || '',
                permanentDate
            ];
            body.push(row);
            rowCount++;
        });
        
        // Calculate table width
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 2;
        
        // Generate table using autoTable
        doc.autoTable({
            startY: yPos,
            head: headers,
            body: body,
            theme: 'grid',
            styles: {
                fontSize: 5,
                cellPadding: 2,
                overflow: 'linebreak',
                textAlign: 'left',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 4,
                cellPadding: 3
            },
            columnStyles: {
                0: { cellWidth: 8, halign: 'center' },   // ID
                1: { cellWidth: 25, halign: 'left' },     // EMPLOYEE
                2: { cellWidth: 18, halign: 'left' },     // LOCATION
                3: { cellWidth: 20, halign: 'left' },     // DEPARTMENT
                4: { cellWidth: 18, halign: 'center' },   // DATE OF BIRTH
                5: { cellWidth: 18, halign: 'center' },   // MOBILE NO
                6: { cellWidth: 18, halign: 'center' },   // TELEPHONE
                7: { cellWidth: 18, halign: 'center' },   // NIC
                8: { cellWidth: 12, halign: 'center' },   // GENDER
                9: { cellWidth: 28, halign: 'left' },     // EMAIL
                10: { cellWidth: 30, halign: 'left' },    // PERMANENT ADDRESS
                11: { cellWidth: 30, halign: 'left' },    // TEMPORARY ADDRESS
                12: { cellWidth: 18, halign: 'left' },    // JOB CATEGORY
                13: { cellWidth: 15, halign: 'center' },  // JOB STATUS
                14: { cellWidth: 18, halign: 'center' }   // PERMANENT DATE
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
            tableWidth: 'auto',
            showHead: 'everyPage',
            didParseCell: function(data) {
                // Truncate long email addresses if needed
                if (data.column.index === 9 && data.cell.text && data.cell.text.length > 30) {
                    data.cell.text = data.cell.text.substring(0, 27) + '...';
                }
                // Truncate long addresses if needed
                if ((data.column.index === 10 || data.column.index === 11) && data.cell.text && data.cell.text.length > 35) {
                    data.cell.text = data.cell.text.substring(0, 32) + '...';
                }
            },
            willDrawPage: function(data) {
                // Add company name and page number on each page
                const companyName = $('#company_name').val() || 'Company Name';
                doc.setFontSize(7);
                doc.setFont('helvetica', 'normal');
                doc.text(companyName, margin, 10);
                doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - margin, 10, { align: 'right' });
                
                // Add report title on subsequent pages
                if (data.pageNumber > 1) {
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Employee Report (Continued)', doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
                }
            }
        });
        
        // Add summary on last page
        const totalPages = doc.internal.getNumberOfPages();
        if (totalPages > 0) {
            doc.setPage(totalPages);
            const finalY = doc.lastAutoTable ? doc.lastAutoTable.finalY + 10 : 150;
            
            // Only add summary if there's enough space
            if (finalY < doc.internal.pageSize.getHeight() - 40 && rowCount > 0) {
                doc.setFontSize(8);
                doc.setFont('helvetica', 'bold');
                doc.text('Report Summary:', margin, finalY);
                
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                let summaryY = finalY + 7;
                
                doc.text(`Total Employees: ${rowCount}`, margin, summaryY);
            }
            
            // Add footer on last page
            doc.setFontSize(6);
            const generatedBy = $('#emp_name').val() || 'System User';
            const companyName = $('#company_name').val() || 'Company Name';
            const footerY = doc.internal.pageSize.getHeight() - 10;
            
            if (footerY > 20) {
                doc.text(`Generated by: ${generatedBy}`, margin, footerY);
                doc.text(`Date: ${currentDate}`, doc.internal.pageSize.getWidth() / 2, footerY, { align: 'center' });
                doc.text(companyName, doc.internal.pageSize.getWidth() - margin, footerY, { align: 'right' });
            }
        }
        
        // Save the PDF
        const safeDept = department.replace(/[^a-zA-Z0-9]/g, '_') || 'Report';
        const fileName = `Employee_Report_${safeDept}_${currentDate.replace(/[^0-9]/g, '')}.pdf`;
        doc.save(fileName);
    }
</script>

@endsection