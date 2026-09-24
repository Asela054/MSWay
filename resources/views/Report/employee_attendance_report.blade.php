
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
                    <span>Employee Attendance Time Sheet</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                        class="fas fa-filter mr-1"></i> Filter
                                    Records</button><br>
                            </div>
                            <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-danger btn-sm float-right px-3" id="btnexport"><i class="fas fa-file-pdf mr-2"></i>Export PDF</button>
                                 <br><br>
                            </div>
                        </div>

                    <div class="alert alert-primary" role="alert">
                        Employee data now loads in batches. First 100 entries, then scroll to load more.
                    </div>
                    <div id="employee_list"></div>
                </div>
            </div>


        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title font-weight-bolderer" id="offcanvasRightLabel">Records Filter Options</h2>
                <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                    <span aria-hidden="true" class="h1 font-weight-bolderer">&times;</span>
                </button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <form class="form-horizontal" id="formFilter">
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Company*</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($companies as $company){
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                    }
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bolder text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    <option value="All">All Departments</option>
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

                                <input type="submit" class="d-none" id="hideformsubmit">
                                <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn px-3" id="pdf_excel">
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
            $('#department').select2({ width: '100%' });

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

            var loading = false;
            var lastEmpId = 0;

            function loadEmployees() {
                if (loading) return;
                loading = true;
                var departmentID = $('#department').val();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();            

                closeOffcanvasSmoothly();


             $.ajax({
                    url: "{{ route('employeetimesheetgenerate') }}",
                    method: "POST",
                    data: {
                        department: departmentID,
                        from_date: from_date,
                        to_date: to_date,
                        last_emp_id: lastEmpId,
                        _token: '{{csrf_token()}}'
                    },
                   success: function (result) {
                        $('#pdf_excel').html('<i class="fas fa-file-pdf mr-2"></i> Search').prop('disabled', false);
                        if (result.length > 0) {
                            var html = '';

                            var obj = JSON.parse(result);
                            let datalist = obj[0].data;

                            $.each(datalist, function (i, item) {
                                if (lastEmpId != datalist[i].id) {
                                    lastEmpId = datalist[i].id;
                                    let totot = 0;
                                    let totdoubleot = 0;
                                    let tottripleot = 0;
                                    let totlatemin = 0;
                                    let totOtMinutes = 0;        
                                    let totDoubleOtMinutes = 0;  

                                    html += '<table class="exporttable" style="border-collapse: collapse; font-size: 12px;" width="100%;">'
                                        // colspan=15 now - matches total data columns exactly
                                        + '<tr><td colspan="15" style="padding-bottom: 10px;"><strong>' + datalist[i].companyname + '</strong></td></tr>'
                                        + '<tr><td colspan="15" style="border-bottom: 1px solid black;padding-bottom: 10px;"><strong>Summarize Attendance</strong></td></tr>'
                                        + '<tr>'
                                            + '<td colspan="7" style="padding-top: 10px;"><strong>Number-</strong> ' + datalist[i].emp_id + '</td>'
                                            + '<td colspan="8" style="padding-top: 10px;"><strong>Division -</strong> ' + datalist[i].departmentname + '</td>'
                                        + '</tr>'
                                        + '<tr>'
                                            + '<td colspan="7"><strong>Name -</strong> ' + datalist[i].emp_fullname + '</td>'
                                            + '<td colspan="8"><strong>Sub-Division -</strong> ' + (datalist[i].subdivisionname ?? 'None') + '</td>'
                                        + '</tr>'
                                        + '<tr>'
                                            + '<td colspan="15" style="border-bottom: 1px solid black;padding-bottom: 10px;"><strong>Designation-</strong> ' + (datalist[i].jobtitlename ?? '') + '</td>'
                                        + '</tr>'
                                        + '<tr>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Date</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Shift</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Day</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Remark</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">In</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Out</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">In 2</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Out 2</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Late</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">OT</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">DOT</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Days Pay</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">24Cont</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Leave Type</th>'
                                            + '<th style="padding-top: 10px;padding-bottom: 10px;border-bottom: 1px solid black;">Leave Amount</th>'
                                        + '</tr>';

                                    var objattendance = datalist[i].attendance;

                                    $.each(objattendance, function (j, item) {
                                        if (objattendance[j].in_time == null && objattendance[j].leave_type == '') {
                                            var status;
                                                if (objattendance[j].day_type == 'Work') {
                                                    status = 'Absent';
                                                } else if (objattendance[j].day_type == 'Saturday' && datalist[i].is_sat_ot_type_as_act != 0) {
                                                    status = 'Absent';
                                                } else if (objattendance[j].day_type == 'Sunday' && datalist[i].is_sun_ot_type_as_act == 2) {
                                                    status = 'Absent';
                                                } else {
                                                    status = 'OFF';
                                                }
                                            html += '<tr>'
                                                + '<td>' + objattendance[j].in_date + '</td>'
                                                + '<td>' + objattendance[j].shift + '</td>'
                                                + '<td>' + objattendance[j].day_type + '</td>'
                                                + '<td' + (status == 'Absent' ? ' style="color: red;"' : '') + '>' + status + '</td>'
                                                + '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'
                                                + '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'
                                                + '<td>&nbsp;</td><td>&nbsp;</td>'
                                            + '</tr>';
                                        } else if (objattendance[j].in_time == null && objattendance[j].leave_type != '') {
                                            html += '<tr>'
                                                + '<td>' + objattendance[j].in_date + '</td>'
                                                + '<td>' + objattendance[j].shift + '</td>'
                                                + '<td>' + objattendance[j].day_type + '</td>'
                                                + '<td>Leave</td>'
                                                + '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'
                                                + '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>'
                                                + '<td>' + objattendance[j].leave_type + '</td>'
                                                + '<td>' + objattendance[j].leave_days + '</td>'
                                            + '</tr>';
                                        } else {
                                            totot += objattendance[j].ot_hours;
                                            totdoubleot += objattendance[j].double_ot;
                                            tottripleot += objattendance[j].triple_ot;
                                            totlatemin += objattendance[j].late_min;

                                            if (objattendance[j].ot_hours > 0) {
                                                totOtMinutes += timeToMinutes(objattendance[j].duration_time);
                                            }
                                            if (objattendance[j].double_ot > 0) {
                                                totDoubleOtMinutes += timeToMinutes(objattendance[j].duration_time);
                                            }

                                            html += '<tr>'
                                                + '<td>' + objattendance[j].in_date + '</td>'
                                                + '<td>' + objattendance[j].shift + '</td>'
                                                + '<td>' + objattendance[j].day_type + '</td>'
                                                + '<td>Present</td>'
                                                + '<td>' + objattendance[j].in_time + '</td>'
                                                + '<td>' + objattendance[j].out_time + '</td>'
                                                + '<td>' + (objattendance[j].in_time2 ?? '') + '</td>'
                                                + '<td>' + (objattendance[j].out_time2 ?? '') + '</td>'
                                                + '<td>' + objattendance[j].late_min + '</td>'
                                                + '<td>' + (objattendance[j].ot_hours > 0 ? trimSeconds(objattendance[j].duration_time) : '0') + '</td>'
                                                + '<td>' + (objattendance[j].double_ot > 0 ? trimSeconds(objattendance[j].duration_time) : '0') + '</td>'
                                                // + '<td>' + objattendance[j].double_ot + '</td>'
                                                + '<td>' + (objattendance[j].days_pay ?? '0') + '</td>'
                                                + '<td>' + (objattendance[j].cont24 ?? '0') + '</td>'
                                                + '<td>' + objattendance[j].leave_type + '</td>'
                                                + '<td>' + objattendance[j].leave_days + '</td>'
                                            + '</tr>';
                                        }
                                    });

                                    // Total row - colspan=8 (Date..Out2) + 3 single (Late,OT,DOT) + colspan=4 (DaysPay..LeaveAmount) = 15
                                    html += '<tr>'
                                        + '<td colspan="8">&nbsp;</td>'
                                        + '<td style="border-top: 1px solid black;border-bottom: 2px double black;">' + parseFloat(totlatemin).toFixed(2) + '</td>'
                                        // + '<td style="border-top: 1px solid black;border-bottom: 2px double black;">' + parseFloat(totot).toFixed(2) + '</td>'
                                        // + '<td style="border-top: 1px solid black;border-bottom: 2px double black;">' + parseFloat(totdoubleot).toFixed(2) + '</td>'
                                        + '<td style="border-top: 1px solid black;border-bottom: 2px double black;">' + minutesToHM(totOtMinutes) + '</td>'
                                        + '<td style="border-top: 1px solid black;border-bottom: 2px double black;">' + minutesToHM(totDoubleOtMinutes) + '</td>'
                                        + '<td colspan="4">&nbsp;</td>'
                                    + '</tr>';
                                    html += '</table>';
                                }
                            });

                            lastEmpId = obj[0].lastEmpId;
                            $('#employee_list').append(html);

                            loading = false;

                            exportfunction();
                        } else {
                            $(window).off("scroll");
                        }
                    }
                });
            }

            // Load first 20 employees on button click
            $('#pdf_excel').click(function () {
                $('#pdf_excel').html('<i class="fa fa-spinner fa-spin mr-2"></i> Searching').prop('disabled', true);
                $('#employee_list').empty(); // Clear existing list
                lastEmpId = 0;
                loadEmployees();
            });

            // Load more employees on scroll
            $(window).scroll(function () {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    loadEmployees();
                }
            });
        });

        function exportfunction() {
            $('#btnexport').click(function () {
                var { jsPDF } = window.jspdf;
                var doc = new jsPDF('l', 'pt', 'A4');

                var tables = $('.exporttable');
                var pageWidth = doc.internal.pageSize.getWidth();
                var pageHeight = doc.internal.pageSize.getHeight();
                var marginLeft = 25, marginRight = 25;

                tables.each(function (index, table) {
                    var blockStartY = 25;

                    doc.autoTable({
                        html: table,
                        startY: blockStartY,
                        margin: { top: 25, left: marginLeft, right: marginRight, bottom: 40 }, // extra bottom margin for footer
                        theme: 'plain',
                        tableWidth: 'auto',
                        styles: {
                            fontSize: 8,
                            cellPadding: { top: 4, bottom: 4, left: 4, right: 4 },
                            valign: 'middle',
                            overflow: 'linebreak',
                            lineColor: [0, 0, 0],
                            fillColor: [255, 255, 255],
                            textColor: [0, 0, 0],
                            lineWidth: 0
                        },
                        didParseCell: function (data) {
                            var rowIndex = data.row.index;
                            var section = data.section;
                            var totalBodyRows = data.table.body.length;

                            data.cell.styles.fillColor = [255, 255, 255];
                            data.cell.styles.textColor = [0, 0, 0];

                            if (section === 'body' && rowIndex === 0) {
                                data.cell.styles.fontStyle = 'bold';
                                data.cell.styles.fontSize = 13;
                                data.cell.styles.lineWidth = 0;
                                data.cell.styles.cellPadding = { top: 6, bottom: 8, left: 4, right: 4 };
                            } else if (section === 'body' && rowIndex === 1) {
                                data.cell.styles.fontStyle = 'bold';
                                data.cell.styles.fontSize = 10;
                                data.cell.styles.lineWidth = { top: 0, right: 0, bottom: 1, left: 0 };
                                data.cell.styles.cellPadding = { top: 2, bottom: 8, left: 4, right: 4 };
                            } else if (section === 'body' && (rowIndex === 2 || rowIndex === 3)) {
                                data.cell.styles.fontSize = 9;
                                data.cell.styles.lineWidth = 0;
                                data.cell.styles.cellPadding = { top: 4, bottom: 4, left: 4, right: 4 };
                            } else if (section === 'body' && rowIndex === 4) {
                                data.cell.styles.fontSize = 9;
                                data.cell.styles.lineWidth = { top: 0, right: 0, bottom: 1, left: 0 };
                                data.cell.styles.cellPadding = { top: 4, bottom: 10, left: 4, right: 4 };
                            } else if (section === 'head') {
                                data.cell.styles.fontStyle = 'bold';
                                data.cell.styles.fontSize = 8;
                                data.cell.styles.lineWidth = 0.5;
                                data.cell.styles.halign = 'center';
                                data.cell.styles.cellPadding = { top: 8, bottom: 8, left: 3, right: 3 };
                            } else if (section === 'body' && rowIndex === totalBodyRows - 1) {
                                data.cell.styles.fontStyle = 'bold';
                                data.cell.styles.lineWidth = { top: 1, right: 0, bottom: 0, left: 0 };
                                data.cell.styles.cellPadding = { top: 6, bottom: 6, left: 4, right: 4 };
                            } else if (section === 'body') {
                                data.cell.styles.halign = 'center';
                                data.cell.styles.lineWidth = 0.5;
                                data.cell.styles.cellPadding = { top: 3, bottom: 3, left: 3, right: 3 };
                            }
                        }
                    });

                    // Footer line + page info (drawn on every page this table touches)
                    doc.setLineWidth(0.5);
                    doc.setDrawColor(0, 0, 0);
                    doc.line(marginLeft, pageHeight - 30, pageWidth - marginRight, pageHeight - 30);

                    doc.setFontSize(8);
                    doc.setFont(undefined, 'normal');
                    doc.text('Printed on: ' + new Date().toLocaleString(), marginLeft, pageHeight - 18);
                    doc.text('Page ' + (index + 1) + ' of ' + tables.length, pageWidth - marginRight, pageHeight - 18, { align: 'right' });

                    if (index < tables.length - 1) {
                        doc.addPage();
                    }
                });

                var departmenttext = $("#department option:selected").text();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var doctitle = 'attendance_in_' + departmenttext + '_from_' + from_date + '_to_' + to_date;

                doc.save(doctitle + '.pdf');
            });
        }

        // Helper: parse "HH:MM:SS" or "HH:MM" into total minutes
        function timeToMinutes(timeStr) {
            if (!timeStr) return 0;
            var parts = timeStr.split(':');
            var h = parseInt(parts[0], 10) || 0;
            var m = parseInt(parts[1], 10) || 0;
            return (h * 60) + m;
        }

        // Helper: format total minutes back into "HH:MM"
        function minutesToHM(totalMinutes) {
            var h = Math.floor(totalMinutes / 60);
            var m = totalMinutes % 60;
            return (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
        }

        function trimSeconds(timeStr) {
            if (!timeStr) return '0';
            var parts = timeStr.split(':');
            if (parts.length < 2) return timeStr; // not a time string, return as-is
            return parts[0] + ':' + parts[1];
        }   

    </script>

@endsection

