<?php $page_stitle = 'Report on Employee Leaves - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
            <div class="container-fluid">
                @include('layouts.reports_nav_bar')
               
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="card mb-2">
                <div class="card-body">
                    <form class="form-horizontal" id="formFilter">
                        <div class="form-row mb-1">
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd"
                                    >
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd"
                                    >
                                </div>
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
                    <div class="center-block fix-width scroll-inner">
                    <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="attendreporttable">
                        <thead>
                        <tr>
                            <th>UID</th>
                            <th>Name with Initial</th>
                            <th>Department</th>
                            <th>Leave From</th>
                            <th>Leave To</th>
                            <th>Leave Type</th>
                            <th>Covering Person</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    {{ csrf_field() }}
                </div>
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
            $('#employeereportmaster').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let from_date = $('#from_date');
            let to_date = $('#to_date');

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
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val()
                        }
                    },
                    cache: true
                }
            });


            load_dt('');
            function load_dt(department, employee){
                $('#attendreporttable').DataTable({
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
                        "url": "{{url('/leave_report_list')}}",
                        "data": {'department':department, 'employee':employee, 'from_date':from_date.val(), 'to_date':to_date.val()},
                    },

                    columns: [
                        { data: 'id' },
                        { data: 'emp_name_with_initial' },
                        { data: 'dept_name' },
                        { data: 'leave_from' },
                        { data: 'leave_to' },
                        { data: 'leave_type_name' },
                        { data: 'emp_covering_name' },
                        { data: 'reson' },
                        { data: 'status' }
                    ],
                    "bDestroy": true,
                    "order": [[ 0, "desc" ]],
                });
            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();

                load_dt(department, employee);
            });
        });
    </script>

@endsection

