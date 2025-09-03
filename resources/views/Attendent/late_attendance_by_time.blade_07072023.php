<?php $page_stitle = 'Report on Employee Attendance - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Late Attendances Mark </span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="message"></div>
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <input type="date" name="date" id="date" class="form-control-sm form-control" required>
                            </div>

                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Type</label>
                                <select name="late_type" id="late_type" class="form-control form-control-sm">
                                </select>
                            </div>

                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter">
                                    Filter
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="table-responsive table_outer">
                        <table class="table table-striped table-bordered table-sm small" id="attendreporttable">
                            <thead>
                            <tr>
                                <th></th>
                                <th>UID</th>
                                <th>Name with Initial</th>
                                <th>Date</th>
                                <th>Checkin Time</th>
                                <th>CheckOut Time</th>
                                <th>Working Hours</th>
                                <th>Location</th>
                                <th>Department</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <button id="mark_as_late" class="btn btn-outline-primary float-right mt-2 btn-sm"> Mark as Late</button>
                    {{ csrf_field() }}
                </div>
            </div>
        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#attendance_collapse').addClass('show');
            $('#late_attendance_mark_link').addClass('active');

            $('.table_outer').css('display', 'none');
            $('#mark_as_late').css('display', 'none');

            let msg = '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                'Please select a date and filter to load records.' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';

            $('.main_card').append(msg);

            let selected_cb = [];

            $("#from_date").datetimepicker({
                pickTime: false,
                minView: 2,
                format: 'yyyy-mm-dd',
                autoclose: true,
            });

            $("#to_date").datetimepicker({
                pickTime: false,
                minView: 2,
                format: 'yyyy-mm-dd',
                autoclose: true,
            });

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
                    url: '{{url("employee_list_from_attendance_sel2")}}',
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

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_from_attendance_sel2")}}',
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

            //load_dt('','','','','');

            function load_dt(department, company, location, date, late_type) {

                $('.alert').remove();
                $('.table_outer').css('display', 'block');
                $('#mark_as_late').css('display', 'block');

                $('#attendreporttable').DataTable({
                    "columnDefs": [
                        {
                            "targets": -3,
                            "orderable": false
                        }
                    ],
                    "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                    dom: 'Blfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            className: 'btn btn-default btn-sm',
                            exportOptions: {
                                columns: 'th:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'Print',
                            className: 'btn btn-default btn-sm',
                            exportOptions: {
                                columns: 'th:not(:last-child)'
                            }
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{url('/attendance_by_time_report_list')}}",
                        "data": {
                            'department': department,
                            'company': company,
                            'location': location,
                            'date': date,
                            'late_type': late_type
                        },
                    },

                    columns: [
                        { data: 'id' ,
                            render : function ( data, type, row, meta ) {
                                return type === 'display'  ?
                                    '<label> '+
                                    '<input type="checkbox" ' +
                                    'data-id="'+data+'" ' +
                                    'data-uid="'+row["uid"]+'" ' +
                                    'data-emp_name_with_initial="'+row["emp_name_with_initial"]+'" ' +
                                    'data-date="'+row["date"]+'" ' +
                                    'data-timestamp="'+row["timestamp"]+'" ' +
                                    'data-lasttimestamp="'+row["lasttimestamp"]+'" ' +
                                    'data-workhours="'+row["workhours"]+'" ' +
                                    'data-location_id="'+row["location_id"]+'" ' +
                                    'data-dept_id="'+row["dept_id"]+'" ' +
                                    'class="cb"/> '+
                                    '</label> '
                                    :data
                            }},
                        {data: 'uid'},
                        {data: 'emp_name_with_initial'},
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
                let location = $('#location').val();
                let date = $('#date').val();
                let late_type = $('#late_type').val();

                load_dt(department, company, location, date, late_type);
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

            $(document).on('click', '#mark_as_late', function (e) {
                e.preventDefault();
                let save_btn = $(this);
                let r = confirm("Mark as Late ?");
                if (r == true) {
                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                    $.ajax({
                        url: "lateAttendance_mark_as_late",
                        method: "POST",
                        data: {
                            'selected_cb': selected_cb,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                $('.message').html("<div class='alert alert-success'>"+data.msg+"</div>");
                                $('#attendreporttable').DataTable().ajax.reload();
                                selected_cb = [];
                            }else{
                                $('.message').html("<div class='alert alert-danger'>"+data.msg+"</div>");
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

        });
    </script>

@endsection

