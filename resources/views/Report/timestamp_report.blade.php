<?php $page_stitle = 'Report on Employee No Pay Days '; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.reports_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3 div_month">
                               <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd" required>

                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd" required>
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div></div>
                        <button class="btn btn-danger btn-sm" id="btn-pdf">
                            <i class="fas fa-file-pdf mr-1"></i> Export PDF
                        </button>
                    </div>
                     <div class="daily_table table-responsive center-block fix-width scroll-inner" id="tableContainer">
                      </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        const { jsPDF } = window.jspdf;

        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeereportmaster').addClass('navbtnactive');


            let company = $('#company');
            let department = $('#department');
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


            //load_dt('');
            function load_dt(department, location,from_date, to_date){

                 $.ajax({
                    url: '{{ route("generate_timestamp_report") }}',
                    type: 'GET',
                    data: {
                        department: department,
                        from_date: from_date,
                        to_date: to_date,
                        location: location
                    },
                    success: function (response) {
                        $('#tableContainer').html(response.html);
                    }
                });

            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let location = $('#location').val();
                let to_date = $('#to_date').val();
                let from_date = $('#from_date').val();

                load_dt(department, location,from_date, to_date);
            });


          $(document).on('click', '#btn-pdf', function() {
                generatePDF();
            });

        

            function generatePDF() {
                // Get filter values for PDF header
                const companyName = $('#company').select2('data')[0]?.text || 'All Companies';
                const departmentName = $('#department').select2('data')[0]?.text || 'All Departments';
                const locationName = $('#location').select2('data')[0]?.text || 'All Locations';
                const fromDate = $('#from_date').val() || 'Not specified';
                const toDate = $('#to_date').val() || 'Not specified';
                const currentDate = new Date().toLocaleDateString();
                
                // Initialize PDF in landscape mode
                const doc = new jsPDF('l', 'mm', 'a4'); // landscape, millimeters, A4 size
                
                // Add report title
                doc.setFontSize(18);
                doc.setFont('helvetica', 'bold');
                doc.text('Employee Timestamp Report', doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });
                
                // Add filter information
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                
                let yPos = 30;
                doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
                doc.text(`Company: ${companyName}`, 100, yPos);
                doc.text(`Department: ${departmentName}`, 180, yPos);
                
                yPos += 5;
                doc.text(`Location: ${locationName}`, 15, yPos);
                doc.text(`Generated on: ${currentDate}`, 180, yPos);
                
                // Add a line separator
                yPos += 7;
                doc.setLineWidth(0.3);
                doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
                yPos += 5;
                
                // Get all tables from the container
                const tableContainer = document.getElementById('tableContainer');
                const tables = tableContainer.getElementsByTagName('table');
                
                if (tables.length > 0) {
                    let isFirstTable = true;
                    
                    // Process each table
                    for (let i = 0; i < tables.length; i++) {
                        const table = tables[i];
                        
                        // Check if we need a new page (leave space for header)
                        if (yPos > 180 && !isFirstTable) {
                            doc.addPage('l', 'a4');
                            yPos = 20;
                        }
                        
                        // Extract table data
                        const headers = [];
                        const data = [];
                        
                        // Get table headers and center align them
                        const headerRows = table.querySelectorAll('thead tr');
                        headerRows.forEach(headerRow => {
                            const headerCells = headerRow.querySelectorAll('th');
                            const headerRowData = [];
                            headerCells.forEach(cell => {
                                headerRowData.push({
                                    content: cell.textContent.trim(),
                                    styles: { halign: 'center' }
                                });
                            });
                            headers.push(headerRowData);
                        });
                        
                        // Get table body data
                        const bodyRows = table.querySelectorAll('tbody tr');
                        bodyRows.forEach(row => {
                            const rowCells = row.querySelectorAll('td');
                            const rowData = [];
                            rowCells.forEach(cell => {
                                rowData.push(cell.textContent.trim());
                            });
                            data.push(rowData);
                        });
                        
                        // Calculate table width (full width minus margins)
                        const pageWidth = doc.internal.pageSize.getWidth();
                        const margin = 15;
                        const tableWidth = pageWidth - (2 * margin);
                        
                        // Generate table using autoTable with 100% width and centered headers
                        doc.autoTable({
                            startY: yPos,
                            head: headers,
                            body: data,
                            theme: 'grid',
                            styles: {
                                fontSize: 8,
                                cellPadding: 2,
                                overflow: 'linebreak',
                                textAlign: 'left'
                            },
                            headStyles: {
                                fillColor: [41, 128, 185],
                                textColor: 255,
                                fontStyle: 'bold',
                                halign: 'center' // Center align header text
                            },
                            columnStyles: {
                                // Center align first column if it contains serial numbers
                                0: { halign: 'center' }
                            },
                            bodyStyles: {
                                textAlign: 'left'
                            },
                            alternateRowStyles: {
                                fillColor: [245, 245, 245]
                            },
                            margin: { left: margin, right: margin },
                            pageBreak: 'auto',
                            tableWidth: tableWidth, // Set table width to calculated width
                            tableLineWidth: 0.1,
                            tableLineColor: [200, 200, 200]
                        });
                        
                        // Update Y position for next table
                        yPos = doc.lastAutoTable.finalY + 10;
                        isFirstTable = false;
                    }
                } else {
                    // If no tables, display a message
                    doc.setFontSize(12);
                    doc.text('No data available for the selected filters', 15, yPos);
                }
                
                // Add page numbers to all pages
                const totalPages = doc.internal.getNumberOfPages();
                for (let i = 1; i <= totalPages; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.text(
                        `Page ${i} of ${totalPages}`,
                        doc.internal.pageSize.getWidth() - 30,
                        doc.internal.pageSize.getHeight() - 10
                    );
                }
                
                // Save the PDF
                doc.save(`Timestamp_Report_${fromDate.replace(/-/g, '_')}_${toDate.replace(/-/g, '_')}.pdf`);
            }


        });
    </script>

@endsection

