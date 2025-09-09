<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.attendant&leave_nav_bar')
               
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
                    <div class="table_outer">
                        <table class="table table-striped table-bordered table-sm small" id="attendreporttable">
                            <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Name with Initial</th>
                                <th>Date</th>
                                <th>Checkin Time</th>
                                <th>CheckOut Time</th>
                                <th>Working Hours</th>
                                <th>Location</th>
                                <th>Department</th>
                                <th>Is Approved?</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <button id="approve" class="btn btn-outline-primary float-right mt-2 btn-sm"> Approve</button>
                    {{ csrf_field() }}
                </div>
            </div>

             <!-- approve modal -->
            <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Approve Late Attendances</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="message_modal"></div>
                            <form class="form-horizontal" id="formApprove">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-dark">Leave Type</label>
                                    <select name="leave_type" id="leave_type" class="form-control form-control-sm">
                                        <option value="">Select Leave Type</option>
                                        @foreach($leave_types as $leave_type)
                                            <option value="{{ $leave_type->id }}">{{ $leave_type->leave_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-approve">Approve</button>
                        </div>
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

            let msg = '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                'Please select a date and filter to load records.' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';

            $('.table_outer').css('display', 'none');
            $('#approve').css('display', 'none');
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

            //load_dt('','','','');

            function load_dt(department, company, location, date) {

                $('.alert').remove();
                $('.table_outer').css('display', 'block');
                $('#approve').css('display', 'block');

                $('#attendreporttable').DataTable({
                    "columnDefs": [
                        {
                            "targets": 1,
                            "orderable": false
                        },
                        {
                            "targets": -2,
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
                        "url": "{{url('/attendance_by_time_approve_report_list')}}",
                        "data": {
                            'department': department,
                            'company': company,
                            'location': location,
                            'date': date
                        },
                    },

                    columns: [
                        { data: 'id' ,
                            render : function ( data, type, row, meta ) {
                                return type === 'display'  ?
                                    '<label> '+
                                    '<input type="checkbox" ' +
                                    'data-id="'+data+'" ' +
                                    'data-emp_name_with_initial="'+row["emp_name_with_initial"]+'" ' +
                                    'data-date="'+row["date"]+'" ' +
                                    'data-check_in_time="'+row["check_in_time"]+'" ' +
                                    'data-check_out_time="'+row["check_out_time"]+'" ' +
                                    'data-working_hours="'+row["working_hours"]+'" ' +
                                    'data-location_id="'+row["location_id"]+'" ' +
                                    'data-dept_id="'+row["dept_id"]+'" ' +
                                    'class="cb"/> '+
                                    '</label> '
                                    :data
                            }},
                        {data: 'id'},
                        {data: 'employee_display'},
                        {data: 'date'},
                        {data: 'check_in_time'},
                        {data: 'check_out_time'},
                        {data: 'working_hours'},
                        {data: 'location'},
                        {data: 'dept_name'},
                        {data: 'is_approved'}
                    ],
                    "bDestroy": true,
                    "order": [[9, "asc"]],

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

                load_dt(department, company, location, date);
            });

            $('body').on('click', '.cb', function (){
                let id = $(this).data('id');

                let b = {};
                b["id"] = id;
                b["emp_name_with_initial"] = $(this).data('emp_name_with_initial');
                b["date"] = $(this).data('date');
                b["check_in_time"] = $(this).data('check_in_time');
                b["check_out_time"] = $(this).data('check_out_time');
                b["working_hours"] = $(this).data('working_hours');
                b["location_id"] = $(this).data('location_id');
                b["dept_id"] = $(this).data('dept_id');
                b["is_approved_int"] = $(this).data('is_approved_int');

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

            $(document).on('click', '#approve', function (e) {
                e.preventDefault();
                $('.message_modal').html('');

                $('#approveModal').modal('show');

                //#btn-approve
                $('#btn-approve').on('click', function (e) {
                    e.preventDefault();

                    $('.error_msg').remove();

                    let save_btn = $(this);
                    let leave_type = $('#leave_type').val();

                    if(leave_type == ''){
                        $('#leave_type').after('<span class="error_msg" style="color:red;">Please select leave type.</span>');
                        return false;
                    }

                    save_btn.prop("disabled", true);
                    save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...' );
                    $.ajax({
                        url: "lateAttendance_mark_as_late_approve",
                        method: "POST",
                        data: {
                            'selected_cb': selected_cb,
                            'leave_type': leave_type,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if(data.status == true){
                                $('.message_modal').html("<div class='alert alert-success'>"+data.msg+"</div>");
                                $('#attendreporttable').DataTable().ajax.reload();
                                selected_cb = [];
                                $('#approveModal').modal('hide');
                            }else{
                                $('.message_modal').html("<div class='alert alert-danger'>"+data.msg+"</div>");
                            }

                            save_btn.prop("disabled", false);
                            save_btn.html('Approve' );
                        }
                    });

                });

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

