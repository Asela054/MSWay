@extends('layouts.app')

@section('content')

<main>
       <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                   @include('EmployeeWorkRates.workRate_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-check"></i></div>
                         <span>Employee Work Rate</span>
                     </h1>
                 </div>
             </div>
         </div>

    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2 main_card">
                <div class="row">
                    <div class="col-md-12">

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
                                <div class="col-12 text-right">
                                    <button id="approve_att" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Add All</button>
                                </div>
                            </div>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap w-100" id="attendtable">
                                <thead>
                                <tr>
                                    <th>EMPLOYEE ID</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>DEPARTMENT</th>
                                    <th>WORKING DAYS</th>
                                    <th>WORKING HOURS</th>
                                    <th>LEAVE DAYS</th>
                                    <th>NOPAY DAYS</th>
                                    <th>LATE HOURS</th>
                                    <th>NORMAL OT HRS</th>
                                    <th>DOUBLE OT HRS</th>
                                    <th>TRIPLE OT HRS</th>
                                    <th>HOLIDAY NOPAY</th>
                                    <th>HOL. NORMAL OT</th>
                                    <th>HOL. DOUBLE OT</th>
                                    <th class="d-none">EMP_AUTO_ID</th>
                                    <th class="d-none">EMP_ETFNO</th>
                                </tr>
                                </thead>
                                <tbody class="response"></tbody>
                            </table>
                        </div>
                    </div>
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
                                   <label class="small font-weight-bolder text-dark">Company*</label>
                                    <select name="company" id="company" class="form-control form-control-sm" required>
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
                                    <label class="small font-weight-bolder text-dark">Month*</label>
                                     <input type="month" id="month" name="month" class="form-control form-control-sm" placeholder="yyyy-mm" required>
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

    $('#workrate_menu_link').addClass('active');
    $('#workrate_menu_link_icon').addClass('active');
    $('#emp_work_rate').addClass('navbtnactive');

    let company    = $('#company');
    let department = $('#department');
    let dtInstance = null;

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

    showInitialMessage();

    function inputCell(savedValue, name, readOnly = false) {
        const sv = (savedValue !== null && savedValue !== undefined && savedValue !== '') ? savedValue : '';
        return `
            <input type="number" min="0" step="0.01"
                class="form-control form-control-sm work-rate-input text-center"
                name="${name}" value="${sv}"
                style="min-width:80px;${readOnly ? 'background:#f8f9fa;color:#6c757d;cursor:default;' : ''}"
                ${readOnly ? 'readonly tabindex="-1"' : ''}>`;
    }

    function load_dt(company, department, month) {

        if (dtInstance) { dtInstance.destroy(); dtInstance = null; }
        $('#attendtable tbody').empty();

        dtInstance = $('#attendtable').DataTable({
            processing  : true,
            serverSide  : true,
            destroy     : true,
            dom : "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" +
                  "<'row'<'col-sm-12'tr>>" +
                  "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-success btn-sm',
                    title: 'Employee Work Rate',
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-danger btn-sm', 
                    title: 'Employee Work Rate', 
                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                    orientation: 'landscape', 
                    pageSize: 'legal', 
                    customize: function(doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Employee Work Rate',
                    className: 'btn btn-primary btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> Print',
                    customize: function(win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    },
                },
            ],
            order  : [[0, 'asc']],
            ajax: function(data, callback, settings) {
                $.ajax({
                    url: '{{ url("/emp_work_rate_list_ko") }}',
                    data: Object.assign({}, data, { company, department, month }),
                    dataType: 'json',
                    success: function(json) {
                        callback(json);
                    },
                    error: function(xhr) {
                        let msg = 'An error occurred while loading records.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            msg = xhr.responseJSON.error;
                        }
                        const actionObj = { icon:'fas fa-warning', title:'', message: msg, url:'', target:'_blank', type:'danger' };
                        action(JSON.stringify(actionObj));
                        callback({ data: [], recordsTotal: 0, recordsFiltered: 0 });
                    }
                });
            },
            columns: [
                { data: 'uid',                   name: 'employees.emp_id' },
                { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                { data: 'dept_name', name: 'departments.name' },

                { data: 'work_days',            name: 'work_days',            orderable: false, searchable: false,
                render: (v) => inputCell(v, 'work_days', true) },

                { data: 'working_hours',        name: 'working_hours',        orderable: false, searchable: false,
                render: (v) => inputCell(v, 'working_hours', true) },

                { data: 'leave_days',           name: 'leave_days',           orderable: false, searchable: false,
                render: (v) => inputCell(v, 'leave_days') },

                { data: 'no_pay_days',          name: 'no_pay_days',          orderable: false, searchable: false,
                render: (v) => inputCell(v, 'no_pay_days') },

                { data: 'late_hours',           name: 'late_hours',           orderable: false, searchable: false,
                render: (v) => inputCell(v, 'late_hours') },

                { data: 'normal_ot_hours',      name: 'normal_ot_hours',      orderable: false, searchable: false,
                render: (v) => inputCell(v, 'normal_ot_hours') },

                { data: 'double_ot_hours',      name: 'double_ot_hours',      orderable: false, searchable: false,
                render: (v) => inputCell(v, 'double_ot_hours') },

                { data: 'triple_ot_hours',      name: 'triple_ot_hours',      orderable: false, searchable: false,
                render: (v) => inputCell(v, 'triple_ot_hours') },

                { data: 'holiday_nopay_days',   name: 'holiday_nopay_days',   orderable: false, searchable: false,
                render: (v) => inputCell(v, 'holiday_nopay_days') },

                { data: 'holiday_normal_ot_hours', name: 'holiday_normal_ot_hours', orderable: false, searchable: false,
                render: (v) => inputCell(v, 'holiday_normal_ot_hours') },

                { data: 'holiday_double_ot_hours', name: 'holiday_double_ot_hours', orderable: false, searchable: false,
                render: (v) => inputCell(v, 'holiday_double_ot_hours') },

                // Hidden
                { data: 'emp_auto_id', name: 'emp_auto_id', visible: false, searchable: false },
                { data: 'emp_etfno',   name: 'emp_etfno',   visible: false, searchable: false },
            ],
        });
    }

    $('#formFilter').on('submit', function(e) {
        e.preventDefault();
        load_dt($('#company').val(), $('#department').val(), $('#month').val());
        closeOffcanvasSmoothly();
    });

    $(document).on('click', '#approve_att', async function(e) {
        e.preventDefault();

        const month = $('#month').val();
        if (!month) {
            alert('Please select a month first.');
            return;
        }

        const confirmed = await Otherconfirmation("Save all employee work rate records?");
        if (!confirmed) return;

        // Collect every visible row from ALL DataTable pages
        const rows = [];

        $('#attendtable').DataTable().rows().every(function() {
            const rowNode  = $(this.node());
            const rowData  = this.data();

            const getValue = (name) => {
                const input = rowNode.find(`input[name="${name}"]`);
                return input.length ? input.val() : '';
            };

            rows.push({
                emp_auto_id             : rowData.emp_auto_id,
                emp_etfno               : rowData.emp_etfno,
                work_days               : getValue('work_days'),
                working_hours           : getValue('working_hours'),
                leave_days              : getValue('leave_days'),
                no_pay_days             : getValue('no_pay_days'),
                late_hours              : getValue('late_hours'),
                normal_ot_hours         : getValue('normal_ot_hours'),
                double_ot_hours         : getValue('double_ot_hours'),
                triple_ot_hours         : getValue('triple_ot_hours'),
                holiday_nopay_days      : getValue('holiday_nopay_days'),
                holiday_normal_ot_hours : getValue('holiday_normal_ot_hours'),
                holiday_double_ot_hours : getValue('holiday_double_ot_hours'),
            });
        });

        $('#approve_att').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);

        $.ajax({
            url    : '{{ url("emp_work_rate_add_ko") }}',
            method : 'POST',
            data   : {
                month : month,
                rows  : rows,
                _token: $('input[name=_token]').val(),
            },
            success: function(data) {
                $('#approve_att').html('<i class="fa-light fa-clipboard-check"></i>&nbsp;Add All').prop('disabled', false);
                if (data.success) {
                    const actionObj = { icon:'fas fa-save', title:'', message: data.message, url:'', target:'_blank', type:'success' };
                    actionreload(JSON.stringify(actionObj));
                } else {
                    const actionObj = { icon:'fas fa-warning', title:'', message:'Record Error', url:'', target:'_blank', type:'danger' };
                    action(JSON.stringify(actionObj));
                }
            },
            error: function() {
                $('#approve_att').html('<i class="fa-light fa-clipboard-check"></i>&nbsp;Add All').prop('disabled', false);
                alert('An error occurred. Please try again.');
            }
        });
    });

    $('[data-toggle="offcanvas"]').on('click', function () {
        var target = $(this).data('target');
        $(target).addClass('show');
        $('body').addClass('offcanvas-open');
        $('<div class="offcanvas-backdrop fade show"></div>').appendTo('body');
    });
    $(document).on('click', '.offcanvas-backdrop', function () { closeOffcanvasSmoothly(); });
    $('[data-dismiss="offcanvas"]').on('click', function () { closeOffcanvasSmoothly(); });

    $('#btn-reset').on('click', function () {
        $('#formFilter')[0].reset();
        $('#company').val(null).trigger('change');
        $('#department').val(null).trigger('change');
    });

    function showInitialMessage() {
        $('.response').html(
            '<tr><td colspan="15" class="text-center py-5">' +
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div></td></tr>'
        );
    }

    function closeOffcanvasSmoothly(id = '#offcanvasRight') {
        const offcanvas = $(id);
        const backdrop  = $('.offcanvas-backdrop');
        offcanvas.addClass('hiding');
        backdrop.addClass('fading');
        setTimeout(() => {
            offcanvas.removeClass('show hiding');
            backdrop.remove();
            $('body').removeClass('offcanvas-open');
        }, 900);
    }
});
</script>
@endsection