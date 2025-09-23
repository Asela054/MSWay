@extends('layouts.app')

@section('content')

<main>
     <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.attendant&leave_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                    <span>Attendance & Leave</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                      
                    </div>
                    <div class="col-12">
                        <h3>Top 5 Attendance</h3>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                                id="divicestable">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departmentWithMostAttendance as $dettail)
                                    <tr>
                                        <td>{{ $dettail['dept_name'] }}</td>
                                        @php
                                            $totalAttendanceCount = is_array($dettail['attendance_count']) ? array_sum($dettail['attendance_count']) : $dettail['attendance_count'];
                                        @endphp
                                        <td>{{ $totalAttendanceCount }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>                                
                            </table>
                        </div>
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <h3>Top 5 Leaves</h3>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                                id="divicestable">
                                <thead>
                                    <tr>
                                        <th>EPF No</th>
                                        <th>Employee</th>
                                        <th>Leaves</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getmostleaves as $dettail)
                                        <tr>
                                            <td>{{ $dettail->emp_etfno }}</td>
                                            <td>{{ $dettail->emp_name_with_initial }}</td>
                                            <td>{{ $dettail->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <h3>Top 5 OT</h3>
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%"
                                id="divicestable">
                                <thead>
                                    <tr>
                                        <th>EPF No</th>
                                        <th>Employee</th>
                                        <th>Single OT</th>
                                        <th>Double OT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getmostot as $dettail)
                                        <tr>
                                            <td>{{ $dettail->emp_etfno }}</td>
                                            <td>{{ $dettail->emp_name_with_initial }}</td>
                                            <td>{{ $dettail->normaltotal }}</td>
                                            <td>{{ $dettail->doubletotal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr class="border-dark">
                    </div>
                </div>    
            </div>
        </div>
    </div>
</main>
              
@endsection


@section('script')

<script>
$(document).ready(function(){
    $('#attendant_menu_link').addClass('active');
    $('#attendant_menu_link_icon').addClass('active');
});
</script>

@endsection