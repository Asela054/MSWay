
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
                    <span>Attendance & Production Summary Report</span>
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
            $('#departmentvisereport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });

            let company = $('#company');
            let department = $('#department');

            showInitialMessage();

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

            function loadEmployees() {
            var departmentID = $('#department').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            closeOffcanvasSmoothly();

            $.ajax({
                url: "{{ route('opma_attendanceproduction_generatereport') }}",
                method: "POST",
                data: {
                    department: departmentID,
                    from_date: from_date,
                    to_date: to_date,
                    _token: '{{csrf_token()}}'
                },
                success: function (result) {
                    $('#pdf_excel').html('<i class="fas fa-file-pdf mr-2"></i> Search').prop('disabled', false);
                    if (result.length > 0) {
                        var html = '';
                        var obj = JSON.parse(result);
                        let datalist = obj[0].data;

                    function badge(val) {
                            return val == 1
                                ? '<span style="color: green; font-size: 16px;">&#10004;</span>'
                                : '<span style="color: red; font-size: 16px;">&#10008;</span>';
                        }

                        $.each(datalist, function (i, item) {

                            html += '<table class="exporttable" style="border-collapse: collapse; font-size: 14px;" width="100%;">'
                                    + '<tr><td colspan="16" style="padding-bottom: 10px; padding-top: 10px; text-align: center; font-size: 20px;"><strong>ATTENDANCE & PRODUCTION SUMMARY APPROVAL</strong></td></tr>'
                                    + '<tr><td colspan="16"><strong>DEPARTME:</strong> ' + datalist[i].departmentname + '</td></tr>'
                                    + '<tr><td colspan="16"><strong>EMP NO:</strong> ' + datalist[i].emp_id + '</td></tr>'
                                    + '<tr><td colspan="16" style="border-bottom: 1px solid black; padding-bottom: 10px;"><strong>NAME:</strong> ' + datalist[i].emp_fullname + '</td></tr>'
                                    + '<tr>'
                                    + '<th style="border: 1px solid black; text-align: center;">DATE</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">IN TIME</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">OUT TIME</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">LATE</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">MC NO</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">STYLE DETAILS</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">TARGET</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">PRODUCE</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">PRO AVG</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">WEIGHTED AVG</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">PRO INS</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">OT</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">TRP: ALL</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">ATT: ALL</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">NIG: ALL</th>'
                                    + '<th style="border: 1px solid black; text-align: center;">TRG: BO</th>'
                                    + '</tr>';

                            var objattendance = datalist[i].attendance;

                            $.each(objattendance, function (j, item) {
                                html += '<tr>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].formatted_date + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].in_time + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].out_time + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].late_min + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].mc_no + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].style_details + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].target + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].produced + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].pro_avg + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].weighted_avg + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + badge(objattendance[j].pro_ins) + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + badge(objattendance[j].ot) + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + badge(objattendance[j].trp_all) + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + badge(objattendance[j].att_all) + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + badge(objattendance[j].nig_all) + '</td>'
                                    + '<td style="border: 1px solid black; text-align: center;">' + objattendance[j].trg_bo + '</td>'
                                    + '</tr>';
                            });

                            html += '</table>';
                        });

                        $('#employee_list').append(html);
                        exportfunction();
                    }
                }
            });
        }

            // Load first 20 employees on button click
            $('#pdf_excel').click(function () {
                $('#pdf_excel').html('<i class="fa fa-spinner fa-spin mr-2"></i> Searching').prop('disabled', true);
                $('#employee_list').empty(); // Clear existing list
                loadEmployees();
            });
        });

    function exportfunction() {
        $('#btnexport').click(function() {
            var { jsPDF } = window.jspdf;
            var doc = new jsPDF('l', 'pt', 'A4');

            var tables = $('.exporttable');
            var badgeCols = [10, 11, 12, 13, 14]; // PRO INS, OT, TRP:ALL, ATT:ALL, NIG:ALL

            tables.each(function(index, table) {
                doc.autoTable({
                    html: table,
                    startY: 20,
                    margin: { top: 20, left: 20, right: 20 },
                    tableWidth: 'auto',
                    theme: 'grid',
                    styles: {
                        fontSize: 7,
                        cellPadding: 3,
                        overflow: 'linebreak',
                        valign: 'middle',
                        halign: 'center',
                        lineColor: [0, 0, 0],
                        lineWidth: 0.5
                    },
                    headStyles: {
                        fillColor: [198, 217, 241],
                        textColor: [0, 0, 0],
                        fontSize: 7,
                        fontStyle: 'bold',
                        lineColor: [0, 0, 0],
                        lineWidth: 0.5
                    },
                    bodyStyles: {
                        textColor: [0, 0, 0]
                    },
                    columnStyles: {
                        0: { cellWidth: 50 },
                        1: { cellWidth: 40 },
                        2: { cellWidth: 40 },
                        3: { cellWidth: 30 },
                        4: { cellWidth: 120 },
                        5: { cellWidth: 90 },
                        6: { cellWidth: 40 },
                        7: { cellWidth: 40 },
                        8: { cellWidth: 45 },
                        9: { cellWidth: 50 },
                        10: { cellWidth: 35 },
                        11: { cellWidth: 35 },
                        12: { cellWidth: 45 },
                        13: { cellWidth: 45 },
                        14: { cellWidth: 45 },
                        15: { cellWidth: 45 }
                    },
                    didParseCell: function(data) {
                        // Title / department / emp / name merged rows (colspan=16)
                        if (data.row.raw.length === 1 && data.cell.colSpan === 16) {
                            data.cell.styles.lineWidth = 0;
                            data.cell.styles.fontStyle = 'bold';
                            if (data.row.index === 0) {
                                data.cell.styles.halign = 'center';
                                data.cell.styles.fontSize = 16;
                            } else {
                                data.cell.styles.halign = 'left';
                                data.cell.styles.fontSize = 8;
                            }
                        }

                        // Badge columns: blank the text, we draw the symbol manually
                        // ONLY for actual body <td> cells, never header <th>
                        if (data.section === 'body'
                            && data.cell.raw
                            && data.cell.raw.tagName === 'TD'
                            && badgeCols.indexOf(data.column.index) !== -1) {
                            data.cell.text = [''];
                            data.cell.styles.halign = 'center';
                        }
                    },
                    didDrawCell: function(data) {
                        if (data.section !== 'body') return;
                        if (!data.cell.raw || data.cell.raw.tagName !== 'TD') return;
                        if (badgeCols.indexOf(data.column.index) === -1) return;

                        var raw = data.cell.raw.textContent ? data.cell.raw.textContent.trim() : '';
                        if (raw === '') return; // no badge in this cell (e.g. blank row)

                        var isCheck = raw.indexOf('\u2714') !== -1; // ✓
                        var cx = data.cell.x + data.cell.width / 2;
                        var cy = data.cell.y + data.cell.height / 2;

                        doc.setLineWidth(1.1);
                        if (isCheck) {
                            doc.setDrawColor(0, 150, 0);
                            doc.line(cx - 4, cy, cx - 1, cy + 3);
                            doc.line(cx - 1, cy + 3, cx + 4, cy - 4);
                        } else {
                            doc.setDrawColor(200, 0, 0);
                            doc.line(cx - 3, cy - 3, cx + 3, cy + 3);
                            doc.line(cx - 3, cy + 3, cx + 3, cy - 3);
                        }
                        doc.setDrawColor(0, 0, 0);
                        doc.setLineWidth(0.5);
                    },
                    didDrawPage: function(data) {
                        if (data.pageCount > 1) {
                            doc.lastAutoTable.finalY = 20;
                        }
                    }
                });

                if (index < tables.length - 1) {
                    doc.addPage();
                }
            });

            var departmenttext = $("#department option:selected").text();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            var doctitle = 'attendance_production_in_' + departmenttext + '_from_' + from_date + '_to_' + to_date;

            doc.save(doctitle + '.pdf');
        });
    }

          function showInitialMessage() {
        $('#employee_list').html(
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>'
        );
        }
    </script>

@endsection

