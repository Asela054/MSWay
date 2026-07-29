
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
                         <span>Department-Wise Employee O.T Report</span>
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
                    url: '{{ route("departmentemployeewise_generateotreport") }}',
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
                                        <button type="button" class="btn btn-danger btn-sm" id="exportOTReportPDF">
                                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                                        </button>
                                    </div>
                                `;
                                
                                $('#tableContainer').html(exportButtonHtml + response.html);
                                
                                // Bind the export button click event
                                $('#exportOTReportPDF').off('click').on('click', function() {
                                    generateOTReportPDF();
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
        function generateOTReportPDF() {
            const table = $('#ot_report_dt');

            if (!table.length || table.find('tbody tr').length === 0) {
                alert('No data available to export');
                return;
            }

            const doc = new jsPDF('l', 'mm', 'a4');
            const pageWidth = doc.internal.pageSize.getWidth();
            const margin = 8;

            // ===== READ MONTH GROUP LABELS FROM ROW 1 (skip EMP ID / EMPLOYEE) =====
            const monthLabels = [];
            table.find('thead tr').eq(0).find('th').each(function(i) {
                if (i < 2) return; // skip EMP ID, EMPLOYEE
                monthLabels.push($(this).text().trim());
            });

            // ===== READ SUB-HEADERS (NORMAL OT / DOUBLE OT pairs) FROM ROW 2 =====
            const subHeaders = [];
            table.find('thead tr').eq(1).find('th').each(function() {
                subHeaders.push($(this).text().trim());
            });

            // ===== BUILD TWO-ROW HEAD =====
            const headRow1 = [
                { content: 'EMP ID', rowSpan: 2 },
                { content: 'EMPLOYEE', rowSpan: 2 }
            ];
            monthLabels.forEach(label => {
                headRow1.push({ content: label, colSpan: 2, halign: 'center' });
            });

            const headRow2 = subHeaders.map(text => ({ content: text }));

            const head = [headRow1, headRow2];

            // ===== BUILD BODY DATA =====
            const bodyData = [];
            table.find('tbody tr').each(function() {
                const row = [];
                $(this).find('td').each(function() {
                    row.push($(this).text().trim());
                });
                if (row.length) bodyData.push(row);
            });

            // ===== FIX TOTAL ROW - merge first two cells (colspan=2 in HTML) =====
            let totalRowIndex = -1;
            if (bodyData.length > 0) {
                const lastRow = bodyData[bodyData.length - 1];
                if (lastRow[0].toUpperCase() === 'TOTAL') {
                    lastRow.splice(1, 0, ''); // add blank cell to represent colspan=2
                    totalRowIndex = bodyData.length - 1;
                }
            }

            // ===== REPORT HEADER =====
            const deptName = $('#department_name_display').val()
                || $('#department option:selected').text()
                || 'All Departments';

            doc.setFontSize(13);
            doc.setFont('helvetica', 'bold');
            doc.text('Department-Wise Employee O.T Report - ' + deptName, pageWidth / 2, 11, { align: 'center' });

            const reporttype = $('#reporttype').val();
            let periodText = '';
            if (reporttype == '1') {
                const selectedMonth = $('#selectedmonth').val();
                periodText = selectedMonth
                    ? new Date(selectedMonth + '-01').toLocaleString('default', { month: 'long', year: 'numeric' })
                    : '';
            } else if (reporttype == '2') {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                periodText = (fromDate && toDate) ? `${fromDate} to ${toDate}` : '';
            }
            const currentDate = new Date().toLocaleDateString();

            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.text(`Period: ${periodText}`, margin, 16);
            doc.text(`Generated on: ${currentDate}`, pageWidth - margin, 16, { align: 'right' });

            doc.setLineWidth(0.3);
            doc.line(margin, 18, pageWidth - margin, 18);

            // ===== COLUMN WIDTHS =====
            const maxWidth = pageWidth - (margin * 2);
            const columnStyles = {
                0: { halign: 'center', cellWidth: 20 },  // EMP ID
                1: { halign: 'left', cellWidth: 55 }     // EMPLOYEE
            };
            const otColCount = monthLabels.length * 2;
            const otColWidth = (maxWidth - 75) / otColCount;
            for (let i = 0; i < otColCount; i++) {
                columnStyles[2 + i] = { halign: 'center', cellWidth: otColWidth };
            }

            // ===== GENERATE TABLE =====
            doc.autoTable({
                startY: 20,
                head: head,
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
                didParseCell: function(data) {
                    // Bold/highlight the TOTAL row
                    if (totalRowIndex !== -1 && data.row.index === totalRowIndex && data.row.section === 'body') {
                        data.cell.styles.fillColor = [200, 200, 200];
                        data.cell.styles.fontStyle = 'bold';
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
            doc.save('OT_Report_' + new Date().getTime() + '.pdf');
        }
    </script>

@endsection

