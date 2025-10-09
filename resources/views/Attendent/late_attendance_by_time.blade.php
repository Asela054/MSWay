<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
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
                         <span>Late Attendance Mark</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
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
                                <div class="col-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="mark_as_late" class="btn btn-primary float-right mt-2 btn-sm px-3"><i class="fas fa-plus mr-2"></i>Mark as Late</button>
                                </div>
                            </div>
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendreporttable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>DATE</th>
                                            <th>CHECK IN TIME</th>
                                            <th>CHECK OUT TIME</th>
                                            <th>WORKING HOURS</th>
                                            <th>LOCATION</th>
                                            <th>DEPARTMENT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ csrf_field() }}
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
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Company</label>
                                    <select name="company" id="company" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Department</label>
                                    <select name="department" id="department" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Employee</label>
                                    <select name="employee" id="employee" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date </label>

                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd">
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Late Type</label>
                                    <select name="late_type" id="late_type" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-md filter-btn px-3"
                                        id="btn-filter">
                                        <i class="fas fa-search mr-2"></i>Search
                                    </button>
                                    <button type="button" class="btn btn-danger btn-md filter-btn px-3" id="btn-reset">
                                        <i class="fas fa-redo mr-1"></i> Reset
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

            let selected_cb = [];

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');
            let late_type = $('#late_type');

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
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
                    data: function (params) {
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

            late_type.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("late_types_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

             load_dt('', '', '', '', '', '', true);

            function load_dt(department, company, employee, from_date,to_date, late_type,isInitialLoad = false) {
                $('#attendreporttable').DataTable({
                    "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Late Attendance  Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Late Attendance  Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Late Attendance   Information',
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
                        "url": "{{url('/attendance_by_time_report_list')}}",
                        "data": {
                            'department': department,
                            'company': company,
                            'employee': employee,
                            'from_date': from_date,
                            'to_date': to_date,
                            'late_type': late_type
                        },
                    },
                     "language": {
                                "emptyTable": isInitialLoad ? 
                                    "<div class='text-center py-4'>" +
                                    "<h5 class='text-muted'>No records to display</h5>" +
                                    "<p class='text-muted small'>Please use the filter options to get records</p>" +
                                    "</div>" : 
                                    "No data available in table"
                            },

                    columns: [
                        { 
                            data: 'id',
                            render: function (data, type, row, meta) {
                                if (type === 'display') {
                                    if (row["is_late_marked"] == 1) {
                                        return '<i class="fa fa-check text-success"></i>';
                                    } else {
                                        return '<label>' +
                                            '<input type="checkbox"' +
                                            'data-id="'+data+'" ' +
                                            'data-uid="'+row["uid"]+'" ' +
                                            'data-emp_name_with_initial="'+row["emp_name_with_initial"]+'" ' +
                                            'data-date="'+row["date"]+'" ' +
                                            'data-timestamp="'+row["timestamp"]+'" ' +
                                            'data-lasttimestamp="'+row["lasttimestamp"]+'" ' +
                                            'data-workhours="'+row["workhours"]+'" ' +
                                            'data-location_id="'+row["location_id"]+'" ' +
                                            'data-dept_id="'+row["dept_id"]+'" ' +
                                            'class="cb"/>' +
                                            '</label>';
                                    }
                                }
                                return data;
                            }
                        },
                        {data: 'uid'},
                        {data: 'employee_display'},
                        {data: 'date'},
                        {data: 'timestamp'},
                        {data: 'lasttimestamp'},
                        {data: 'workhours'},
                        {data: 'location'},
                        {data: 'dept_name'}
                    ],
                    "bDestroy": true,
                    "order": [[0, "desc"]],
                    "createdRow": function( row, data, dataIndex ) {
                        let timestamp = data['timestamp'];
                        let end_time = '08:31:00'

                        let time_arr = timestamp.split(" ");
                        let start_time = time_arr[1];

                        let dt = new Date();
                        //convert both time into timestamp
                        let stt = new Date((dt.getMonth() + 1) + "/" + dt.getDate() + "/" + dt.getFullYear() + " " + start_time);

                        stt = stt.getTime();
                        let endt = new Date((dt.getMonth() + 1) + "/" + dt.getDate() + "/" + dt.getFullYear() + " " + end_time);
                        endt = endt.getTime();

                        if ( stt > endt ) {
                            $(row).addClass('bg-danger-soft');
                        }},

                    "drawCallback": function( settings ) {
                        check_changed_text_boxes();
                    }
                });
            }

            $('#formFilter').on('submit', function (e) {
                e.preventDefault();
                let department = $('#department').val();
                let company = $('#company').val();
                let employee = $('#employee').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let late_type = $('#late_type').val();

                 load_dt(department, company, employee, from_date, to_date, late_type, false);
                 closeOffcanvasSmoothly();
            });

            $('body').on('click', '.cb', function (){
                let id = $(this).data('id');

                let b = {};
                b["id"] = id;
                b["uid"] = $(this).data('uid');
                b["emp_name_with_initial"] = $(this).data('emp_name_with_initial');
                b["date"] = $(this).data('date');
                b["timestamp"] = $(this).data('timestamp');
                b["lasttimestamp"] = $(this).data('lasttimestamp');
                b["workhours"] = $(this).data('workhours');
                b["location_id"] = $(this).data('location_id');
                b["dept_id"] = $(this).data('dept_id');

                if($(this).is(':checked')){
                    if(jQuery.inArray(b, selected_cb) === -1){
                        selected_cb.push(b);

                        let selector = $('.cb[data-id="' + id + '"]');
                        selector.parent().parent().parent().css('background-color', '#f7c8c8');
                    }
                }else {
                    removeA(selected_cb, id)
                }
                //show_selected_po_nos(selected_cb)
            });

            $(document).on('click', '#mark_as_late',async function (e) {

                e.preventDefault();
                let save_btn = $(this);
                let late_type = $('#late_type').val();

                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {
                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                    $.ajax({
                        url: "lateAttendance_mark_as_late",
                        method: "POST",
                        data: {
                            'selected_cb': selected_cb,
                            late_type: late_type,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                     const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message:'Late Attendance Marked',
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        actionreload(actionJSON);
                            }else{
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
                            save_btn.prop("disabled", false);
                            save_btn.html('Mark as Late' );
                        }
                    });
                }


            });

            function removeA(arr, id) {
                $.each(arr , function(index, val) {
                    if(id == val.id){
                        //remove val
                        selected_cb.splice(index,1);
                        let selector = $('.cb[data-id="' + id + '"]');
                        selector.parent().parent().parent().css('background-color', 'inherit');
                    }
                });
            }

            function check_changed_text_boxes(){
                for(let a = 0; a < selected_cb.length; a++){
                    let id = selected_cb[a]['id'];
                    let selector = $('.cb[data-id="' + id + '"]');

                    selector.prop("checked", true);
                    selector.parent().parent().parent().css('background-color', '#f7c8c8');
                }
            }


             $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
                 $('#employee').val(null).trigger('change');
                 $('#location').val(null).trigger('change');
             });

        });
        $('body').on('click', '#selectAll', function () {
            let isChecked = $(this).is(':checked');
            let table = $('#attendreporttable').DataTable();

            // Clear existing selection
            selected_cb = [];

            // Loop through all rows in the DataTable
            table.rows().every(function () {
                let row = this.node();
                let checkbox = $(row).find('.cb');

                // Only process checkboxes that are not already marked as late
                if (checkbox.length > 0 && !checkbox.closest('td').find('.fa-check').length) {
                    if (isChecked) {
                        // Check the checkbox
                        checkbox.prop('checked', true);

                        // Add to selected_cb array
                        let b = {};
                        b["id"] = checkbox.data('id');
                        b["uid"] = checkbox.data('uid');
                        b["emp_name_with_initial"] = checkbox.data('emp_name_with_initial');
                        b["date"] = checkbox.data('date');
                        b["timestamp"] = checkbox.data('timestamp');
                        b["lasttimestamp"] = checkbox.data('lasttimestamp');
                        b["workhours"] = checkbox.data('workhours');
                        b["location_id"] = checkbox.data('location_id');
                        b["dept_id"] = checkbox.data('dept_id');

                        if (jQuery.inArray(b, selected_cb) === -1) {
                            selected_cb.push(b);
                        }
                        // Apply background color
                        $(row).css('background-color', '#f7c8c8');
                    } else {
                        // Uncheck the checkbox
                        checkbox.prop('checked', false);
                        // Remove from selected_cb array by ID
                        selected_cb = selected_cb.filter(item => item.id !== checkbox.data('id'));
                        // Remove background color
                        $(row).css('background-color', '');
                    }
                }
            });
            // Update the select all checkbox state
            $(this).prop('checked', isChecked);
        });

         
    </script>

@endsection

