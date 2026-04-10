@extends('layouts.app')

@section('content')

    <main>
         <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                   @include('layouts.attendant&leave_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                         <span>Incomplete Attendance</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                    <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                        data-toggle="offcanvas" data-target="#offcanvasRight"
                                        aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                                        Records</button>
                                </div>
                                 <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                        </div>
                        <div class="row align-items-center mb-2">
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col text-center">
                                <button type="button" id="export_pdf_btn" class="btn btn-danger btn-sm">
                                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                                </button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary btn-sm px-3" id="btn_mark_as_no_pay">
                                    <i class="fas fa-plus mr-2"></i>Mark as NO Pay Leave
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendance_report_table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID</th>
                                            <th>NAME</th>
                                            <th>DEPARTMENT</th>
                                            <th>DATE</th>
                                            <th>CHECK IN TIME</th>
                                            <th>CHECK OUT TIME</th>
                                            <th>WORK HOURS</th>
                                            <th>LOCATION</th>
                                        </tr>
                                    </thead>
                                    <tbody class="response">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ csrf_field() }}
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

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

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
                    url: '{{url("location_list_from_attendance_sel2")}}',
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

            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt('', '', '', from_date, to_date);

            function load_dt(department, employee, location, from_date, to_date ,company) {

                $('.response').html('');
                $.ajax({
                    url: "{{ route('get_incomplete_attendance_by_employee_data') }}",
                    method: "POST",
                    data: {
                        department: department,
                        employee: employee,
                        company: company,
                        location: location,
                        from_date: from_date,
                        to_date: to_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        $('.response').html(res);
                    }
                });

            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                 let company = $('#company').val();

                load_dt(department, employee, location, from_date, to_date,company);
                closeOffcanvasSmoothly();
            });

           

             $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
                 $('#employee').val(null).trigger('change');
                 $('#location').val(null).trigger('change');
             });


            //document .excel-btn click event
            $(document).on('click', '#btn_mark_as_no_pay', function(e) {
                e.preventDefault();

                //btn
                let btn = $(this);
                let btn_text = $(this).html();

                let checked = [];
                //each checked checkbox
                $('.checkbox_attendance:checked').each(function() {
                    let element = $(this);

                    let empid = $(this).data('empid');
                    let date = $(this).data('date');

                    checked.push({
                        empid: empid,
                        date: date
                    });
                });

                if(checked.length > 0) {
                    $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                    $(btn).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('mark_as_no_pay') }}",
                        method: "POST",
                        data: {
                            checked: checked,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (res) {

                                if (res.errors) {
                                const actionObj = {
                                    icon: 'fas fa-warning',
                                    title: '',
                                    message: 'Record Error',
                                    url: '',
                                    target: '_blank',
                                    type: 'danger'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                action(actionJSON);
                            }
                            if (res.success) {
                                const actionObj = {
                                    icon: 'fas fa-save',
                                    title: '',
                                    message: res.success,
                                    url: '',
                                    target: '_blank',
                                    type: 'success'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                actionreload(actionJSON);
                            }
                        }
                    });
                } 
            });

            $('#selectAll').click(function (e) {
                var isChecked = this.checked;
                
                // Update all checkboxes
                $('#attendance_report_table').closest('table').find('td input.checkbox_attendance').prop('checked', isChecked);
                
                // Handle row coloring for all checkboxes
                $('#attendance_report_table').closest('table').find('td input.checkbox_attendance').each(function() {
                    if (isChecked) {
                        // Change row background color when selected
                        $(this).closest('tr').css('background-color', '#f7c8c8');
                    } else {
                        // Reset row background color when deselected
                        $(this).closest('tr').css('background-color', '');
                    }
                });
            });

            // Individual checkbox handler
            $('body').on('click', '.checkbox_attendance', function (){
                if($(this).is(':checked')){
                    $(this).closest('tr').css('background-color', '#f7c8c8');
                } else {
                    $(this).closest('tr').css('background-color', '');
                }
            });

           
            $('#export_pdf_btn').on('click', function() {
                generatePDF();
            });
     

        });
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


        function generatePDF() {
    // Get current filter values for PDF header
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const department = $('#department').val() || 'All';
    const employee = $('#employee').val() || 'All';
    const location = $('#location').val() || 'All';
    const currentDate = new Date().toLocaleDateString();
    
    // Get table data directly from HTML (not DataTable)
    const table = $('#attendance_report_table');
    const rows = table.find('tbody tr');
    
    // Initialize PDF in landscape mode
    const doc = new jsPDF('l', 'mm', 'a4');
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 10;
    
    // Add report title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Incomplete Attendance Report', pageWidth / 2, 15, { align: 'center' });
    
    // Add filter information
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    
    let yPos = 25;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, margin, yPos);
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
    
    // Check if there's data
    if (!rows || rows.length === 0) {
        doc.setFontSize(10);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters', pageWidth / 2, yPos + 20, { align: 'center' });
        doc.save('Incomplete_Attendance_Report_No_Data.pdf');
        return;
    }
    
    // Prepare data from HTML table rows
    const body = [];
    let rowCount = 0;
    let totalWorkingHours = 0;
    let departmentRows = {};
    
    // Parse each row
    rows.each(function() {
        const $row = $(this);
        const $cells = $row.find('td');
        
        // Check if this is a department header row (has colspan)
        if ($cells.length === 1 && $cells.attr('colspan')) {
            const deptName = $cells.eq(0).text().trim();
            body.push({
                isDepartmentHeader: true,
                departmentName: deptName,
                colSpan: 8
            });
        } 
        // Regular data row
        else if ($cells.length >= 9) {
            const rowData = {
                checkbox: $cells.eq(0).find('input').length > 0 ? true : false,
                empId: $cells.eq(1).text().trim(),
                name: $cells.eq(2).text().trim(),
                department: $cells.eq(3).text().trim(),
                date: $cells.eq(4).text().trim(),
                checkIn: $cells.eq(5).text().trim(),
                checkOut: $cells.eq(6).text().trim(),
                workHours: $cells.eq(7).text().trim(),
                location: $cells.eq(8).text().trim()
            };
            
            // Calculate total working hours
            let workHoursDecimal = 0;
            if (rowData.workHours && rowData.workHours !== '-' && rowData.workHours !== 'N/A') {
                if (rowData.workHours.includes(':')) {
                    const parts = rowData.workHours.split(':');
                    workHoursDecimal = parseFloat(parts[0]) + (parseFloat(parts[1]) / 60);
                } else {
                    workHoursDecimal = parseFloat(rowData.workHours) || 0;
                }
                totalWorkingHours += workHoursDecimal;
            }
            
            body.push({
                isDepartmentHeader: false,
                empId: rowData.empId,
                name: rowData.name,
                department: rowData.department,
                date: rowData.date,
                checkIn: rowData.checkIn,
                checkOut: rowData.checkOut,
                workHours: rowData.workHours,
                location: rowData.location
            });
            rowCount++;
        }
    });
    
    // Define headers (without checkbox column)
    const headers = [[
        'EMP ID', 'NAME', 'DEPARTMENT', 'DATE', 
        'CHECK IN', 'CHECK OUT', 'WORK HOURS', 'LOCATION'
    ]];
    
    // Generate table using autoTable
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body.map(row => {
            if (row.isDepartmentHeader) {
                return [{ content: row.departmentName, colSpan: 8, styles: { fillColor: [230, 242, 255], fontStyle: 'bold', halign: 'left', textColor: [41, 128, 185] } }];
            }
            return [
                row.empId,
                row.name,
                row.department,
                row.date,
                row.checkIn,
                row.checkOut,
                row.workHours,
                row.location
            ];
        }),
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: { top: 2, bottom: 2, left: 3, right: 3 },
            overflow: 'linebreak',
            valign: 'middle',
            lineColor: [200, 200, 200],
            lineWidth: 0.1
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 9,
            cellPadding: { top: 3, bottom: 3, left: 3, right: 3 }
        },
        columnStyles: {
            0: { cellWidth: 18, halign: 'center' },   // EMP ID
            1: { cellWidth: 65, halign: 'left' },     // NAME
            2: { cellWidth: 50, halign: 'left' },     // DEPARTMENT
            3: { cellWidth: 32, halign: 'center' },   // DATE
            4: { cellWidth: 25, halign: 'center' },   // CHECK IN
            5: { cellWidth: 25, halign: 'center' },   // CHECK OUT
            6: { cellWidth: 25, halign: 'right' },    // WORK HOURS
            7: { cellWidth: 40, halign: 'left' }      // LOCATION
        },
        bodyStyles: {
            textColor: [0, 0, 0],
            fontSize: 8
        },
        alternateRowStyles: {
            fillColor: [248, 248, 248]
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        showHead: 'everyPage',
        didParseCell: function(data) {
            // Style for missing check out time
            if (data.column.index === 5 && data.cell.text === '-') {
                data.cell.styles.textColor = [255, 0, 0];
                data.cell.styles.fontStyle = 'italic';
            }
            
            // Style for missing work hours
            if (data.column.index === 6 && (data.cell.text === '-' || data.cell.text === 'N/A')) {
                data.cell.styles.textColor = [255, 0, 0];
                data.cell.styles.fontStyle = 'italic';
            }
            
            // Right align work hours with bold if > 8 hours
            if (data.column.index === 6 && data.cell.text !== '-' && data.cell.text !== 'N/A' && !isNaN(parseFloat(data.cell.text))) {
                data.cell.styles.halign = 'right';
                const hours = parseFloat(data.cell.text);
                if (hours > 8) {
                    data.cell.styles.fontStyle = 'bold';
                    data.cell.styles.textColor = [0, 150, 0];
                }
            }
            
            // Center align date
            if (data.column.index === 3) {
                data.cell.styles.halign = 'center';
            }
        },
        willDrawPage: function(data) {
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(100, 100, 100);
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, pageWidth - margin, 10, { align: 'right' });
            
            if (data.pageNumber > 1) {
                doc.setFontSize(10);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 0);
                doc.text('Incomplete Attendance Report (Continued)', pageWidth / 2, 18, { align: 'center' });
            }
        },
        didDrawPage: function(data) {
            // Add bottom border on each page
            doc.setDrawColor(200, 200, 200);
            doc.setLineWidth(0.2);
            doc.line(margin, doc.internal.pageSize.getHeight() - 12, pageWidth - margin, doc.internal.pageSize.getHeight() - 12);
        }
    });
    
    // Save the PDF
    const safeDept = department.replace(/[^a-zA-Z0-9]/g, '_') || 'Report';
    const fileName = `Incomplete_Attendance_Report_${safeDept}_${currentDate.replace(/[^0-9]/g, '')}.pdf`;
    doc.save(fileName);
}
    </script>

@endsection


