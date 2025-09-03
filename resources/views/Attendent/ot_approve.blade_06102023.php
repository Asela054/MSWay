<?php $page_stitle = 'Report on Employee O.T. Hours - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header page-header-light bg-white shadow">
            <div class="container-fluid">
                <div class="page-header-content py-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fas fa-calendar-times"></i></div>
                        <span>Employee O.T. Approve </span>
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

                            <div class="col-md-3 div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd"
                                           required
                                    >

                                    <input type="date" id="to_date" name="to_date" class="form-control"
                                           required
                                           placeholder="yyyy-mm-dd">
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

                    <div class="info_msg"></div>

                    <div class="response">
                        <div class="alert alert-info" role="alert">
                            <span> Please select a date period to continue</span>
                        </div>
                    </div>

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
            $('#ot_approve_link').addClass('active');

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
                    url: '{{url("employee_list_sel2")}}',
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
                    url: '{{url("location_list_sel2")}}',
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

            $('#formFilter').on('submit',function(e) {
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $('.info_msg').html('');

                e.preventDefault();

                $('.response').html('');
                let btn = $(this).find('button[type="submit"]');
                //btn.attr('disabled',true);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ route('get_ot_details') }}",
                    method: "POST",
                    data: {
                        department: department,
                        employee: employee,
                        location: location,
                        from_date: from_date,
                        to_date: to_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {

                        btn.html('Filter');
                        btn.prop('disabled', false);

                        let ot_data = res.ot_data;

                        let ot_data_html = '<div class="alert alert-info">';
                        ot_data_html += '<span> Please check each rows and click Approve button in bottom of the table.</span>';
                        ot_data_html += '</div>';

                        ot_data_html = '<div class="table-responsive">';

                        ot_data_html += '<table class="table table-bordered table-striped" id="ot_table">';
                        ot_data_html += '<thead>';
                        ot_data_html += '<tr>';
                        ot_data_html += '<th></th>';
                        ot_data_html += '<th>Emp ID</th>';
                        ot_data_html += '<th>ETF NO</th>';
                        ot_data_html += '<th>Employee</th>';
                        ot_data_html += '<th>Date</th>';
                        ot_data_html += '<th>Day</th>';
                        ot_data_html += '<th>From</th>';
                        ot_data_html += '<th>To</th>';
                        ot_data_html += '<th>OT Time</th>';
                        ot_data_html += '<th>D/OT Time</th>';
                        ot_data_html += '<th>Is Holiday</th>';
                        ot_data_html += '</tr>';
                        ot_data_html += '</thead>';
                        ot_data_html += '<tbody>';


                        if(ot_data.length > 0) {
                            ot_data.forEach(function(key, data) {

                                 //iterate through key
                                for(let i in key) {
                                    let obj = key[i];

                                    let is_holiday = obj.is_holiday;

                                    if(is_holiday == 1) {
                                        is_holiday = 'Yes';
                                    } else {
                                        is_holiday = 'No';
                                    }

                                    let from_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+ obj.from_24+'" >';
                                    let to_input = '<input type="datetime-local" class="form-control form-control-sm" placeholder="YYYY-MM-DD HH:MM" value="'+obj.to_24+'" >';
                                    let hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.hours+'" step=".01">';
                                    let double_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.double_hours+'" step=".01">';
                                    //let one_point_five_hours_input = '<input type="number" class="form-control form-control-sm" value="'+obj.one_point_five_ot_hours+'" step=".01">';

                                    ot_data_html += '<tr>';
                                    ot_data_html += '<td><input type="checkbox" class="cb" ' +
                                        'data-emp_id="'+obj.emp_id+'" ' +
                                        'data-date="'+obj.date+'" ' +
                                        ' /></td>';

                                    ot_data_html += '<td>'+obj.emp_id+'</td>';
                                    ot_data_html += '<td>'+obj.etf_no+'</td>';
                                    ot_data_html += '<td>'+obj.name+'</td>';
                                    ot_data_html += '<td>'+obj.date+'</td>';
                                    ot_data_html += '<td>'+obj.day_name+'</td>';
                                    ot_data_html += '<td>'+from_input+'</td>';
                                    ot_data_html += '<td>'+to_input+'</td>';
                                    ot_data_html += '<td>'+ hours_input +'</td>';
                                    ot_data_html += '<td>'+ double_hours_input +'</td>';
                                    ot_data_html += '<td>'+is_holiday+'</td>';
                                    ot_data_html += '</tr>';
                                }

                            });
                        }

                        ot_data_html += '</tbody>';
                        ot_data_html += '</table>' +
                            '<div class="form-group mt-3 ">' +
                            '<button type="button" class="btn btn-primary btn-sm float-right" id="btn_approve_ot">Approve</button>' +
                            '</div>';
                        ot_data_html += '</div>';

                        $('.response').html(ot_data_html);

                        btn.html('Filter');
                        btn.prop('disabled', false);

                        $('.date_time').datetimepicker({
                            format:'Y-m-d H:i',
                            mask:false,
                        });

                    }

                });

            });

            //document btn_approve_ot click
            $(document).on('click', '#btn_approve_ot', function(e) {

                let btn = $(this);

                let cb = $('.cb');
                let ot_data = [];

                cb.each(function(e1) {
                    let cb_obj = $(this);
                    if(cb_obj.is(':checked')) {
                        let emp_id = cb_obj.data('emp_id');
                        let date = cb_obj.data('date');
                        let from_input = cb_obj.parent().parent().find('td:nth-child(7) input');
                        let to_input = cb_obj.parent().parent().find('td:nth-child(8) input');
                        let hours_input = cb_obj.parent().parent().find('td:nth-child(9) input');
                        //let one_point_five_hours_input = cb_obj.parent().parent().find('td:nth-child(9) input');
                        let double_hours_input = cb_obj.parent().parent().find('td:nth-child(10) input');

                        let from = from_input.val();
                        let to = to_input.val();
                        let hours = hours_input.val();
                        //let one_point_five_hours = one_point_five_hours_input.val();
                        let double_hours = double_hours_input.val();
                        let is_holiday = cb_obj.parent().parent().find('td:nth-child(11)');

                        let ot_data_obj = {
                            emp_id: emp_id,
                            date: date,
                            from: from,
                            to: to,
                            hours: hours,
                            //one_point_five_hours: one_point_five_hours,
                            double_hours: double_hours,
                            is_holiday: is_holiday.text()
                        }

                        ot_data.push(ot_data_obj);

                    }

                });

                if(ot_data.length > 0) {
                    $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                    $(btn).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('ot_approve_post') }}",
                        method: "POST",
                        data: {
                            ot_data: ot_data,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (res) {
                            if(res.success) {
                                $('.info_msg').html('<div class="alert alert-success">' + res.success + '</div>');
                                $('.cb:checked').each(function() {
                                     $(this).parent().parent().remove();
                                });
                            }

                            btn.html('Approve');
                            btn.prop('disabled', false);

                            //scroll to top
                            $('html, body').animate({
                                scrollTop: 100
                            }, 'fast');
                        }
                    });
                } else {
                    $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                    $('html, body').animate({
                        scrollTop: 100
                    }, 'fast');
                }


            });

        });

    </script>

@endsection