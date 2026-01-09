
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
                        <span>Employee Time In-Out Report</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2">
                    <form class="form-horizontal" id="formFilter"  method="POST" action="{{ url('employeeattendancereportgenerate') }}">
                        {{ csrf_field() }}
                        <div class="form-row mb-1">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Company</label>
                                <select name="company" id="company" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
    
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Department</label>
                                <select name="department" id="department" class="form-control form-control-sm">
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                            <div class="col-md-3" >
                                <label class="small font-weight-bold text-dark">Date : From - To</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0"
                                           placeholder="yyyy-mm-dd">
                                    <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>
                            {{-- <div class="col-md-3" >
                                <label class="small font-weight-bold text-dark">Paginattion Range</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="from_range" name="from_range" class="form-control form-control-sm border-right-0">
                                    <input type="number" id="to_range" name="to_range" class="form-control">
                                </div>
                            </div> --}}
                            <div class="col">
                                <br>
                                <button type="submit" id="pdf_excel" class="btn btn-sm btn-danger" style="margin-top:5px;"><i class="fas fa-file-pdf" ></i>&nbsp;Download Time In-Out Report</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
    </main>



@endsection

@section('script')

    <script>
        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#employeedetailsreport').addClass('navbtnactive');
            $('#department').select2({ width: '100%' });

            let company = $('#company');
            let department = $('#department');

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

