<?php $page_stitle = 'Report on Employees - Ansen Gas'; ?>
@extends('layouts.app')

@section('content')
<main> 
    <div class="page-header page-header-light bg-white shadow">
        <div class="container-fluid">
            <div class="page-header-content py-3">
                <h1 class="page-header-title">
                    <div class="page-header-icon"><i class="fas fa-users"></i></div>
                    <span>Emplyee Report</span>
                </h1>
            </div>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm small" id="emptable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name with Initial</th>
                                    <th>Location</th>
                                    <th>Department</th>
                                    <th>Date of Birth</th>
                                    <th>Mobile No</th>
                                    <th>Telephone</th>
                                    <th>Nic No</th>
                                    <th>Gender</th>
                                    <th>Email</th>
                                    <th>Permanent Address</th>
                                    <th>Temporary Address</th>
                                    <th>Job Category</th>
                                    <th>Job Status</th>
                                    <th>Permanent Date</th>

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

    $('#report_main_nav_link').prop('aria-expanded', 'true').removeClass('collapsed');
    $('#report_collapse').addClass('show');
    $('#employees_report_link').addClass('active');

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
        $('#emptable').DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                'excelHtml5',
                'pdfHtml5'
            ],
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{url('/employee_report_list')}}",
                "data": {'department':department},
            },
            columns: [
                { data: 'id' },
                { data: 'emp_name_with_initial' },
                { data: 'location' },
                { data: 'dept_name' },
                { data: 'emp_birthday' },
                { data: 'emp_mobile' },
                { data: 'emp_work_telephone' },
                { data: 'emp_national_id' },
                { data: 'emp_gender' },
                { data: 'emp_email' },
                { data: 'emp_address' },
                { data: 'emp_addressT' },
                { data: 'title' },
                { data: 'e_status' },
                { data: 'emp_permanent_date' }
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



} );
</script>

@endsection