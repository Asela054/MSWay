<?php

namespace App\Http\Controllers;

use App\EmployeeTask;
use App\EmpTaskAllocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use Illuminate\Support\Facades\Input;

class TaskEndingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('task-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        return view('Daily_Task.daily_task_ending');
    }

    public function tasklist()
    {
        $types = DB::table('emp_task_allocation')
            ->select(
                'emp_task_allocation.*',
                'task.taskname as task_name'
            )
            ->leftJoin('task', 'emp_task_allocation.task_id', '=', 'task.id')
            ->whereIn('emp_task_allocation.status', [1])
            ->get();

        return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $user = Auth::user();

                    // Add Finish task button if status is not already finished
                    if($row->task_status != 2 && $user->can('task-ending-finish')) {
                        $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-success btn-sm" type="button" title="Finish Task"><i class="fas fa-check-circle"></i></button>';
                    }
                    // Add Cancel Production button if status is not already cancelled
                    if($row->task_status != 3 && $user->can('task-ending-cancel')) {
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm" type="button" title="Cancel Task"><i class="fas fa-times-circle"></i></button>';
                    }
            
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $current_date_time = Carbon::now()->toDateTimeString();

          $task_type = $request->input('task_type');
          $quntity = $request->input('quntity');
          $desription = $request->input('desription');
          $hidden_id = $request->input('hidden_id');

           $maindata = DB::table('emp_task_allocation')
                ->select('emp_task_allocation.*','task.hourly_rate as hourly_rate','task.daily_rate as daily_rate')
                ->leftJoin('task', 'emp_task_allocation.task_id', '=', 'task.id')
                ->where('emp_task_allocation.id', $hidden_id)
                ->first(); 

          $taskdate = $maindata->date;
          $task_id = $maindata->task_id;
          $hourly_rate = $maindata->hourly_rate;
          $daily_rate = $maindata->daily_rate;

          $product_unitvalue=0;

          if($task_type ==="Hourly"){

            if (is_null($hourly_rate)) {
                return response()->json([
                    'errors' => 'Hourly rate is not set for this task. Please configure the hourly rate before proceeding.'
                ]);
            }
            $product_unitvalue = $hourly_rate;

          }else{

            if (is_null($daily_rate)) {
                return response()->json([
                    'errors' => 'Daily rate is not set for this task. Please configure the daily rate before proceeding.'
                ]);
            }
            $product_unitvalue = $daily_rate;
          }

           $employeeAllocations = DB::table('emp_task_allocation_details')
                            ->where('allocation_id', $hidden_id)
                            ->get();

          $employeeCount = $employeeAllocations->count();

          $step01 = $product_unitvalue * $quntity;
        if ($employeeCount > 0) {
            
            // $employee_amount = $step01 / $employeeCount;
            $employee_amount = $step01;
            foreach ($employeeAllocations as $allocation) {

                $existingRecord = EmployeeTask::where('task_allocation_id', $hidden_id)
                                            ->where('emp_id', $allocation->emp_id)
                                            ->first();

                $data = [
                'task_allocation_id' => $hidden_id,
                'emp_id' => $allocation->emp_id,
                'date' => $taskdate,
                'task_id' => $task_id,
                'amount' => $employee_amount,
                'description' => $desription,
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_at' => $current_date_time
                 ];

                    if ($existingRecord) {
                        $existingRecord->update($data);
                    } else {
                        $data['updated_by'] = Auth::id();
                        $data['created_at'] = $current_date_time;
                        EmployeeTask::create($data);
                    }
            }

        $form_data = array(
                    'task_status' => '2',
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time,
                );
        
        EmpTaskAllocation::findOrFail($hidden_id)->update($form_data);

        }
         return response()->json(['success' => 'Task Successfully Finished']);
    }

    public function canceltask(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('task-ending-cancel');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

          $cancel_desription = $request->input('cancel_desription');
          $cancel_id = $request->input('cancel_id');


        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'cancel_description' => $cancel_desription,
            'task_status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpTaskAllocation::findOrFail($cancel_id)->update($form_data);

        return response()->json(['success' => 'Task Successfully Canceled']);

    }

    public function employeetask()
    {
        $user = Auth::user();
        $permission = $user->can('task-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tasks = DB::table('task')
            ->select('id', 'taskname')
            ->get();

        return view('Daily_Task.employee_task', compact('tasks'));
    }


     public function employee_list_task(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;
            $term = Input::get("term");

            $query = DB::table('employees')
                ->where(function($q) use ($term) {
                    $q->where('employees.calling_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('employees.emp_name_with_initial', 'LIKE', '%' . $term . '%');
                })
                ->where('deleted', 0)
                ->where('is_resigned', 0);

            $breeds = $query
                ->select(
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                )
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = Count($breeds); // Get count from the actual results

            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = [
                "results" => $breeds,
                "pagination" => [
                    "more" => $morePages
                ]
            ];

            return response()->json($results);
        }
    }

}
