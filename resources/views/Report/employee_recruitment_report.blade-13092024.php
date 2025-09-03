<?php $page_stitle = 'Report on Employees Resignation - Multi Offset HRM'; ?>
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
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm">
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
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
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Type</label>
                            <select name="reporttype" id="reporttype" class="form-control form-control-sm">
                                <option value="">Please Select Type</option>
                                <option value="1">As Interviewer</option>
                                <option value="2">As Employee</option>
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

                        <div class="table-responsive" id="emptablesection">
                            <table class="table table-striped table-bordered table-sm small" id="emptable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name with Initial</th>
                                    <th>First Interviewer</th>
                                    <th>First Interview Date</th>
                                    <th>Second Interviewer</th>
                                    <th>Second Interview Date</th>
                                    <th>Third Interviewer</th>
                                    <th>Third Interview Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="table-responsive" id="interviwersection">
                            <table class="table table-striped table-bordered table-sm small" id="tableinterviwer">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name with Initial</th>
                                    <th>Department</th>
                                    <th>Interviewer</th>
                                    <th>Interview Date</th>
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
    $('#department').select2({ width: '100%' });
    $('#employee_f').select2({ width: '100%' });



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





    $('#formFilter').on('submit', function (e) {
        e.preventDefault();

        let $type = $('#reporttype').val();

        if ($type == 1) {
            $('#emptablesection').addClass('d-none');
            $('#interviwersection').removeClass('d-none');
            var interviewtable = $('#tableinterviwer').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100, 500, 1000],
                    [10, 25, 50, 100, 500, 1000]
                ],
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Report on Employees Recruitment - Multi Offset HRM'
                    },
                    {
                        extend: 'pdf',
                        title: 'Report on Employees Recruitment - Multi Offset HRM',
                        customize: function (doc) {
                            doc.pageSize = 'LEGAL';
                            doc.pageOrientation = 'landscape';
                            doc.content[1].layout = 'auto';
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("filterRecruitmentinterviwerReport") }}',
                    type: 'GET',
                    data: function (d) {
                        d.employee = $('#employee_f').val();
                        d.reportType = $('#reporttype').val();
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
                        data: 'emp_department',
                        name: 'emp_department'
                    },
                    {
                        data: 'interviewer_role',
                        name: 'interviewer_role'
                    },
                    {
                        data: 'interview_date',
                        name: 'interview_date'
                    }
                ]
            });

        } else {


            $('#interviwersection').addClass('d-none');
            $('#emptablesection').removeClass('d-none');
            var table = $('#emptable').DataTable({
                "lengthMenu": [
                    [10, 25, 50, 100, 500, 1000],
                    [10, 25, 50, 100, 500, 1000]
                ],
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Report on Employees Recruitment - Multi Offset HRM'
                    },
                    {
                        extend: 'pdf',
                        title: 'Report on Employees Recruitment - Multi Offset HRM',
                        customize: function (doc) {
                            doc.pageSize = 'LEGAL';
                            doc.pageOrientation = 'landscape';
                            doc.content[1].layout = 'auto';
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("filterRecruitmentReport") }}',
                    type: 'GET',
                    data: function (d) {
                        d.department = $('#department').val();
                        d.employee = $('#employee_f').val();
                        d.reportType = $('#reporttype').val();
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
                ]
            });
        }
    });





} );
</script>

@endsection