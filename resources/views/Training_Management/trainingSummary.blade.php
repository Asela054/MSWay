@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid d-none d-sm-block shadow">
            @include('layouts.employee_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-users-gear"></i></div>
                    <span>Training Summary</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-2">
                <div class="row">
                    <div class="col-12">
                        <div class="row align-items-center mb-4">
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
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>DATE</th>
                                            <th class="text-right">ACTION</th>
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
        </div>

        {{-- offcanvas menu --}}
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
                                <label class="small font-weight-bolder text-dark">Allocation <span class="text-red">*</span></label>
                                <select name="allocation_id" id="allocation_id" class="form-control form-control-sm" required>
                                    <option value="">Select Allocation...</option>
                                </select>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bold text-dark">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" />
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="col-md-12">
                                <label class="small font-weight-bold text-dark">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" />
                            </div>
                        </li>
                        <li>
                            <div class="col-md-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-sm px-3" id="btn-reset">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm px-3" id="btn-filter">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </li>
                    </form>
                </ul>
            </div>
        </div>

        <!-- Types Modal -->
        <div class="modal fade" id="typesModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-lg modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h5 class="modal-title">View Training Types</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="types_session_id" />
                        <input type="hidden" id="types_allocation_id" />
                        <input type="hidden" id="types_emp_id" />
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="typesTable">
                                <thead id="types_thead"></thead>
                                <tbody id="types_tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#employee_menu_link').addClass('active');
        $('#employee_menu_link_icon').addClass('active');
        $('#training').addClass('navbtnactive');

        // Load allocations into filter dropdown
        $.get('{{ url("get_allocations_list") }}', function(data) {
            var allocationSel = $('#allocation_id');
            allocationSel.empty().append('<option value="">Select Allocation...</option>');
            $.each(data, function(i, item) {
                allocationSel.append('<option value="' + item.id + '">' + item.training_name + '</option>');
            });
        });


        load_dt(true);

        function load_dt(isInitialLoad) {
            $('#dataTable').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [{
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Training Summary Details',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        title: 'Training Summary Details',
                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Training Summary Details',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                "order": [
                    [0, "desc"]
                ],
                ajax: {
                    url: '{{ url("/training_summary_list") }}',
                    "type": "POST",
                    "headers": {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    "data": function(d) {
                        d.allocation_id = $('#allocation_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                language: {
                    emptyTable: isInitialLoad ?
                        "<div class='text-center py-4'><h5 class='text-muted'>No records to display</h5><p class='text-muted small'>Please use the filter options to search for records</p></div>" : "No data available in table"
                },
                columns: [{
                        data: 0,
                        name: 'id'
                    },
                    {
                        data: 1,
                        name: 'emp_id'
                    },
                    {
                        data: 2,
                        name: 'employee_display'
                    },
                    {
                        data: 3,
                        name: 'date'
                    },
                    {
                        data: '4',
                        orderable: false,
                        searchable: false,
                        className: 'text-right'
                    }
                ],
                order: [
                    [0, 'asc']
                ],
            });
        }

        // Filter form submission
        $('#formFilter').on('submit', function(e) {
            e.preventDefault();
            load_dt(false);
            $('#offcanvasRight .btn-close').trigger('click');
        });

        // Reset button
        $('#btn-reset').on('click', function() {
            $('#formFilter')[0].reset();
            $('#allocation_id_label').text('Allocation Name: ');
            load_dt(true);
        });

        // Open types modal
        $(document).on('click', '.open-types-modal', function() {
            var allocation_id = $(this).data('allocation');
            var emp_id = $(this).data('employee');

            $('#types_thead').html('<tr><th colspan="99" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</th></tr>');
            $('#types_tbody').html('');
            $('#typesModal').modal('show');

            $.get('{{ url("get_summary_types") }}', {
                allocation_id: allocation_id,
                emp_id: emp_id
            }, function(data) {
                var sessions = data.sessions;
                var types = data.types;
                var points = data.points;

                var h1 = '<tr><th rowspan="2" class="align-middle">TRAINING TYPE</th>';
                var h2 = '<tr>';

                $.each(sessions, function(i, s) {
                    h1 += '<th colspan="2" class="text-center">' + s.session_name + '</th>';
                    h2 += '<th class="text-center">Done</th><th class="text-center">Points</th>';
                });


                h1 += '</tr>';
                h2 += '</tr>';
                $('#types_thead').html(h1 + h2);

                // Rows -> sp - session points, pts - points
                var rows = '';
                $.each(types, function(i, type) {
                    rows += '<tr><td>' + type.type_name + '</td>';
                    $.each(sessions, function(j, s) {
                        var sp = points[s.session_id] || [];
                        var match = $.grep(sp, function(p) {
                            return parseInt(p.type_id) == parseInt(type.type_id);
                        })[0];
                        var done = match && match.is_attend == 1 ? '<span class="text-success">✔</span>' : '<span class="text-muted">—</span>';
                        var pts = match && match.points != null ? match.points : '<span class="text-muted">—</span>';
                        rows += '<td class="text-center">' + done + '</td><td class="text-center">' + pts + '</td>';
                    });
                    rows += '</tr>';
                });
                $('#types_tbody').html(rows || '<tr><td colspan="99" class="text-center text-muted">No types found</td></tr>');
            });
        });

        //print handler
        $(document).on('click', '.print-row', function() {
            var allocation_id = $(this).data('allocation');
            var emp_id = $(this).data('employee');

            $.get('{{ url("get_summary_types") }}', {
                allocation_id: allocation_id,
                emp_id: emp_id
            }, function(data) {
                var sessions  = data.sessions;
                var types     = data.types;
                var points    = data.points;
                var employee  = data.employee  || {};
                var trainDate = data.training_date || '';

                var empName  = (employee.emp_name_with_initial || '') + ' ' + (employee.calling_name || '');
                var deptName = employee.department_name || '';
                var picFile  = employee.emp_pic_filename  || '';
                var baseUrl  = '{{ asset("") }}';
                var picUrl   = picFile ? (baseUrl + 'images/' + picFile) : '';

                var currentYear = new Date().getFullYear();

                // ---- header row ----
                var headerHtml =
                    '<table style="width:100%;border-collapse:collapse;border:1px solid #333;margin-bottom:0">' +
                    '<tr>' +
                    '<td style="width:130px;padding:8px;border:1px solid #333;vertical-align:middle;text-align:center">' +
                    (picUrl
                        ? '<img src="' + picUrl + '" style="width:110px;height:110px;object-fit:cover;border:1px solid #aaa"/>'
                        : '<div style="width:110px;height:110px;border:1px solid #aaa;display:inline-block;line-height:110px;color:#aaa;font-size:11px;text-align:center">No Photo</div>'
                    ) +
                    '</td>' +
                    '<td style="padding:8px;border:1px solid #333;text-align:center;vertical-align:middle">' +
                    '<strong style="font-size:22px">Defect Training Points</strong>' +
                    '</td>' +
                    '</tr>' +
                    '</table>';

                // ---- info rows ----
                var infoHtml =
                    '<table style="width:100%;border-collapse:collapse;border:1px solid #333;border-top:none;margin-bottom:0">' +
                    '<tr>' +
                    '<td style="width:280px;padding:6px 10px;border:1px solid #333;font-weight:bold">Date</td>' +
                    '<td style="padding:6px 10px;border:1px solid #333">' + trainDate + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td style="padding:6px 10px;border:1px solid #333;font-weight:bold">Employee Name</td>' +
                    '<td style="padding:6px 10px;border:1px solid #333">' + empName.trim() + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td style="padding:6px 10px;border:1px solid #333;font-weight:bold">Designation</td>' +
                    '<td style="padding:6px 10px;border:1px solid #333"></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td style="padding:6px 10px;border:1px solid #333;font-weight:bold">Department</td>' +
                    '<td style="padding:6px 10px;border:1px solid #333">' + deptName + '</td>' +
                    '</tr>' +
                    '</table>';

                // ---- year row ----
                var yearHtml =
                    '<table style="width:100%;border-collapse:collapse;border:1px solid #333;border-top:none;margin-bottom:0">' +
                    '<tr>' +
                    '<td colspan="99" style="text-align:center;padding:10px;border:1px solid #333;font-size:26px;font-weight:bold;color:#2255bb">- ' + currentYear + ' -</td>' +
                    '</tr>' +
                    '</table>';

                // ---- data table ----
                var h1 = '<tr><th rowspan="2" style="background:#f0f0f0;padding:6px 8px;border:1px solid #333">Training Type</th>';
                var h2 = '<tr>';
                $.each(sessions, function(i, s) {
                    h1 += '<th colspan="2" style="background:#f0f0f0;padding:6px 8px;border:1px solid #333;text-align:center">' + s.session_name + '</th>';
                    h2 += '<th style="background:#f0f0f0;padding:6px 8px;border:1px solid #333;text-align:center">Done</th>' +
                          '<th style="background:#f0f0f0;padding:6px 8px;border:1px solid #333;text-align:center">Points</th>';
                });
                h1 += '</tr>';
                h2 += '</tr>';

                var rows = '';
                $.each(types, function(i, type) {
                    rows += '<tr><td style="padding:5px 8px;border:1px solid #333">' + type.type_name + '</td>';
                    $.each(sessions, function(j, s) {
                        var sp    = points[s.session_id] || [];
                        var match = $.grep(sp, function(p) {
                            return parseInt(p.type_id) == parseInt(type.type_id);
                        })[0];
                        rows += '<td style="text-align:center;padding:5px 8px;border:1px solid #333">' +
                                    (match && match.is_attend == 1 ? '✔' : '—') +
                                '</td>';
                        rows += '<td style="text-align:center;padding:5px 8px;border:1px solid #333">' +
                                    (match && match.points != null ? match.points : '—') +
                                '</td>';
                    });
                    rows += '</tr>';
                });

                var dataTableHtml =
                    '<table style="width:100%;border-collapse:collapse;border:1px solid #333;border-top:none">' +
                    '<thead>' + h1 + h2 + '</thead>' +
                    '<tbody>' + (rows || '<tr><td colspan="99" style="text-align:center;padding:10px;border:1px solid #333">No types found</td></tr>') + '</tbody>' +
                    '</table>';

                var win = window.open('', '_blank');
                win.document.write(
                    '<html><head><title>Defect Training Points</title>' +
                    '<style>' +
                    'body{font-family:sans-serif;font-size:13px;margin:0;padding:24px}' +
                    '.print-wrap{padding:20px}' +
                    '@media print{body{padding:20px}}' +
                    '</style></head><body>' +
                    '<div class="print-wrap">' +
                    headerHtml + infoHtml + yearHtml + dataTableHtml +
                    '</div>' +
                    '</body></html>'
                );
                win.document.close();
                win.print();
            });
        });


    });
</script>

@endsection