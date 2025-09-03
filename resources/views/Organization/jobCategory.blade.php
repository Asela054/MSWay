@extends('layouts.app')

@section('content')

<main>
    <div class="page-header shadow">
        <div class="container-fluid">
            @include('layouts.corporate_nav_bar')
           
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                    @can('company-create')
                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" name="create_record" id="create_record"><i class="fas fa-plus mr-2"></i>Add Job Category</button>
                    @endcan
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID </th>
                                    <th>Category</th>
                                    <th>Annual Leaves</th>
                                    <th>Casual Leaves</th>
                                    <th>Medical Leaves</th>
                                    <th>Payroll Work Days </th>
                                    <th>Payroll Work Hours</th>
                                    <th>OT Hours</th>
                                    <th>Holiday OT Minimum minits</th>
                                    <th>OT Deduction Precentage</th>
                                    <th>Shift Hours</th>
                                    <th>Working Type</th>
                                    <th>Morning OT Applicable</th>
                                    <th>Lunch Deduction</th>
                                    <th>Lunch Deduction Minutes</th>
                                    <th>Holiday Work Hours</th>
                                    <th>Late Type</th>
                                    <th>Saturday OT Type</th>
                                    <th>Custom Saturday OT Rate</th>
                                    <th>Sunday OT Type</th>
                                    <th>Custom Sunday OT Rate</th>
                                    <th>Special Day 01 OT Day</th>
                                    <th>Special Day 01 OT Type</th>
                                    <th>Custom Special Day 01 OT Rate</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach($jobcategory as $jobcategories)
                                <tr>
                                    <td>{{$jobcategories->id}}</td>
                                    <td>{{$jobcategories->category}}</td>
                                    <td>{{$jobcategories->annual_leaves}}</td>
                                    <td>{{$jobcategories->casual_leaves}}</td>
                                    <td>{{$jobcategories->medical_leaves}}</td>
                                    <td>{{$jobcategories->emp_payroll_workdays}}</td>
                                    <td>{{$jobcategories->emp_payroll_workhrs}}</td>
                                    <td>{{$jobcategories->ot_app_hours}}</td>
                                    <td>{{$jobcategories->holiday_ot_minimum_min}}</td>
                                    <td>{{$jobcategories->spe_deduct_pre}}</td>
                                    <td>{{$jobcategories->shift_hours}}</td>
                                    <td>
                                        <?php
                                        if($jobcategories->work_hour_date == 'Date'){
                                            echo 'Dates';
                                        }elseif ($jobcategories->work_hour_date == 'Hour'){
                                            echo 'Hours';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($jobcategories->morning_ot === 0){
                                            echo 'No';
                                        }elseif ($jobcategories->morning_ot == 1){
                                            echo 'Yes';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($jobcategories->lunch_deduct_type === 0){
                                            echo 'No';
                                        }elseif ($jobcategories->lunch_deduct_type == 1){
                                            echo 'Yes';
                                        }
                                        ?>
                                    </td>
                                    <td>{{$jobcategories->lunch_deduct_min}}</td>
                                    <td>{{$jobcategories->holiday_work_hours}}</td>
                                    <td>
                                        <?php
                                        if($jobcategories->late_type == 1){
                                            echo 'Late Per Minutes';
                                        }elseif ($jobcategories->late_type == 2){
                                            echo 'Late Leave';
                                        }elseif ($jobcategories->late_type == 3){
                                            echo 'Custom';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($jobcategories->is_sat_ot_type_as_act == 1){
                                            echo 'As Act';
                                        }elseif ($jobcategories->is_sat_ot_type_as_act === 0){
                                            echo 'Custom';
                                        }else{
                                            echo 'Working Day';
                                        }
                                        ?>
                                    </td>
                                    <td>{{$jobcategories->custom_saturday_ot_type}}</td>
                                    <td>
                                        <?php
                                        if($jobcategories->is_sun_ot_type_as_act == 1){
                                            echo 'As Act';
                                        }elseif ($jobcategories->is_sun_ot_type_as_act === 0){
                                            echo 'Custom';
                                        }else{
                                            echo 'Working Day';
                                        }
                                        ?>
                                    </td>
                                    <td>{{$jobcategories->custom_sunday_ot_type}}</td>
                                    <td>
                                        <?php
                                        if($jobcategories->spe_day_1_day === 0){
                                            echo 'Sunday';
                                        }elseif ($jobcategories->spe_day_1_day == 1){
                                            echo 'Monday';
                                        }elseif ($jobcategories->spe_day_1_day == 2){
                                            echo 'Tuesday';
                                        }elseif ($jobcategories->spe_day_1_day == 3){
                                            echo 'Wednesday';
                                        }elseif ($jobcategories->spe_day_1_day == 4){
                                            echo 'Thursday';
                                        }elseif ($jobcategories->spe_day_1_day == 5){
                                            echo 'Friday';
                                        }
                                        elseif ($jobcategories->spe_day_1_day == 6){
                                            echo 'Saturday';
                                        }
                                        else{
                                            echo 'No Special Day';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($jobcategories->spe_day_1_type == 1){
                                            echo 'As Act';
                                        }elseif ($jobcategories->spe_day_1_type === 0){
                                            echo 'Custom';
                                        }
                                        ?>
                                    </td>
                                    <td>{{$jobcategories->spe_day_1_rate}}</td>
                                    <td class="text-right">
                                        @can('company-edit')
                                            <button style="margin:2px;" name="edit" id="{{$jobcategories->id}}" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>
                                        @endcan
                                        @can('company-delete')
                                            <button style="margin:2px;" type="submit" name="delete" id="{{$jobcategories->id}}" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                      
                       
                    </div>
                </div>    
            </div>
        </div>
    </div>

        <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Job Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}	
                                
                                <div class="form-row mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bold text-dark">Category</label>
                                        <input type="text" name="category" id="category" class="form-control form-control-sm" required />
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Annual Leaves</label>
                                        <input type="number" name="annual_leaves" id="annual_leaves" class="form-control form-control-sm" required />
                                    </div> 
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Casual Leaves</label>
                                    <input type="number" name="casual_leaves" id="casual_leaves" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Medical Leaves</label>
                                        <input type="number" name="medical_leaves" id="medical_leaves" class="form-control form-control-sm" required />
                                </div>
                                </div>

                                <div class="form-row mb-2">
{{--                                    <div class="col-md-6">--}}
{{--                                        <label class="small font-weight-bold text-dark">OT Deduct</label>--}}
{{--                                        <input type="number" name="otdeduct" step="0.01" id="otdeduct" class="form-control form-control-sm" required />--}}
{{--                                    </div>                                    --}}
{{--                                <div class="col-md-6">--}}
{{--                                    <label class="small font-weight-bold text-dark">No Pay Deduct</label>--}}
{{--                                    <input type="number" name="nopaydeduct" step="0.01" id="nopaydeduct" class="form-control form-control-sm" required />--}}
{{--                                </div>--}}
                                </div>

                                <div class="form-row mb-2">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Payroll Workdays</label>
                                    <input type="number" name="emp_payroll_workdays" step="0.01" id="emp_payroll_workdays" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Payroll Work Hours</label>
                                    <input type="number" name="emp_payroll_workhrs" step="0.01" id="emp_payroll_workhrs" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">OT Calculate Hours (After Shift)</label>
                                    <input type="number" name="ot_app_hours" step="0.01" id="ot_app_hours" class="form-control form-control-sm" required />
                                </div>
                                </div>

                                <div class="form-row mb-2">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Holiday OT Minimum minits</label>
                                    <input type="number" name="holiday_ot_minimum_min" id="holiday_ot_minimum_min" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Special OT Deduct Precentage(%)</label>
                                    <input type="number" name="spe_deduct_pre" step="0.01" id="spe_deduct_pre" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold text-dark">Shift Hours</label>
                                    <input type="number" name="shift_hours" step="0.01" id="shift_hours" class="form-control form-control-sm" required />
                                </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Holiday Work Hours</label>
                                        <input type="number" name="holiday_work_hours" id="holiday_work_hours" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">After how hours W.Days Double?</label>
                                        <input type="number" name="week_after_double" id="week_after_double" class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Working Calculation</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input work_hour_date" name="work_hour_date" id="work_hour_date_0" value="Date" checked>Dates
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input work_hour_date" name="work_hour_date" id="work_hour_date_1" value="Hour">Hours
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="form-row mb-2">
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Lunch Hours Deduct</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input lunch_deduct_type" name="lunch_deduct_type" id="lunch_deduct_type_0" value="0" checked>No
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input lunch_deduct_type" name="lunch_deduct_type" id="lunch_deduct_type_1" value="1">Yes
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 custom_lunch" style="display: none">
                                    <label class="small font-weight-bold text-dark">Lunch Deduction Minutes</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="number" class="form-control form-control-sm" name="lunch_deduct_min" id="lunch_deduct_min"/>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Salary Without Attendance</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input salary_without_attendace" name="salary_without_attendace" id="salary_without_attendace_0" value="0" checked>No
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input salary_without_attendace" name="salary_without_attendace" id="salary_without_attendace_1" value="1">Yes
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Morning OT Applicable</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input morning_ot" name="morning_ot" id="morning_ot_0" value="0" checked>No
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input morning_ot" name="morning_ot" id="morning_ot_1" value="1">Yes
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Holiday OT Start</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input holiday_ot_start" name="holiday_ot_start" id="holiday_ot_start_1" value="1" checked>As Act
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input holiday_ot_start" name="holiday_ot_start" id="holiday_ot_start_0" value="0">Shift Time
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-dark">Holiday Lunch Deduct</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input holiday_lunch_deduct" name="holiday_lunch_deduct" id="holiday_lunch_deduct_0" value="0" checked>No
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input holiday_lunch_deduct" name="holiday_lunch_deduct" id="holiday_lunch_deduct_1" value="1">Yes
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Saturday OT Type</label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sat_ot_type_as_act" name="is_sat_ot_type_as_act" id="is_sat_ot_type_as_act_1" value="1" checked>As Act
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sat_ot_type_as_act" name="is_sat_ot_type_as_act" id="is_sat_ot_type_as_act_0" value="0">Custom
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sat_ot_type_as_act" name="is_sat_ot_type_as_act" id="is_sat_ot_type_as_act_2" value="2">Saturday is a working date
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-2 custom_sat" style="display: none">
                                    <label class="small font-weight-bold text-dark">Custom Saturday OT Type</label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="custom_saturday_ot_type" id="custom_saturday_ot_type_1" value="1" checked>1
                                        </label>
                                    </div>
                                    <!-- <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="custom_saturday_ot_type" id="custom_saturday_ot_type_1_5" value="1.5">1.5
                                        </label>
                                    </div> -->
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="custom_saturday_ot_type" id="custom_saturday_ot_type_2" value="2">2
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Sunday OT Type</label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sun_ot_type_as_act" name="is_sun_ot_type_as_act" id="is_sun_ot_type_as_act_1" value="1" checked>As Act
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sun_ot_type_as_act" name="is_sun_ot_type_as_act" id="is_sun_ot_type_as_act_0" value="0">Custom
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input is_sun_ot_type_as_act" name="is_sun_ot_type_as_act" id="is_sun_ot_type_as_act_2" value="2">Sunday is a working date
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-4 custom_sun" style="display: none">
                                        <label class="small font-weight-bold text-dark">Custom Sunday OT Type</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input custom_sunday_ot_type" name="custom_sunday_ot_type" id="custom_sunday_ot_type_1" value="1">1
                                            </label>
                                        </div>
                                        <!-- <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="custom_sunday_ot_type" id="custom_sunday_ot_type_1_5" value="1.5">1.5
                                            </label>
                                        </div> -->
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input custom_sunday_ot_type" name="custom_sunday_ot_type" id="custom_sunday_ot_type_2" value="2">2
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 custom_sun_after" style="display: none">
                                    <label class="small font-weight-bold text-dark">After how many hours will it double?</label>
                                        <br>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="number" class="form-control form-control-sm" name="sun_after_double" id="sun_after_double"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Special Day 01 Day</label>
                                    <br>
                                    <select class="form-control form-control-sm" id="spe_day_1_day" name="spe_day_1_day">
                                        <option value="">Select Day</option>
                                        <option value="0">Sunday</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                    </select>
                                    <label class="small font-weight-bold text-dark">Special Day 01 OT Type</label>
                                    <br>
                                    <!-- <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input spe_day_1_type" name="spe_day_1_type" id="spe_day_1_type_1" value="1" checked>As Act
                                        </label>
                                    </div> -->
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input spe_day_1_type" name="spe_day_1_type" id="spe_day_1_type_0" value="0">Custom
                                        </label>
                                    </div>
                                    <!-- <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input spe_day_1_type" name="spe_day_1_type" id="spe_day_1_type_2" value="2">Special day is a working date
                                        </label>
                                    </div> -->
                                </div>
                                <div class="form-group mb-2 custom_spe">
                                    <label class="small font-weight-bold text-dark">Custom Special Day OT Type</label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="spe_day_1_rate" id="spe_day_1_rate_1" value="1">1
                                        </label>
                                    </div>
                                    <!-- <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="spe_day_1_rate" id="spe_day_1_rate_1_5" value="1.5">1.5
                                        </label>
                                    </div> -->
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="spe_day_1_rate" id="spe_day_1_rate_2" value="2">2
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Late Type</label>
                                    <br>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input late_type" name="late_type" id="late_type_1" value="1">Late Per Minutes
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input late_type" name="late_type" id="late_type_2" value="2">Late Leave
                                        </label>
                                    </div>
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input late_type" name="late_type" id="late_type_3" value="3">Custom
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row mb-2">
                                    <div class="col-md-4 per_min" style="display: none">
                                        <label class="small font-weight-bold text-dark">Late Attendance Minutes</label>
                                            <br>
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="number" class="form-control form-control-sm" name="late_attend_min" id="late_attend_min"/>
                                                </label>
                                            </div>
                                    </div>
                                    <div class="col-md-4 short_lev" style="display: none">
                                        <label class="small font-weight-bold text-dark">Short Leaves</label>
                                            <br>
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="number" class="form-control form-control-sm" name="short_leaves" id="short_leaves"/>
                                                </label>
                                            </div>
                                    </div>
                                    <div class="col-md-4 half_day" style="display: none">
                                        <label class="small font-weight-bold text-dark">Half Days</label>
                                            <br>
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="number" class="form-control form-control-sm" name="half_days" id="half_days"/>
                                                </label>
                                            </div>
                                    </div>
                                </div>

                                <div class="form-group mt-2">
                                    <button type="submit" name="action_button" id="action_button" class="btn btn-outline-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col text-center">
                            <h4 class="font-weight-normal">Are you sure you want to remove this data?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" name="ok_button" id="ok_button" class="btn btn-danger px-3 btn-sm">OK</button>
                    <button type="button" class="btn btn-dark px-3 btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Area End -->
</main>
@endsection

@section('script')
<script>
$(document).ready(function(){

    $('#organization_menu_link').addClass('active');
    $('#organization_menu_link_icon').addClass('active');
    $('#jobcategorylink').addClass('navbtnactive');

    $('#dataTable').DataTable();

    $('#create_record').click(function(){
        $('.modal-title').text('Add New Job Category');
        $('#action_button').html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();

        $('.custom_lunch').hide();
        $('.custom_sat').hide();
        $('.custom_sun').hide();
        $('.custom_sun_after').hide();
        $('.per_min').hide();
        $('.short_lev').hide();
        $('.half_day').hide();

        $('#formModal').modal('show');
    });
 
    $('#formTitle').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if ($('#action').val() == 'Add') {
            action_url = "{{ route('addJobCategory') }}";
        }
        if ($('#action').val() == 'Edit') {
            action_url = "{{ route('JobCategory.update') }}";
        }

        $.ajax({
            url: action_url,
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (data) {//alert(data);

                var html = '';
                if (data.errors) {
                    html = '<div class="alert alert-danger">';
                    for (var count = 0; count < data.errors.length; count++) {
                        html += '<p>' + data.errors[count] + '</p>';
                    }
                    html += '</div>';
                }
                if (data.success) {
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#formTitle')[0].reset();
                    //$('#titletable').DataTable().ajax.reload();
                    location.reload()
                }
                $('#form_result').html(html);
            }
        });
    });

    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $('#form_result').html('');
        $.ajax({
            url: "JobCategory/" + id + "/edit",
            dataType: "json",
            success: function (data) {

                //$('#sunday_rate').val(data.result.sunday_rate);
                let short_leave_enabled = data.result.short_leave_enabled;
                if(short_leave_enabled == 1){
                    $('#short_leave_enabled').prop( "checked", true );
                }else {
                    $('#short_leave_enabled').prop( "checked", false );
                }

                $('#category').val(data.result.category);
                $('#annual_leaves').val(data.result.annual_leaves);
                $('#casual_leaves').val(data.result.casual_leaves);
                $('#medical_leaves').val(data.result.medical_leaves);
                $('#emp_payroll_workdays').val(data.result.emp_payroll_workdays);
                $('#emp_payroll_workhrs').val(data.result.emp_payroll_workhrs);
                $('#ot_app_hours').val(data.result.ot_app_hours);
                $('#holiday_ot_minimum_min').val(data.result.holiday_ot_minimum_min);
                $('#spe_deduct_pre').val(data.result.spe_deduct_pre);
                $('#shift_hours').val(data.result.shift_hours);
               // $('#otdeduct').val(data.result.otdeduct);
               // $('#nopaydeduct').val(data.result.nopaydeduct);
                $('#holiday_work_hours').val(data.result.holiday_work_hours);
                $('#week_after_double').val(data.result.week_after_double);

               if(data.result.work_hour_date == 'Date'){
                    $('#work_hour_date_0').prop( "checked", true );
                    $('.custom_work').css('display', 'none');
                }else if(data.result.work_hour_date == 'Hour') {
                    $('#work_hour_date_1').prop( "checked", true );
                    $('.custom_work').css('display', 'block');
                }

               if(data.result.morning_ot == 0){
                    $('#morning_ot_0').prop( "checked", true );
                    $('.custom_morning').css('display', 'none');
                }else if(data.result.morning_ot == 1) {
                    $('#morning_ot_1').prop( "checked", true );
                    $('.custom_morning').css('display', 'block');
                }

               if(data.result.holiday_ot_start == 0){
                    $('#holiday_ot_start_0').prop( "checked", true );
                }else if(data.result.holiday_ot_start == 1) {
                    $('#holiday_ot_start_1').prop( "checked", true );
                }

               if(data.result.holiday_lunch_deduct == 0){
                    $('#holiday_lunch_deduct_0').prop( "checked", true );
                }else if(data.result.holiday_lunch_deduct == 1) {
                    $('#holiday_lunch_deduct_1').prop( "checked", true );
                }

               if(data.result.lunch_deduct_type == 0){
                    $('#lunch_deduct_type_0').prop( "checked", true );
                    $('.custom_lunch').css('display', 'none');
                    $('#lunch_deduct_min').val(0).prop( "checked", true); 
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'lunch_deduct_min',
                        value: 0
                    }); 
                }else if(data.result.lunch_deduct_type == 1) {
                    $('#lunch_deduct_type_1').prop( "checked", true );
                    $('.custom_lunch').css('display', 'block');
                }

                $('#lunch_deduct_min').val(data.result.lunch_deduct_min);

                if(data.result.salary_without_attendace == 0){
                    $('#salary_without_attendace_0').prop( "checked", true );
                    $('.custom_salary').css('display', 'none');
                }else if(data.result.salary_without_attendace == 1) {
                    $('#salary_without_attendace_1').prop( "checked", true );
                    $('.custom_salary').css('display', 'block');
                }

                if(data.result.is_sat_ot_type_as_act == 1){
                    $('#is_sat_ot_type_as_act_1').prop( "checked", true );
                    $('.custom_sat').css('display', 'none');
                }else if(data.result.is_sat_ot_type_as_act == 0) {
                    $('#is_sat_ot_type_as_act_0').prop( "checked", true );
                    $('.custom_sat').css('display', 'block');
                }else{
                    $('#is_sat_ot_type_as_act_2').prop( "checked", true );
                    $('.custom_sat').css('display', 'none');
                }

                if(data.result.custom_saturday_ot_type == 1 ){
                    $('#custom_saturday_ot_type_1').prop( "checked", true );
                }

                if(data.result.custom_saturday_ot_type == 1.5 ){
                    $('#custom_saturday_ot_type_1_5').prop( "checked", true );
                }

                if(data.result.custom_saturday_ot_type == 2 ){
                    $('#custom_saturday_ot_type_2').prop( "checked", true );
                }

                if(data.result.is_sun_ot_type_as_act == 1){
                    $('#is_sun_ot_type_as_act_1').prop( "checked", true );
                    $('.custom_sun').css('display', 'none');
                    $('.custom_sun_after').css('display', 'none'); 
                    $('#sun_after_double').prop('required', false);
                }else if(data.result.is_sun_ot_type_as_act == 0) {
                    $('#is_sun_ot_type_as_act_0').prop( "checked", true );
                    $('.custom_sun').css('display', 'block');
                }else{
                    $('#is_sun_ot_type_as_act_2').prop( "checked", true );
                    $('.custom_sun').css('display', 'none');
                    $('.custom_sun_after').css('display', 'none'); 
                    $('#sun_after_double').prop('required', false);
                }

                if (data.result.custom_sunday_ot_type == 1) {
                    $('#custom_sunday_ot_type_1').prop("checked", true);
                    $('.custom_sun_after').css('display', 'block'); 
                    $('#sun_after_double').prop('disabled', false).val(data.result.sun_after_double);
                    $('#sun_after_double').prop('required', true); 
                } else if (data.result.custom_sunday_ot_type == 2) {
                    $('#custom_sunday_ot_type_2').prop("checked", true);
                    $('.custom_sun_after').css('display', 'none'); 
                    $('#sun_after_double').val(0).prop("checked", true); 
                    $('#sun_after_double').prop('required', false);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'sun_after_double',
                        value: 0
                    }); 
                }

                $('#sun_after_double').val(data.result.sun_after_double);

                $('#spe_day_1_day').change(function () {
                    const selectedDay = $(this).val();

                    if (selectedDay == "") { 
                        $('.spe_day_1_type').prop("checked", false); 
                        $('.custom_spe').css('display', 'none'); 
                        $('input[name="spe_day_1_rate"]').prop("checked", false); 
                    } else {                        
                        $('.custom_spe').css('display', 'block'); 
                    }
                });

                $('#spe_day_1_day').val(data.result.spe_day_1_day);

                if (data.result.spe_day_1_type == 1) {
                    $('#spe_day_1_type_1').prop("checked", true); 
                    $('.custom_spe').css('display', 'none'); 
                } else if (data.result.spe_day_1_type == 0) {
                    $('#spe_day_1_type_0').prop("checked", true); 
                    $('.custom_spe').css('display', 'block'); 
                } else {
                    console.warn('Unexpected value for spe_day_1_type:', data.result.spe_day_1_type);
                }

                if (data.result.spe_day_1_rate == 1) {
                    $('#spe_day_1_rate_1').prop("checked", true); 
                } else if (data.result.spe_day_1_rate == 2) {
                    $('#spe_day_1_rate_2').prop("checked", true); 
                } else {
                    console.warn('Unexpected value for spe_day_1_rate:', data.result.spe_day_1_rate);
                }

                if(data.result.late_type == 1){
                    $('#late_type_1').prop( "checked", true );
                    $('.per_min').css('display', 'block');
                    $('.short_lev').css('display', 'none');
                    $('.half_day').css('display', 'none');
                    $('#short_leaves').val(0).prop( "checked", true); 
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'short_leaves',
                        value: 0
                    });
                    $('#half_days').val(0).prop( "checked", true);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'half_days',
                        value: 0
                    });
                }else if(data.result.late_type == 2){
                    $('#late_type_2').prop( "checked", true );
                    $('.short_lev').css('display', 'block');
                    $('.half_day').css('display', 'block');
                    $('.per_min').css('display', 'none');
                    $('#late_attend_min').val(0).prop( "checked", true); 
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'late_attend_min',
                        value: 0
                    });
                } else if(data.result.late_type == 3){
                    $('#late_type_3').prop( "checked", true );
                    $('.short_lev').css('display', 'block');
                    $('.half_day').css('display', 'block');
                    $('.per_min').css('display', 'block');
                }

                $('#late_attend_min').val(data.result.late_attend_min);
                $('#short_leaves').val(data.result.short_leaves);
                $('#half_days').val(data.result.half_days);

                $('#hidden_id').val(id);
                $('.modal-title').text('Edit Job Category');
                $('#action_button').html('<i class="fas fa-edit"></i>&nbsp;Edit');
                $('#action').val('Edit');
                $('#formModal').modal('show');
            }
        })
    });

    var user_id;

    $(document).on('click', '.delete', function () {
        user_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function () {
        $.ajax({
            url: "JobCategory/destroy/" + user_id,
            beforeSend: function () {
                $('#ok_button').text('Deleting...');
            },
            success: function (data) {//alert(data);
                setTimeout(function () {
                    $('#confirmModal').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    alert('Data Deleted');
                }, 2000);
                location.reload()
            }
        })
    });

    $(document).on('change', '.lunch_deduct_type', function (e) {
        let val = $(this).val();
        if(val == 1){
            $('.custom_lunch').css('display', 'block');
        }else{
            $('.custom_lunch').css('display', 'none');
            $('#lunch_deduct_min').val(0).prop( "checked", true); 
            $('<input>').attr({
                type: 'hidden',
                name: 'lunch_deduct_min',
                value: 0
            });
        }

    });

    $(document).on('change', '.is_sat_ot_type_as_act', function (e) {
        let val = $(this).val();
        if(val == 0){
            $('.custom_sat').css('display', 'block');
        }else{
            $('.custom_sat').css('display', 'none');
        }

    });

    $(document).on('change', '.is_sun_ot_type_as_act', function (e) {
        let val = $(this).val();
        if(val == 0){
            $('.custom_sun').css('display', 'block');
        }else{
            $('.custom_sun').css('display', 'none');
            $('.custom_sun_after').css('display', 'none');
            $('#sun_after_double').prop('required', false);
            
        }

    });

    $(document).on('change', '.custom_sunday_ot_type', function (e) {
        let val = $(this).val();
        if (val == 1) {
            $('.custom_sun_after').css('display', 'block');
            $('#sun_after_double').prop('disabled', false).val('');
            $('#sun_after_double').prop('required', true); 
        } else if (val == 2) {
            $('.custom_sun_after').css('display', 'none');
            $('#sun_after_double').val(0).prop("checked", true);
            $('#sun_after_double').prop('required', false); 
            $('<input>').attr({
                type: 'hidden',
                name: 'sun_after_double',
                value: 0
            });  
        }
    });

    // $(document).on('change', '.spe_day_1_type', function (e) {
    //     let val = $(this).val();
    //     if(val == 0){
    //         $('.custom_spe').css('display', 'block');
    //     }else{
    //         $('.custom_spe').css('display', 'none');
    //     }

    // });

    $(document).on('change', '.late_type', function (e) {
        let val = $(this).val();
        if(val == 1){
            $('.per_min').css('display', 'block');
            $('.short_lev').css('display', 'none');
            $('.half_day').css('display', 'none');
            $('#short_leaves').val(0).prop( "checked", true); 
            $('<input>').attr({
                type: 'hidden',
                name: 'short_leaves',
                value: 0
            });
            $('#half_days').val(0).prop( "checked", true);
            $('<input>').attr({
                type: 'hidden',
                name: 'half_days',
                value: 0
            });
        }else if(val == 2){
            $('.short_lev').css('display', 'block');
            $('.half_day').css('display', 'block');
            $('.per_min').css('display', 'none');
            $('#late_attend_min').val(0).prop( "checked", true); 
            $('<input>').attr({
                type: 'hidden',
                name: 'late_attend_min',
                value: 0
            });
        } else if(val == 3){
            $('.short_lev').css('display', 'block');
            $('.half_day').css('display', 'block');
            $('.per_min').css('display', 'block');
        }

    });
    
});
</script>


@endsection




