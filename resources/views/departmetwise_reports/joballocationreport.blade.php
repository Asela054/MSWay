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
                            <label class="small font-weight-bold text-dark">Location</label>
                            <select name="company" id="company" class="form-control form-control-sm">
                                <option value="">Please Select</option>
                                @foreach ($locations as $location){
                                    <option value="{{$location->id}}">{{$location->location_name}}</option>
                                }  
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small font-weight-bold text-dark">Time Period</label><br>
                            <div class="input-group input-group-sm mb-3">
                                <input type="date" id="from_date" name="from_date" class="form-control border-right-0" required>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-sm"><i class="far fa-calendar"></i></span>
                                </div>
                                <input type="date" id="to_date" name="to_date" class="form-control" required>
                            </div>
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
                                    <th>Name with Initial</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>On Time</th>
                                    <th>Off Time</th>
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
    $('#departmentvisereport').addClass('navbtnactive');
    $('#employee_f').select2({ width: '100%' });
    $('#company').select2({ width: '100%' });



    function load_dt(location,from_date, to_date,employee_f) {
    $('#emptable').DataTable({
        "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Report on Employees Job Location - ShapeUPHRM'
            },
            {
                extend: 'pdf',
                title: 'Report on Employees Job Location - ShapeUPHRM',
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
            "url": "{{url('/getjoblocationreport')}}",
            "data": {'location': location,
                'from_date': from_date,
                'to_date': to_date,
                'employee_f': employee_f},
        },
        columns: [
            { data: 'emp_name_with_initial' },
            { data: 'attendance_date' },
            { data: 'location_name' },
            { data: 'on_time' },
            { data: 'off_time' }
        ],
        "bDestroy": true,
        "order": [[ 1, "desc" ]],
    });
}


    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let location = $('#company').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let employee_f = $('#employee_f').val();

        load_dt(location,from_date, to_date,employee_f);
    });



} );
</script>

@endsection