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
            <div class="card">
                <div class="card-body p-0 p-2">
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
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-sm float-right px-3"
                                    id="btn_mark_as_no_pay"><i class="fas fa-plus mr-2"></i>Mark as NO Pay Leave</button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="attendance_report_table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID</th>
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
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> From Date* </label>
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}"
                                           required>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark"> To Date*</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        placeholder="yyyy-mm-dd"  value="{{date('Y-m-d') }}" required>
                                </div>
                            </li>
                            <li>
                                <div class="col-md-12 d-flex justify-content-between">
                                    
                                    <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
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

                load_dt(department, employee, location, from_date, to_date);
                closeOffcanvasSmoothly();
            });

           

             $('#btn-reset').on('click', function () {
                 $('#formFilter')[0].reset();
                 $('#company').val(null).trigger('change');
                 $('#department').val(null).trigger('change');
                 $('#employee').val(null).trigger('change');
                 $('#location').val(null).trigger('change');
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

                    let empid = $(this).data('empid');
                    let date = $(this).data('date');

                    checked.push({
                        empid: empid,
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
          function closeOffcanvasSmoothly(offcanvasId = '#offcanvasRight') {
             const offcanvas = $(offcanvasId);
             const backdrop = $('.offcanvas-backdrop');

             // Add hiding class to trigger reverse animation
             offcanvas.addClass('hiding');
             backdrop.addClass('fading');

             // Remove elements after animation completes
             setTimeout(() => {
                 offcanvas.removeClass('show hiding');
                 backdrop.remove();
                 $('body').removeClass('offcanvas-open');
             }, 900); // Match this with your CSS transition duration
         }
    </script>

@endsection


