<?php $page_stitle = 'Report on Employee No Pay Days - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Employee No Pay Report </span>
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

                            <div class="col-md-3 div_month">
                                <label class="small font-weight-bold text-dark">Month</label>
                                <input type="month" name="month" id="month" required class="form-control-sm form-control">
                            </div>

                            <div class="col">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <table class="table table-striped table-bordered table-sm small" id="ot_report_dt">
                        <thead>
                        <tr id="dt_head">
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    {{ csrf_field() }}
                </div>
            </div>
        </div>

    </main>

{{--    no_pay_days_modal--}}
    <div class="modal fade" id="no_pay_days_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">No Pay Days</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="no_pay_days_data"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#report_collapse').addClass('show');
            $('#no_pay_report_link').addClass('active');

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


            //load_dt('');
            function load_dt(department, employee, location, month){

                    $('#dt_head').html('<th>Emp ID</th> ' +
                        '<th>Emp Name</th>' +
                        '<th>Month</th> ' +
                        '<th>Work Days</th> ' +
                        '<th>Basic Salary</th>' +
                        '<th>No Pay Days</th>' +
                        '<th>Amount</th>' +
                        '<th>Location</th> ' +
                        '<th>Department</th> ' );

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
                                    //columns: 'th:not(:last-child)'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: 'Print',
                                className: 'btn btn-default btn-sm',
                                exportOptions: {
                                    //columns: 'th:not(:last-child)'
                                }
                            }
                        ],
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "{{url('/no_pay_report_list_month')}}",
                            "data": {
                                'department':department,
                                'employee':employee,
                                'location': location,
                                'month': month
                            }
                        },

                        columns: [
                            { data: 'emp_id' },
                            { data: 'emp_name_with_initial' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'no_pay_days_data.basic_salary' },
                            { data: 'view_no_pay_days_btn' },
                            { data: 'no_pay_days_data.amount' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 2, "desc" ]],
                        "fnDrawCallback": function (oSettings) {

                            //.view_no_pay_days_btn
                            $(document).on('click','.view_no_pay_days_btn',function(e){
                                e.preventDefault();
                                let emp_id = $(this).attr('data-id');
                                let month = $(this).attr('data-month');

                                $.ajax({
                                    url: "{{url('/no_pay_days_data')}}",
                                    type: "POST",
                                    data: {
                                        'emp_id': emp_id,
                                        'month': month,
                                        '_token': '{{csrf_token()}}'
                                    },
                                    success: function (data) {
                                        $('#no_pay_days_data').html(data);
                                        $('#no_pay_days_modal').modal('show');
                                    }
                                });
                            });

                        }
                    });


            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let month = $('#month').val();

                load_dt(department, employee, location, month);
            });



        });
    </script>

@endsection

