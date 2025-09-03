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
                "lengthMenu": [
                    [10, 25, 50],
                    [10, 25, 50]
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
                "lengthMenu": [
                    [10, 25, 50],
                    [10, 25, 50]
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