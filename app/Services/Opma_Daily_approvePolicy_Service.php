<?php

namespace App\Services;

use DB;
use App\Leave;
use Illuminate\Support\Facades\Auth;

class Opma_Daily_approvePolicy_Service
{

     public function OT_LatestoreDailyApprove($empid,$date,$hours,$double_hours,$triple_hours)
     {
          $record = DB::table('opma_daily_approval_summary')
                    ->where('emp_id', $empid)
                    ->where('date', $date)
                    ->where('status', 1)
                    ->first();

          $lateRecord = DB::table('employee_late_attendance_minites')
                    ->where('emp_id', $empid)
                    ->where('attendance_date', $date)
                    ->first();

             $lateMinutes = $lateRecord ? $lateRecord->minites_count : 0;


          if ($record) {
               DB::table('opma_daily_approval_summary')
                    ->where('id', $record->id)
                    ->update([
                         'normal_ot'    => $hours,
                         'late_minites' => $lateMinutes,
                         'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
          }else{

               $employee = DB::table('employees')
                              ->where('emp_id', $empid)
                              ->first();

                         $departmentId = $employee ? $employee->emp_department : null;

                         $attendance = DB::table('attendances as at1')
                              ->select(
                                   DB::raw('MIN(at1.timestamp) as first_checkin'),
                                   DB::raw('MAX(at1.timestamp) as lasttimestamp')
                              )
                              ->where('at1.emp_id', $empid)
                              ->where('at1.date', $date)
                              ->first();

                         $onTime  = $attendance ? $attendance->first_checkin : null;
                         $offTime = $attendance ? $attendance->lasttimestamp : null;

                         DB::table('opma_daily_approval_summary')->insert([
                              'emp_id'        => $empid,
                              'department_id' => $departmentId,
                              'date'          => $date,
                              'on_time'       => $onTime,
                              'off_time'      => $offTime,
                              'late_minites'  => $lateMinutes,
                              'normal_ot'     => $hours,
                              'status'        => 1,
                              'created_at'    => date('Y-m-d H:i:s'),
                              'updated_at'    => date('Y-m-d H:i:s'),
                         ]);
          }
           return true;
     }


      public function Production_storeDailyApprove($empid, $date, $total_target, $total_produce_qty, $daily_aveg,$total_amount)
      {
           $record = DB::table('opma_daily_approval_summary')
                    ->where('emp_id', $empid)
                    ->where('date', $date)
                    ->where('status', 1)
                    ->first();

          if ($record) {
               DB::table('opma_daily_approval_summary')
                    ->where('id', $record->id)
                    ->update([
                         'daily_target'    => $total_target,
                         'daily_produce' => $total_produce_qty,
                         'daily_average' => $daily_aveg,
                         'target_bonus' => $total_amount,
                         'updated_at'   => date('Y-m-d H:i:s'),
                    ]);

               $summaryId = $record->id;    
          }else{

                         $employee = DB::table('employees')
                              ->where('emp_id', $empid)
                              ->first();

                         $departmentId = $employee ? $employee->emp_department : null;

                         $attendance = DB::table('attendances as at1')
                              ->select(
                                   DB::raw('MIN(at1.timestamp) as first_checkin'),
                                   DB::raw('MAX(at1.timestamp) as lasttimestamp')
                              )
                              ->where('at1.emp_id', $empid)
                              ->where('at1.date', $date)
                              ->first();

                         $onTime  = $attendance ? $attendance->first_checkin : null;
                         $offTime = $attendance ? $attendance->lasttimestamp : null;

                          $summaryId = DB::table('opma_daily_approval_summary')->insertGetId([
                                        'emp_id'            => $empid,
                                        'department_id'     => $departmentId,
                                        'date'              => $date,
                                        'on_time'           => $onTime,
                                        'off_time'          => $offTime,
                                        'daily_target'      => $total_target,
                                        'daily_produce' => $total_produce_qty,
                                        'daily_average'     => $daily_aveg,
                                        'target_bonus'      => $total_amount,
                                        'status'            => 1,
                                        'created_at'        => date('Y-m-d H:i:s'),
                                        'updated_at'        => date('Y-m-d H:i:s'),
                                   ]);
          }

               $productionRows = DB::table('opma_employee_production')
                    ->where('emp_id', $empid)
                    ->where('date', $date)
                    ->where('status', 1)
                    ->get();

               DB::table('opma_daily_approval_summary_detail')
                    ->where('summary_id', $summaryId)
                    ->delete();

               foreach ($productionRows as $row) {
                    DB::table('opma_daily_approval_summary_detail')->insert([
                         'summary_id' => $summaryId,
                         'machine_id' => $row->machine_id,
                         'style_id'   => $row->product_id,
                         'target'     => $row->target,
                         'produced'   => $row->Produce_qty,
                         'average'    => $row->precentage,
                         'status'     => 1,
                         'created_at' => date('Y-m-d H:i:s'),
                         'updated_at' => date('Y-m-d H:i:s'),
                    ]);
               }

            return true;
      }




}