<?php $page_stitle = 'Report on Employee No Pay Days '; ?>
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
                    <span>No Pay Report</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                                data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                    class="fas fa-filter mr-1"></i> Filter
                                Records</button><br><br>
                        </div>
                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small" id="ot_report_dt">
                                    <thead>
                                        <tr id="dt_head">
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>MONTH</th>
                                            <th>WORK DAYS</th>
                                            <th>BASIC SALARY</th>
                                            <th>BRA 1</th>
                                            <th>BRA 2</th>
                                            <th>NO PAY DAYS</th>
                                            <th>AMOUNT</th>
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

             <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                  aria-labelledby="offcanvasRightLabel">
                  <div class="offcanvas-header">
                      <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options
                      </h2>
                      <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                          <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                      </button>
                  </div>
                  <div class="offcanvas-body">
                      <ul class="list-unstyled">
                          <form class="form-horizontal" id="formFilter">
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Company</label>
                                      <select name="company" id="company" class="form-control form-control-sm">
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
                                      <label class="small font-weight-bolder text-dark">Location</label>
                                      <select name="location" id="location" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li class="mb-2">
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Employee</label>
                                      <select name="employee" id="employee" class="form-control form-control-sm">
                                      </select>
                                  </div>
                              </li>
                              <li>
                                  <div class="col-md-12">
                                      <label class="small font-weight-bolder text-dark">Month*</label>
                                      <div class="input-group input-group-sm mb-3">
                                          <input type="month" id="month" name="month"
                                              class="form-control form-control-sm" placeholder="yyyy-mm-dd" required>
                                      </div>
                                  </div>
                              </li>
                              <li>
                                  <div class="col-md-12 d-flex justify-content-between">

                                      <button type="button" class="btn btn-danger btn-sm filter-btn px-3"
                                          id="btn-reset">
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
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeereportmaster').addClass('navbtnactive');

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

            showInitialMessage()

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


            function load_dt(department, employee, location, month){
                    $('#ot_report_dt').DataTable({
                        "destroy": true,
                        "processing": true,
                        "serverSide": true,
                        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        "buttons": [{
                                extend: 'csv',
                                className: 'btn btn-success btn-sm',
                                title: 'No Pay Reports',
                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                            },
                            { 
                                extend: 'pdf', 
                                className: 'btn btn-danger btn-sm', 
                                title: 'No Pay Reports', 
                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                orientation: 'landscape', 
                                pageSize: 'legal', 
                                customize: function(doc) {
                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                }
                            },
                            {
                                extend: 'print',
                                title: 'No Pay Reports',
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
                            { data: 'employee_display' },
                            { data: 'month' },
                            { data: 'work_days' },
                            { data: 'no_pay_days_data.basic_salary' },
                            { data: 'no_pay_days_data.BRA_I' },
                            { data: 'no_pay_days_data.add_bra2' },
                            { data: 'view_no_pay_days_btn' },
                            { data: 'no_pay_days_data.amount' },
                            { data: 'b_location' },
                            { data: 'dept_name' }
                        ],
                        "bDestroy": true,
                        "order": [[ 0, "desc" ]],
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
                closeOffcanvasSmoothly();
            });

        });

    function showInitialMessage() {
        $('#ot_report_dt tbody').html(
            '<tr>' +
            '<td colspan="11" class="text-center py-5">' + // Changed colspan to 9 to match your columns
            '<div class="d-flex flex-column align-items-center">' +
            '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
            '<h4 class="text-muted mb-2">No Records Found</h4>' +
            '<p class="text-muted">Use the filter options to get records</p>' +
            '</div>' +
            '</td>' +
            '</tr>'
        );
    }

    </script>

@endsection

