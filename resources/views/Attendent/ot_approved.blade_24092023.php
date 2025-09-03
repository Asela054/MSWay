<?php $page_stitle = 'Report on Employee O.T. Hours - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Approved O.T. </span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Location</label>
                                <select name="location" id="location" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
{{--                            <div class="col-md-3">--}}
{{--                                <label class="small font-weight-bold text-dark">Type</label>--}}
{{--                                <select name="type" id="type" class="form-control form-control-sm">--}}
{{--                                    <option value="Daily">Daily</option>--}}
{{--                                    <option value="Monthly">Monthly</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-3 div_month">--}}
{{--                                <label class="small font-weight-bold text-dark">Month</label>--}}
{{--                                <input type="month" name="month" id="month" class="form-control-sm form-control">--}}
{{--                            </div>--}}

                            <div class="col-md-3 mt-3 div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd"
                                    >
                                    {{--                                    <div class="input-group-prepend">--}}
                                    {{--                                        <span class="input-group-text" id="inputGroup-sizing-sm"><i class="far fa-calendar"></i></span>--}}
                                    {{--                                    </div>--}}
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col mt-3">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="info_msg"></div>
                    <div class="daily_table">
                        <table class="table table-striped table-bordered table-sm small" id="ot_report_dt">
                            <thead>
                            <tr id="dt_head">
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div class="month_table">
                        <table class="table table-striped table-bordered table-sm small" id="ot_report_monthly_dt">
                            <thead>
                            <tr id="dt_head_month">
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    {{ csrf_field() }}
                </div>
            </div>
        </div>

    </main>

    <div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">OT Breakdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="viewRes"></div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#attendance_collapse').addClass('show');
            $('#approved_ot_link').addClass('active');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');
            let type = $('#type');

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

            type.on('change',function(e) {
                let type_val = $(this).val();
                if(type_val == 'Daily'){
                    $('.div_month').css('display','none');
                    $('.div_date_range').css('display','block');
                    $('#month').val('');
                }else{
                    $('.div_month').css('display','block');
                    $('.div_date_range').css('display','none');
                    $('#from_date').val('');
                    $('#to_date').val('');
                }
            });
            $('.div_month').css('display','none');

            load_dt('');
            function load_dt(department, employee, location, from_date, to_date, type = 'Daily', month){

                if(type == 'Daily'){

                    $('.month_table').css('display','none');
                    $('.daily_table').css('display','block');

                    $('#dt_head').html('<th>ETF No</th> ' +
                        '<th>Emp Name</th>' +
                        '<th>Date</th> ' +
                        '<th>From</th> ' +
                        '<th>To</th>' +
                        '<th>Hours</th>' +
                        '<th>1.5 Hours</th>' +
                        '<th>Double Hours</th> ' +
                        '<th>Is Holiday</th> ' +
                        '<th>Location</th> ' +
                        '<th>Department</th> ' +
                        '<th> Action </th> ' );

                    $('#ot_report_dt').DataTable({
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
                            "url": "{{url('/ot_approved_list')}}",
                            "data": {'department':department,
                                'employee':employee,
                                'location': location,
                                'from_date': from_date,
                                'to_date': to_date,
                                'type': type,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'emp_name_with_initial' },
                            { data: 'date' },
                            { data: 'from' },
                            { data: 'to' },
                            { data: 'hours' },
                            { data: 'one_point_five_hours' },
                            { data: 'double_hours' },
                            { data: 'is_holiday' },
                            { data: 'b_location' },
                            { data: 'dept_name' },
                            {data: 'action'}
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }
                else if(type == 'Monthly'){

                    $('.month_table').css('display','block');
                    $('.daily_table').css('display','none');

                    $('#dt_head_month').html('<th>Emp ID</th> ' +
                        '<th>Emp Name</th>' +
                        '<th>Month</th> ' +
                        '<th>Work Days</th> ' +
                        '<th>Leave Days</th>' +
                        '<th>No Pay Days</th>' +
                        '<th>O.T. Hours</th> ' +
                        '<th>Double O.T. Hours</th> ' +
                        '<th>Location</th> ' +
                        '<th>Department</th> ' );

                    $('#ot_report_monthly_dt').DataTable({
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
                            "url": "{{url('/ot_report_list_month')}}",
                            "data": {
                                'department':department,
                                'employee':employee,
                                'location': location,
                                'from_date': from_date,
                                'to_date': to_date,
                                'type': type,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'emp_name_with_initial' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'leave_days' },
                            { data: 'no_pay_days' },
                            { data: 'normal_rate_otwork_hrs' },
                            { data: 'double_rate_otwork_hrs' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                    });
                }


            }

            $('#formFilter').on('submit',function(e) {
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let type = $('#type').val();
                let month = $('#month').val();

                if (type == 'Monthly'){
                    if (month == ''){
                        alert('Please select month');
                        return false;
                    }
                }

                e.preventDefault();

                load_dt(department, employee, location, from_date, to_date, type, month);
            });

            $(document).on('click','.delete_btn',function(e){

                e.preventDefault();
                let id = $(this).data('id');
                let btn = $(this);

                if(confirm("Are you sure you want to delete this?")){

                    btn.attr('disabled',true);
                    btn.html('<i class="fa fa-spinner fa-spin"></i>');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{url('/ot_approved_delete')}}",
                        type: "POST",
                        data: {
                            'id': id
                        },
                        success: function (data) {
                            btn.attr('disabled',false);
                            btn.html('<i class="fa fa-trash"></i>');
                            $('.info_msg').html('<div class="alert alert-success">'+data.msg+'</div>');
                            load_dt();

                        },
                        error: function (data) {
                            btn.attr('disabled',false);
                            btn.html('<i class="fa fa-trash"></i>');
                            console.log(data);
                        }

                    });

                }
                else{
                    return false;
                }


            });

        });
    </script>

@endsection

