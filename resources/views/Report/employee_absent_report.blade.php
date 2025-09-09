<?php $page_stitle = 'Report on Employees Resignation - '.$company_name.''; ?>
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
                        {{-- <div class="col">
                            <label class="small font-weight-bold text-dark">Company</label>
                            <select name="company" id="company" class="form-control form-control-sm">
                            </select>
                        </div> --}}
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Date From</label>
                           <input type="date" name="selectdatefrom" id="selectdatefrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Date To</label>
                           <input type="date" name="selectdateto" id="selectdateto" class="form-control form-control-sm">
                        </div>
                        <div class="col-3">
                            <label class="small font-weight-bold text-dark">Department</label>
                            <select name="department" id="department" class="form-control form-control-sm" required>
                                <option value="">Please Select</option>
                                <option value="All">All Departments</option>
                                @foreach ($departments as $department){
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                }  
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <br>
                            <button type="button" class="btn btn-primary btn-sm filter-btn" id="btn-filter"> Filter</button>
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
                                    <th>Date</th>
                                    <th>EMP ID</th>
                                    <th>Name with Initial</th>
                                    <th>Location</th>
                                    <th>Department</th>
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

    $('#department').select2({
    width: '100%'
    });


    $('#btn-filter').click(function() {
        let selectdatefrom = $('#selectdatefrom').val();
        let selectdateto = $('#selectdateto').val();
        let department = $('#department').val();
        let departmentname = $('#department option:selected').text();

            if(!selectdatefrom){
                $('#selectdatefrom').focus();
                return false;
            }
            if(!selectdateto){
                $('#selectdateto').focus();
                return false;
            }
            if(!department){
                $('#department').focus();
                return false;
            }
       

        $('#emptable').DataTable({
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            dom: 'Blfrtip',
            buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Report on Employees Absent ('+selectdatefrom+'-'+selectdateto+') '+departmentname+' -<?php echo $company_name; ?>'
                            },
                            {
                            extend: 'pdf',
                            title: 'Report on Employees Absent ('+selectdatefrom+'-'+selectdateto+') '+departmentname+' -<?php echo $company_name; ?>',
                            customize: function(doc) {
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
                "url": "{{url('/get_absent_employees')}}",
                "data": {'selectdatefrom':selectdatefrom,
                         'selectdateto':selectdateto,
                         'department':department
                },
            },
            columns: [
                { data: 'date' },
                { data: 'emp_id' },
                { data: 'employee_display' },
                { data: 'departmentname' },
                { data: 'location' },
            ],
            "bDestroy": true,
            "order": [[ 0, "desc" ]],
        });
    });




} );
</script>

@endsection