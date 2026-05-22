
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
                         <span>Department-Wise Employee Late Report</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                    <div class="card-body p-0 p-2 main_card">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Records</button>
                        </div><br>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="table_outer">
                            <div class="daily_table table-responsive center-block fix-width scroll-inner" id="tableContainer">
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
                                  <label class="small font-weight-bolder text-dark">Company*</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                              </div>
                          </li>
                          <li class="mb-2">
                              <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Department*</label>
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
                                  <label class="small font-weight-bolder text-dark">Type*</label>
                                  <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                    <option value="">Please Select Type</option>
                                    <option value="1">Month Wise</option>
                                    <option value="2">Date Range Wise</option>
                                </select>
                              </div>
                          </li>

                          <li class="div_date_range">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">From Date</label>
                                  <div class="input-group input-group-sm mb-2">
                                      <input type="date" id="from_date" name="from_date"
                                          class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                  </div>
                              </div>
                          </li>
                          <li class="div_date_range">
                              <div class="col-md-12">
                                  <label class="small font-weight-bolder text-dark">To Date  </label>
                                  <div class="input-group input-group-sm mb-2">
                                      <input type="date" id="to_date" name="to_date"  class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                  </div>
                              </div>
                          </li>
                          <li id="div_month">
                              <div class="col-md-12">
                                 <label class="small font-weight-bolder text-dark">Month</label>
                                 <div class="input-group input-group-sm mb-2">
                                    <input type="month" id="selectedmonth" name="selectedmonth" class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                </div>
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
            $('#departmentvisereport').addClass('navbtnactive');
 
            showInitialMessage()

            $('.div_date_range').addClass('d-none');
            $('#div_month').addClass('d-none');
            $('#reporttype').on('change', function () {
                let $type = $(this).val();
                if ($type == 1) {

                    $('.div_date_range').addClass('d-none');
                    $('#div_month').removeClass('d-none');

                } else {
                    $('#div_month').addClass('d-none');
                    $('.div_date_range').removeClass('d-none');
                }
            });

            let company = $('#company');
            let department = $('#department');
            let location = $('#location');

            company.select2({
                placeholder: 'Select a Company',
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
                placeholder: 'Select a Department',
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
                placeholder: 'Select a Location',
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

            $('#formFilter').on('submit', function (e) {
                let department = $('#department').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let reporttype = $('#reporttype').val();
                let selectedmonth = $('#selectedmonth').val();
                e.preventDefault();

                closeOffcanvasSmoothly();

                 $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("departmentemployeewise_generatelatereport") }}',
                    type: 'POST',
                    data: {
                        department: department,
                        location: location,
                        from_date: from_date,
                        to_date: to_date,
                        reporttype: reporttype,
                        selectedmonth: selectedmonth
                    },
                    success: function (response) {
                         const exportButtonHtml = `
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-danger btn-sm" id="exportLateReportPDF">
                                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                                        </button>
                                    </div>
                                `;
                                
                                $('#tableContainer').html(exportButtonHtml + response.html);
                                
                                // Bind the export button click event
                                $('#exportLateReportPDF').off('click').on('click', function() {
                                    generateLateReportPDF();
                                });
                        $('#leave_report').DataTable({});
                    }
                });
            });

            $('#btn-reset').on('click', function () {
            $('#formFilter')[0].reset();
            $('#company').val(null).trigger('change');
            $('#department').val(null).trigger('change');
        });
    
        });

         function showInitialMessage() {
        $('#tableContainer').html(
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>'
        );
        }

    function generateLateReportPDF() {
        const table = $('#late_report_dt');
        
        if (!table.length || table.find('tbody tr').length === 0) {
            alert('No data available to export');
            return;
        }
        
        const doc = new jsPDF('l', 'mm', 'a4');
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 8;
        
        // ===== CLEAN HEADERS - Remove duplicate "LATE MINUTES" =====
        const allThElements = table.find('thead th');
        const cleanHeaders = [];
        const monthHeaders = [];
        let hasSecondRow = false;
        
        // Check if we have 2 header rows
        const headerRowCount = table.find('thead tr').length;
        if (headerRowCount === 2) {
            // Get only from first row (month names)
            table.find('thead tr').eq(0).find('th').each(function() {
                const text = $(this).text().trim();
                if (text && text !== 'LATE MINUTES') {
                    cleanHeaders.push(text);
                }
            });
            hasSecondRow = true;
        } else {
            // Single header row
            allThElements.each(function() {
                const text = $(this).text().trim();
                if (text && !cleanHeaders.includes(text)) {
                    cleanHeaders.push(text);
                }
            });
        }
        
        // ===== BUILD BODY DATA =====
        const bodyData = [];
        table.find('tbody tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push($(this).text().trim());
            });
            bodyData.push(row);
        });
        
        // ===== FIX TOTAL ROW - Add blank cell for colspan effect =====
        if (bodyData.length > 0) {
            const lastRow = bodyData[bodyData.length - 1];
            if (lastRow[0].toUpperCase() === 'TOTAL') {
                // Add blank cell after "TOTAL" to represent colspan=2 effect
                lastRow.splice(1, 0, '');
            }
        }
        
        // ===== REPORT HEADER =====
        doc.setFontSize(13);
        doc.setFont('helvetica', 'bold');
        doc.text('Late Attendance Report - EMBROIDERY DEPARTMENT', pageWidth / 2, 11, { align: 'center' });
        
        const fromDate = $('#from_date').val() || '2026-01-01';
        const toDate = $('#to_date').val() || '2026-05-21';
        const currentDate = new Date().toLocaleDateString();
        
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text(`Period: ${fromDate} to ${toDate}`, margin, 16);
        doc.text(`Generated on: ${currentDate}`, pageWidth - margin, 16, { align: 'right' });
        
        doc.setLineWidth(0.3);
        doc.line(margin, 18, pageWidth - margin, 18);
        
        // ===== CALCULATE WIDTHS =====
        const colCount = cleanHeaders.length;
        const maxWidth = pageWidth - (margin * 2);
        
        const columnStyles = {
            0: { halign: 'center', cellWidth: 18 },  // EMP ID
            1: { halign: 'left', cellWidth: 55 }     // EMPLOYEE
        };
        
        // Month columns - distribute evenly
        const monthWidth = (maxWidth - 73) / (colCount - 2);
        for (let i = 2; i < colCount; i++) {
            columnStyles[i] = { halign: 'center', cellWidth: monthWidth };
        }
        
        // ===== GENERATE TABLE =====
        doc.autoTable({
            startY: 20,
            head: [cleanHeaders],
            body: bodyData,
            theme: 'grid',
            styles: {
                fontSize: 7,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle',
                lineWidth: 0.2,
                lineColor: [100, 100, 100]
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 7
            },
            columnStyles: columnStyles,
            alternateRowStyles: {
                fillColor: [240, 240, 240]
            },
            didDrawCell: function(data) {
                // Highlight TOTAL row
                if (bodyData.length > 0 && data.row.index === bodyData.length - 1 && data.row.section === 'body') {
                    if (data.cell.text && data.cell.text[0] && data.cell.text[0].toString().toUpperCase() === 'TOTAL') {
                        doc.setFillColor(200, 200, 200);
                        doc.rect(data.cell.x, data.cell.y, data.cell.width, data.cell.height, 'F');
                        doc.setFont('helvetica', 'bold');
                    }
                }
            },
            margin: { left: margin, right: margin },
            tableWidth: maxWidth,
            showHead: 'everyPage',
            pageBreak: 'auto',
            rowPageBreak: 'avoid',
            didDrawPage: function(data) {
                doc.setFontSize(6);
                doc.setFont('helvetica', 'normal');
                const companyName = $('#company_name').val() || 'Organization';
                doc.text(companyName, margin, 3);
                
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(
                    `Page ${data.pageNumber} of ${pageCount}`,
                    pageWidth - margin - 10,
                    doc.internal.pageSize.getHeight() - 3,
                    { align: 'right' }
                );
            }
        });
        
        // ===== FOOTER =====
        doc.setFontSize(6);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(100, 100, 100);
        const generatedBy = $('#emp_name').val() || 'System User';
        doc.text(
            `Generated by: ${generatedBy} | ${currentDate}`,
            margin,
            doc.internal.pageSize.getHeight() - 4
        );
        
        // ===== SAVE =====
        doc.save('Late_Attendance_Report_' + new Date().getTime() + '.pdf');
    }

    </script>

@endsection

