
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
                <form class="form-horizontal" id="formFilter" method="POST" action="{{ url('auditgeneratetimereport') }}">
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

                    <div class="col-md-3">
                        <label class="small font-weight-bold text-dark">Date : From - To</label>
                        <div class="input-group input-group-sm mb-3">
                            <input type="date" id="from_date" name="from_date" class="form-control form-control-sm border-right-0" placeholder="yyyy-mm-dd">
                            <input type="date" id="to_date" name="to_date" class="form-control" placeholder="yyyy-mm-dd">
                        </div>
                    </div>

                    <div class="col">
                        <br>
                        <button type="submit" name="action" value="pdf" class="btn btn-sm btn-danger" style="margin-top:5px;">
                            <i class="fas fa-file-pdf"></i>&nbsp;PDF
                        </button>

                        <button type="submit" name="action" value="excel" class="btn btn-sm btn-success" style="margin-top:5px;">
                            <i class="fas fa-file-excel"></i>&nbsp;Excel
                        </button>
                    </div>
                </div>
            </form>
                </div>
            </div>
    </main>



@endsection

@section('script')

<script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById('formFilter');

            form.addEventListener('submit', function (e) {
                const clickedButton = document.activeElement;
                if (clickedButton.name === 'action') {
                    if (clickedButton.value === 'pdf') {
                        form.action = "{{ url('auditgeneratetimereport') }}";
                    } else if (clickedButton.value === 'excel') {
                        form.action = "{{ url('auditgeneratetimereportexcel') }}";
                    }
                }
            });
        });

        $(document).ready(function () {

            $('#report_menu_link').addClass('active');
            $('#report_menu_link_icon').addClass('active');
            $('#compliancereport').addClass('navbtnactive');
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

