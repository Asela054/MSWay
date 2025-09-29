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
                         <span>Incomplete Attendance</span>
                     </h1>
                 </div>
             </div>
         </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body">
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
                                <label class="small font-weight-bold text-dark">Employee</label>
                                <select name="employee" id="employee" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd"
                                           value="{{date('Y-m-d') }}"
                                           required
                                    >
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd"
                                           value="{{date('Y-m-d') }}"
                                           required
                                    >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i> Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="col-12">
                        
                        <div class="row mt-1">
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-sm float-right"
                                    id="btn_mark_as_no_pay">Mark as NO Pay Leave</button>
                            </div>
                        </div>
                        <br>
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendance_report_table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>ETF NO</th>
                                            <th>NAME</th>
                                            <th>DEPARTMENT</th>
                                            <th>DATE</th>
                                            <th>CHECK IN TIME</th>
                                            <th>CHECK OUT TIME</th>
                                            <th>WORK HOURS</th>
                                            <th>LOCATION</th>
                                        </tr>
                                    </thead>
                                    <tbody class="response">
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#attendantmaster').addClass('navbtnactive');

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
                    url: '{{url("location_list_from_attendance_sel2")}}',
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

            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            load_dt('', '', '', from_date, to_date);

            function load_dt(department, employee, location, from_date, to_date) {

                $('.response').html('');

                let element = $('.filter-btn');
                element.attr('disabled', true);
                element.html('<i class="fa fa-spinner fa-spin"></i>');

                //add loading to element button
                $(element).val('<i class="fa fa-spinner fa-spin"></i>');
                //disable
                $(element).prop('disabled', true);

                $.ajax({
                    url: "{{ route('get_incomplete_attendance_by_employee_data') }}",
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
                        element.html('Filter');
                        element.prop('disabled', false);
                        $('.response').html(res);
                    }
                });

            }

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                let department = $('#department').val();
                let employee = $('#employee').val();
                let location = $('#location').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();

                $('.info_msg').html('');

                load_dt(department, employee, location, from_date, to_date);
            });

            //document .excel-btn click event
            $(document).on('click', '#btn_mark_as_no_pay', function(e) {
                e.preventDefault();

                //btn
                let btn = $(this);
                let btn_text = $(this).html();

                let checked = [];
                //each checked checkbox
                $('.checkbox_attendance:checked').each(function() {
                    let element = $(this);

                    let etf_no = $(this).data('etf_no');
                    let date = $(this).data('date');

                    checked.push({
                        etf_no: etf_no,
                        date: date
                    });
                });

                if(checked.length > 0) {
                    $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                    $(btn).prop('disabled', true);

                    $.ajax({
                        url: "{{ route('mark_as_no_pay') }}",
                        method: "POST",
                        data: {
                            checked: checked,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (res) {

                                if (res.errors) {
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
                            if (res.success) {
                                const actionObj = {
                                    icon: 'fas fa-save',
                                    title: '',
                                    message: res.success,
                                    url: '',
                                    target: '_blank',
                                    type: 'success'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                actionreload(actionJSON);
                            }
                        }
                    });
                } else {
                    $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                    $('html, body').animate({
                        scrollTop: 100
                    }, 'fast');
                }

            });

            $('#selectAll').click(function (e) {
                var isChecked = this.checked;
                
                // Update all checkboxes
                $('#attendance_report_table').closest('table').find('td input.checkbox_attendance').prop('checked', isChecked);
                
                // Handle row coloring for all checkboxes
                $('#attendance_report_table').closest('table').find('td input.checkbox_attendance').each(function() {
                    if (isChecked) {
                        // Change row background color when selected
                        $(this).closest('tr').css('background-color', '#f7c8c8');
                    } else {
                        // Reset row background color when deselected
                        $(this).closest('tr').css('background-color', '');
                    }
                });
            });

            // Individual checkbox handler
            $('body').on('click', '.checkbox_attendance', function (){
                if($(this).is(':checked')){
                    $(this).closest('tr').css('background-color', '#f7c8c8');
                } else {
                    $(this).closest('tr').css('background-color', '');
                }
            });
        });
    </script>

@endsection


