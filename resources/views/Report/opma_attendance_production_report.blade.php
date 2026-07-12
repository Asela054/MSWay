
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
                        ? '<span>Allowed</span>'
                        : '<span>Not Allowed</span>';
                }

                $.each(datalist, function (i, item) {

                    html += '<table class="exporttable" style="border-collapse: collapse; font-size: 14px;" width="100%;">'
                        + '<tr><td colspan="15" style="padding-bottom: 10px; text-align: center; font-size: 20px;"><strong>ATTENDANCE & PRODUCTION SUMMARY APPROVAL</strong></td></tr>'
                        + '<tr><td colspan="15"><strong>DEPARTME:</strong> ' + datalist[i].departmentname + '</td></tr>'
                        + '<tr><td colspan="15"><strong>EMP NO:</strong> ' + datalist[i].emp_id + '</td></tr>'
                        + '<tr><td colspan="15" style="border-bottom: 1px solid black; padding-bottom: 10px;"><strong>NAME:</strong> ' + datalist[i].emp_fullname + '</td></tr>'
                        + '<tr>'
                        + '<th style="border-bottom: 1px solid black;">DATE</th>'
                        + '<th style="border-bottom: 1px solid black;">IN TIME</th>'
                        + '<th style="border-bottom: 1px solid black;">OUT TIME</th>'
                        + '<th style="border-bottom: 1px solid black;">LATE</th>'
                        + '<th style="border-bottom: 1px solid black;">MC NO</th>'
                        + '<th style="border-bottom: 1px solid black;">STYLE DETAILS</th>'
                        + '<th style="border-bottom: 1px solid black;">TARGET</th>'
                        + '<th style="border-bottom: 1px solid black;">PRODUCE</th>'
                        + '<th style="border-bottom: 1px solid black;">PRO AVG</th>'
                        + '<th style="border-bottom: 1px solid black;">PRO INS</th>'
                        + '<th style="border-bottom: 1px solid black;">OT</th>'
                        + '<th style="border-bottom: 1px solid black;">TRP: ALL</th>'
                        + '<th style="border-bottom: 1px solid black;">ATT: ALL</th>'
                        + '<th style="border-bottom: 1px solid black;">NIG: ALL</th>'
                        + '<th style="border-bottom: 1px solid black;">TRG: BO</th>'
                        + '</tr>';

                    var objattendance = datalist[i].attendance;

                    $.each(objattendance, function (j, item) {
                        html += '<tr>'
                            + '<td>' + objattendance[j].formatted_date + '</td>'
                            + '<td>' + objattendance[j].in_time + '</td>'
                            + '<td>' + objattendance[j].out_time + '</td>'
                            + '<td>' + objattendance[j].late_min + '</td>'
                            + '<td>' + objattendance[j].mc_no + '</td>'
                            + '<td>' + objattendance[j].style_details + '</td>'
                            + '<td>' + objattendance[j].target + '</td>'
                            + '<td>' + objattendance[j].produced + '</td>'
                            + '<td>' + objattendance[j].pro_avg + '</td>'
                            + '<td>' + badge(objattendance[j].pro_ins) + '</td>'
                            + '<td>' + badge(objattendance[j].ot) + '</td>'
                            + '<td>' + badge(objattendance[j].trp_all) + '</td>'
                            + '<td>' + badge(objattendance[j].att_all) + '</td>'
                            + '<td>' + badge(objattendance[j].nig_all) + '</td>'
                            + '<td>' + objattendance[j].trg_bo + '</td>'
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

       function exportfunction(){
    $('#btnexport').click(function() {
        var { jsPDF } = window.jspdf;
        var doc = new jsPDF('l', 'pt', 'A4');

        var tables = $('.exporttable');

        tables.each(function(index, table) {
            doc.autoTable({
                html: table,
                startY: index === 0 ? 20 : 20,
                margin: { top: 20, left: 20, right: 20 },
                tableWidth: 'auto',       // force table to fill available page width
                theme: 'grid',            // gives boxed borders like the image
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
                    fillColor: [198, 217, 241], // light blue like the image
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
                    0: { cellWidth: 55 },
                    1: { cellWidth: 45 },
                    2: { cellWidth: 45 },
                    3: { cellWidth: 35 },
                    4: { cellWidth: 75 },
                    5: { cellWidth: 90 },
                    6: { cellWidth: 45 },
                    7: { cellWidth: 45 },
                    8: { cellWidth: 50 },
                    9: { cellWidth: 55 },
                    10: { cellWidth: 40 },
                    11: { cellWidth: 55 },
                    12: { cellWidth: 55 },
                    13: { cellWidth: 55 },
                    14: { cellWidth: 45 }
                },
                didParseCell: function(data) {
                    if (data.row.raw.length === 1 && data.cell.colSpan === 15) {
                        data.cell.styles.lineWidth = 0;
                        data.cell.styles.fontStyle = 'bold';

                        // Title row is the first colSpan-15 row in the table (row index 0)
                        if (data.row.index === 0) {
                            data.cell.styles.halign = 'center';
                            data.cell.styles.fontSize = 16;
                        } else {
                            data.cell.styles.halign = 'left';
                            data.cell.styles.fontSize = 8;
                        }
                    }
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

