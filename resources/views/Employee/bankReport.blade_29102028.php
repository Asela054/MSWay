<?php $page_stitle = 'Report on Employee Bank Accounts - Multi Offset'; ?>
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
                                <select name="department" id="department" class="form-control form-control-sm" required>
                                </select>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dt1">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Office</th>
                                    <th>Department</th>
                                    <th>Bank</th>
                                    <th>Branch</th>
                                    <th>Account No</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection


@section('script')
<script>
    $(document).ready(function() {

        $('#report_menu_link').addClass('active');
        $('#report_menu_link_icon').addClass('active');
        $('#employeereportmaster').addClass('navbtnactive');

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

        load_dt('');
        function load_dt(department){
            $('#dt1').DataTable({
                "columnDefs": [ {
                    "targets": -1,
                    "orderable": false
                } ],
                "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                dom: 'Blfrtip',
                buttons: [
                    'excelHtml5',
                    'pdfHtml5'
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{url('/bank_report_list')}}",
                    "data": {'department':department},
                },
                columns: [
                    { data: 'id' },
                    { data: 'emp_name_with_initial' },
                    { data: 'location' },
                    { data: 'dept_name' },
                    { data: 'bank' },
                    { data: 'branch' },
                    { data: 'bank_ac_no' },
                ],
                "bDestroy": true,
                "order": [[ 0, "desc" ]],
            });
        }

        $('#formFilter').on('submit',function(e) {
            e.preventDefault();
            let department = $('#department').val();

            load_dt(department);
        });
    });
</script>
@endsection