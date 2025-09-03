
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
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    @foreach ($companies as $company){
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    }  
                                    @endforeach
                                </select>
                            </div>
    
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                    <option value="All">All Departments</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold text-dark">Type</label>
                                <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                    <option value="">Please Select Type</option>
                                    <option value="1">Month Wise</option>
                                    <option value="2">Date Range Wise</option>
                                </select>
                            </div>
                            <div class="col-md-3 " id="div_date_range">
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            <div class="col-md-3 " id="div_month">
                                <label class="small font-weight-bold text-dark">Month</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="month" id="selectedmonth" name="selectedmonth" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                    
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
                <div class="card-body p-0 p-2 main_card">
                    <div class="table_outer">
                        <div class="daily_table table-responsive" id="tableContainer">
                          
                        </div>

                </div>
            </div>
        </div>

    </main>

    <div class="modal fade" id="view_more_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">OT Breakdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" table-responsive">
                        <table class="table table-striped table-bordered table-sm small" id="empotview" style="width:100%">
                            <thead>
                                <th>Emp ID</th>
                                <th>Employee</th>
                                <th>Total Leave</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
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
            $('#departmentvisereport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });

            $('#div_date_range').addClass('d-none');
            $('#div_month').addClass('d-none');
            $('#reporttype').on('change', function () {
                let $type = $(this).val();
                if ($type == 1) {

                    $('#div_date_range').addClass('d-none');
                    $('#div_month').removeClass('d-none');

                } else {
                    $('#div_month').addClass('d-none');
                    $('#div_date_range').removeClass('d-none');
                }
            });

            $('#formFilter').on('submit',function(e) {
                let department = $('#department').val();
                let from_date = $('#from_date').val();
                let to_date = $('#to_date').val();
                let reporttype = $('#reporttype').val();
                let selectedmonth = $('#selectedmonth').val();
                e.preventDefault();

                $.ajax({
                    url: '{{ route("departmentwise_generateattendancereport") }}',
                    type: 'GET',
                    data: {
                        department: department,
                        from_date: from_date,
                        to_date: to_date,
                        reporttype: reporttype,
                        selectedmonth: selectedmonth
                    },
                    success: function (response) {
                        $('#tableContainer').html(response.table);
                        $('#leave_report').DataTable({});
                    },
                    error: function (xhr, status, error) {
                        alert('Error generating the report');
                        console.error("Error: " + error);
                        console.error("Response: " + xhr.responseText);
                    }
                });

               
            });

            $(document).on('click', '.view_more', function (e) {
                var depid = $(this).attr('id');

                $.ajax({
                    url: '{{ route("departmentwise_gettotalattendanceemployee") }}',
                    type: 'GET',
                    data: {
                        department: depid,
                        from_date: $('#from_date').val(),
                        to_date: $('#to_date').val(),
                        reporttype: $('#reporttype').val(),
                        selectedmonth: $('#selectedmonth').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#view_more_modal .modal-body').html(response.table);
                            $('#view_more_modal').modal('show');
                            $('#empotview').DataTable({});
                        } else {
                            alert('No data found');
                        }
                    }
                });
            });


        $('#company').on('change', function() {
            var companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: '/getdepartments/' + companyId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#department').empty(); 
                        $('#department').append('<option value="">Please Select</option>');
                        $('#department').append('<option value="All">All Departments</option>');

                        $.each(data, function(key, department) {
                            $('#department').append('<option value="' + department.id + '">' + department.name + '</option>');
                        });
                    }
                });
            } else {
                $('#department').empty();
                $('#department').append('<option value="">Please Select</option>');
            }
        });

        });

        function getMonthsBetween(startDate, endDate) {
        let months = [];
        let date = new Date(startDate);

        while (date <= endDate) {
            months.push(date.toLocaleString('default', { month: 'long', year: 'numeric' }));
            date.setMonth(date.getMonth() + 1);
        }

        return months;
    }
    </script>

@endsection

