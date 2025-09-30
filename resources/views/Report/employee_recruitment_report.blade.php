<?php $page_stitle = 'Report on Employees Resignation - Multi Offset HRM'; ?>
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
                    <span>Employee Recruitment Report</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card mb-2">
            <div class="card-body p-0 p-2">
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
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm">
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Employee</label>
                            <select name="employee" id="employee_f" class="form-control form-control-sm">
                                <option value="">Please Select</option>
                                @foreach ($employees as $employee){
                                    <option value="{{$employee->id}}">{{$employee->emp_name_with_initial}}</option>
                                }  
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small font-weight-bold text-dark">Type</label>
                            <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                <option value="">Please Select Type</option>
                                <option value="1">As Interviewer</option>
                                <option value="2">As Employee</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm filter-btn" id="btn-filter"><i class="fas fa-search mr-2"></i> Filter</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">

                        <div class="center-block fix-width scroll-inner" id="emptablesection">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="emptable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>FIRST INTERVIWER</th>
                                    <th>FIRST INTERVIWE DATE</th>
                                    <th>SECOND INTERVIWER</th>
                                    <th>SECOND INTERVIWE DATE</th>
                                    <th>THIRD INTERVIWER</th>
                                    <th>THIRD INTERVIWE DATE</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="center-block fix-width scroll-inner" id="interviwersection">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="tableinterviwer">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DEPARTMENT</th>
                                    <th>INTERVIWER</th>
                                    <th>INTERVIWE DATE</th>
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
    $('#employeedetailsreport').addClass('navbtnactive');
    $('#employee_f').select2({ width: '100%' });
    $('#department').select2({ width: '100%' });


    $('#emptablesection').addClass('d-none');
    $('#interviwersection').addClass('d-none');
    $('#reporttype').on('change', function () {
    let $type = $(this).val();
    if ($type == 1) {
        $('#emptablesection').addClass('d-none');
        $('#interviwersection').removeClass('d-none');
    } else{
        $('#interviwersection').addClass('d-none');
        $('#emptablesection').removeClass('d-none');
    }
});



$('#formFilter').on('submit',function(e) {
    e.preventDefault();
        let type = $('#reporttype').val();
        let employee_f = $('#employee_f').val();
        let department = $('#department').val();


        if (type == 1) {
            $('#emptablesection').addClass('d-none');
            $('#interviwersection').removeClass('d-none');

        $('#tableinterviwer').DataTable({
             "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Report on Employees Recruitment',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Report on Employees Recruitment', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Report on Employees Recruitment',
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
                    url: '{{ route("filterRecruitmentinterviwerReport") }}',
                    type: 'GET',
                    data: {
                        employee : employee_f,
                        reportType : type
                    }
                },
                columns: [{
                        data: 'interview_id',
                        name: 'interview_id'
                    },
                    {
                        data: 'emp_name_with_initial',
                        name: 'emp_name_with_initial'
                    },
                    {
                        data: 'empdepartment',
                        name: 'empdepartment'
                    },
                    {
                        data: 'interviewer_role',
                        name: 'interviewer_role'
                    },
                    {
                        data: 'interview_date',
                        name: 'interview_date'
                    }
                ],
                "bDestroy": true,
                 "order": [[ 0, "desc" ]],
            });

        } else {


            $('#interviwersection').addClass('d-none');
            $('#emptablesection').removeClass('d-none');

            $('#emptable').DataTable({
              "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Report on Employees Recruitment',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Report on Employees Recruitment', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Report on Employees Recruitment',
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
                    url: '{{ route("filterRecruitmentReport") }}',
                    type: 'GET',
                    data:  {
                        department : department,
                        employee : employee_f,
                        reportType : type
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'emp_name_with_initial',
                        name: 'emp_name_with_initial'
                    },
                    {
                        data: 'first_interviewer_name',
                        name: 'first_interviewer_name'
                    },
                    {
                        data: 'first_interview_date',
                        name: 'first_interview_date'
                    },
                    {
                        data: 'second_interviewer_name',
                        name: 'second_interviewer_name'
                    },
                    {
                        data: 'second_interview_date',
                        name: 'second_interview_date'
                    },
                    {
                        data: 'third_interviewer_name',
                        name: 'third_interviewer_name'
                    },
                    {
                        data: 'third_interview_date',
                        name: 'third_interview_date'
                    }
                ],
                "bDestroy": true,
                "order": [[ 0, "desc" ]],
            });
        }
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

} );
</script>

@endsection