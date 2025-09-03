<?php $page_stitle = 'Report on Employee Attendance - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Late Attendances </span>
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
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"> </span>
                                    </div>
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
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
                <div class="card-body p-0 p-2">
                    <table class="table table-striped table-bordered table-sm small" id="attendtable">
                        <thead>
                        <tr>
                            <th>Emp ID</th>
                            <th>Emp Name</th>
                            <th>Date</th>
                            <th>Check In Time</th>
                            <th>Check Out Time</th>
                            <th>Working Hours</th>
                            <th>Location</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </main>

    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#attendance_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
            $('#attendance_collapse').addClass('show');
            $('#late_attendance_link').addClass('active');

            let late_id = 0;

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



            load_dt('','','','','','');

            function load_dt(department, company, location, employee, from_date, to_date) {

                $('#attendtable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{!! route('late_attendance_list_approved') !!}",
                        "data": {'department':department, 'employee':employee, 'location': location, 'from_date': from_date, 'to_date': to_date},
                    },
                    columns: [
                        { data: 'emp_id', name: 'emp_id' },
                        { data: 'emp_name_with_initial', name: 'emp_name_with_initial' },
                        { data: 'date', name: 'date' },
                        { data: 'check_in_time', name: 'check_in_time' },
                        { data: 'check_out_time', name: 'check_out_time' },
                        { data: 'working_hours', name: 'working_hours' },
                        { data: 'location', name: 'location' },
                        { data: 'dep_name', name: 'dep_name' },
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    "bDestroy": true,
                    "order": [
                        [3, "desc"]
                    ]
                });

            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let company = $('#company').val();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                load_dt(department, company, location, employee, from_date, to_date);
            });

            $(document).on('click', '.delete_button', function () {
                late_id = $(this).data('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function (e) {
                $.ajax({
                    url: "../late_attendance/destroy/" + late_id,
                    beforeSend: function () {
                        $('#ok_button').text('Deleting...');
                    },
                    success: function (data) {//alert(data);
                        $('#confirmModal').modal('hide');
                        $('#attendtable').DataTable().ajax.reload();
                    }
                })
            });


        });
    </script>

@endsection

