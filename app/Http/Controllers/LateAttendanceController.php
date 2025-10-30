<?php

namespace App\Http\Controllers;

use App\Helpers\EmployeeHelper;
use App\LateAttendance;
use App\Leave;
use App\Services\LatePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateTime;

class LateAttendanceController extends Controller
{
    public function late_attendance_by_time()
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.late_attendance_by_time');
    }

        public function late_types_sel2(Request $request)
    {
        if ($request->ajax()) {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DB::query()
                ->where('name', 'LIKE', '%' . Input::get("term") . '%')
                ->from('late_types')
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT id as id'), DB::raw('name as text')]);

            $count = DB::query()
                ->where('name', 'LIKE', '%' . Input::get("term") . '%')
                ->from('late_types')
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->select([DB::raw('DISTINCT id as id'), DB::raw('name as text')])
                ->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    // late attendace mark data table
    public function attendance_by_time_report_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // Read parameters
        $department = $request->get('department');
        $company = $request->get('company');
        $employee = $request->get('employee');
        $late_type = $request->get('late_type');
        $from_date = date('Y-m-d', strtotime($request->get('from_date'))) . ' 00:00:00';
        $to_date = date('Y-m-d', strtotime($request->get('to_date'))) . ' 00:00:00';


        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'] ?? 0;
        $columnName = $columnName_arr[$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'asc';
        $searchValue = $search_arr['value'] ?? '';

        // Get late time threshold if specified
        $late_time_threshold = null;
        if ($late_type) {
            $late_time = DB::table('late_types')->where('id', $late_type)->first();
            if ($late_time) {
                $late_time_threshold = $late_time->time_from;
            }
        }


        // Base query without late time filter (for counting)
        $baseQuery = DB::table('attendances as at1')
            ->select(
                'at1.id',
                'at1.uid',
                'at1.date',
                DB::raw('MIN(at1.timestamp) as first_checkin'),
                DB::raw('MAX(at1.timestamp) as last_checkout'),
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'branches.location as branch_location',
                'branches.id as branch_id',
                'departments.name as department_name',
                'departments.id as department_id'
            )
            ->join('employees', 'employees.emp_id', '=', 'at1.uid')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('companies', 'companies.id', '=', 'departments.company_id')
            ->whereNull('at1.deleted_at')
            ->groupBy('at1.uid', 'at1.date');

        // Apply filters to base query
        if ($searchValue) {
            $baseQuery->where(function($q) use ($searchValue) {
                $q->where('employees.emp_id', 'like', "%$searchValue%")
                ->orWhere('employees.emp_name_with_initial', 'like', "%$searchValue%")
                 ->orWhere('employees.calling_name', 'like', "%$searchValue%")
                ->orWhere('at1.timestamp', 'like', "%$searchValue%")
                ->orWhere('companies.name', 'like', "%$searchValue%")
                ->orWhere('branches.location', 'like', "%$searchValue%")
                ->orWhere('departments.name', 'like', "%$searchValue%");
            });
        }

        if ($department) {
            $baseQuery->where('departments.id', $department);
        }
        if ($company) {
            $baseQuery->where('employees.emp_company', $company);
        }
        if ($employee) {
            $baseQuery->where('at1.uid', $employee);
        }
        if ($from_date && $to_date) {
            $baseQuery->whereBetween('at1.date', [$from_date, $to_date]);
        } elseif ($from_date) {
            $baseQuery->where('at1.date', '>=', $from_date);
        } elseif ($to_date) {
            $baseQuery->where('at1.date', '<=', $to_date);
        }

        $totalRecords = DB::table(DB::raw("({$baseQuery->toSql()}) as sub"))
            ->mergeBindings($baseQuery)
            ->count();

        $filteredQuery = clone $baseQuery;
        if ($late_time_threshold && $late_time->late_early == 0) {
            $filteredQuery->havingRaw("TIME(MIN(at1.timestamp)) > ?", [$late_time_threshold]);
        }
        else if ($late_time_threshold && $late_time->late_early == 1) {
            $filteredQuery->havingRaw("TIME(MAX(at1.timestamp)) < ?", [$late_time_threshold]);
        }

        $totalFiltered = DB::table(DB::raw("({$filteredQuery->toSql()}) as sub"))
            ->mergeBindings($filteredQuery)
            ->count();



        $filteredQuery->orderBy($columnName, $columnSortOrder);
        $records = $filteredQuery->skip($start)->take($rowperpage)->get();

        $data = [];

        foreach ($records as $record) {
            $first_checkin = Carbon::parse($record->first_checkin);
            $last_checkout = Carbon::parse($record->last_checkout);
            
             $employeeObj = (object)[
            'emp_id' => $record->emp_id,
            'emp_name_with_initial' => $record->emp_name_with_initial,
            'calling_name' => $record->calling_name
        ];

             $is_late_marked = DB::table('employee_late_attendances')
            ->where('emp_id', $record->emp_id)
            ->where('date', $record->date)
            ->exists();

            $data[] = [
                'id' => $record->id,
                'uid' => $record->uid,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'employee_display' => EmployeeHelper::getDisplayName($employeeObj),
                'date' => $record->date,
                'timestamp' => $first_checkin->format('H:i'),
                'lasttimestamp' => $first_checkin->format('H:i') != $last_checkout->format('H:i') 
                                ? $last_checkout->format('H:i') 
                                : '',
                'workhours' => gmdate("H:i:s", $last_checkout->diffInSeconds($first_checkin)),
                'dept_name' => $record->department_name,
                'dept_id' => $record->department_id,
                'location' => $record->branch_location,
                'location_id' => $record->branch_id,
                'is_late_marked' => $is_late_marked ? 1 : 0
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data
        ]);
    }



    public function lateAttendance_mark_as_late(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;


        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Select one or more employees']);
        }


        $data_arr = array();
        foreach ($selected_cb as $cr) {

            // if ($cr['lasttimestamp'] != '') {


            $empshift = DB::table('employees')
            ->select('emp_id', 'emp_shift')
            ->where('emp_id', $cr['uid'])
            ->first();

            if (is_null($empshift)) {
                continue;
            }

             $emprosterinfo = DB::table('employee_roster_details')
                    ->select('emp_id', 'shift_id')
                    ->where('emp_id', $cr['uid'])
                    ->where('work_date', $cr['date'])
                    ->first();

                if ($emprosterinfo) {
                    $empshiftid = $emprosterinfo->shift_id;   
                }
                else {
                    $empshiftid = $empshift->emp_shift; 
                }

            $shiftType = DB::table('shift_types')
                ->select('shift_types.onduty_time')
                 ->where('id', $empshiftid)
                ->first();



                if ($shiftType && $shiftType->onduty_time) {
                            $ondutyTime = new DateTime($shiftType->onduty_time);
                            $checkInTime = new DateTime($cr['timestamp']);

                            $interval = $checkInTime->diff($ondutyTime);
                            $minutesDifference = ($interval->h * 60) + $interval->i;

                            // Check if check-in time is after on-duty time
                            if ($checkInTime > $ondutyTime) {
                                $interval = $checkInTime->diff($ondutyTime);
                                $minutesDifference = ($interval->h * 60) + $interval->i;

                                $late_minutes_data[] = array(
                                    'attendance_id' => $cr['id'],
                                    'emp_id' => $cr['uid'],
                                    'attendance_date' => $cr['date'],
                                    'minites_count' => $minutesDifference,
                                );
                            }
                }
                

                $data_arr[] = array(
                    'attendance_id' => $cr['id'],
                    'emp_id' => $cr['uid'],
                    'date' => $cr['date'],
                    'check_in_time' => $cr['timestamp'],
                    'check_out_time' => $cr['lasttimestamp'],
                    'working_hours' => $cr['workhours'],
                    'created_by' => Auth::id(),
                );

                DB::table('employee_late_attendances')
                    ->where('attendance_id', $cr['id'])
                    ->where('emp_id', $cr['uid'])
                    ->where('date', $cr['date'])
                    ->where('check_in_time', $cr['timestamp'])
                    ->where('check_out_time', $cr['lasttimestamp'])
                    ->delete();
            // }
        }

        DB::table('employee_late_attendances')->insert($data_arr);

        if (!empty($late_minutes_data)) {
            DB::table('employee_late_attendance_minites')->insert($late_minutes_data);
        }
        return response()->json(['status' => true, 'msg' => 'Updated successfully.']);
    }

    public function late_attendance_by_time_approve()
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-approve');
        if (!$permission) {
            abort(403);
        }

        $leave_types = DB::table('leave_types')->get();
        return view('Attendent.late_attendance_by_time_approve', compact('leave_types'));
    }


        public function lateAttendance_mark_as_late_approve(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $selected_cb = $request->selected_cb;
        $leave_type = $request->leave_type;

        if (empty($selected_cb)) {
            return response()->json(['status' => false, 'msg' => 'Select one or more employees']);
        }

        $latePolicyService = new LatePolicyService();
        $id_arr = array();
        $date = '';
        $errors = [];

        foreach ($selected_cb as $cr) {
            $date = $cr['date'];
            array_push($id_arr, $cr['id']);

            // Get employee late attendance data
            $emp_data = DB::table('employee_late_attendances')
                ->find($cr['id']);

        if (!$emp_data) {
            $errors[] = "Record not found for ID: " . $cr['id'];
            continue; // Skip to next iteration
        }

            // Process late attendance using service
             $result = $latePolicyService->processLateAttendance($emp_data, $leave_type, $date);

                // Check if processing was successful
                if (!$result) {
                    return response()->json(['status' => false, 'msg' => "Employee " . $emp_data->emp_id . " does not have a proper job category assigned Or Late Type assigned."]);
                }
            
                
          // Update late attendance as approved
            DB::table('employee_late_attendances')
                ->where('id', $cr['id'])
                ->update(['is_approved' => 1]);
        }

         
               return response()->json(['status' => true, 'msg' => 'Late Mark Completed successfully.']);
            


        return response()->json(['status' => true, 'msg' => 'Late Mark Completed successfully.']);
    }

     public function late_attendances_all(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-list');
        if (!$permission) {
            abort(403);
        }
        return view('Attendent.late_attendance_all');
    }

    public function late_attendance_list_approved(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('late-attendance-list');
        if (!$permission) {
            return response()->json(['error' => 'You do not have permission.']);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query = DB::query()
            ->select('ela.*',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'branches.location',
                'departments.name as dep_name')
            ->from('employee_late_attendances as ela')
            ->Join('employees', 'ela.emp_id', '=', 'employees.emp_id')
            ->leftJoin('attendances as at1', 'at1.id', '=', 'ela.attendance_id')
            ->leftJoin('branches', 'at1.location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department');

        if ($department != '') {
            $query->where(['departments.id' => $department]);
        }

        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }

        if ($location != '') {
            $query->where(['at1.location' => $location]);
        }

        if ($from_date != '' && $to_date != '') {
            $query->whereBetween('ela.date', [$from_date, $to_date]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('check_in_time', function ($row) {
                return date('H:i', strtotime($row->check_in_time));
            })
            ->editColumn('check_out_time', function ($row) {
                return date('H:i', strtotime($row->check_out_time));
            })
             ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
                })
            ->filterColumn('employee_display', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                        ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                        ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('formatted_date', function($query, $keyword) {
                    $query->where('at1.date', 'like', "%{$keyword}%");
                })
            ->addColumn('action', function ($row) {

                $btn = '';

                $permission = Auth::user()->can('late-attendance-delete');
                if ($permission) {
                    $btn = ' <button type="button" 
                        name="delete_button"
                        title="Delete"
                        data-id="' . $row->id . '"  
                        class="view_button btn btn-danger btn-sm delete_button" data-toggle="tooltip" title="Remove"><i class="fas fa-trash-alt" ></i></button> ';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy_late_attendacne(Request $request)
    {
        $permission = Auth::user()->can('late-attendance-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = Request('id');

        $lateattendance = LateAttendance::findOrFail($id);

        $empid = $lateattendance->emp_id;
        $date = $lateattendance->date;
        $attendanceid = $lateattendance->attendance_id;

        DB::table('employee_late_attendance_minites')
        ->where('attendance_id', $attendanceid)
        ->delete();
        
        $emp_leave = DB::table('leaves')
        ->where('emp_id', $empid)
        ->where('leave_from', $date)
        ->first();

        $message = "";
        if($emp_leave){
            $id_leave = $emp_leave->id;
            $status = $emp_leave->status;

            if($status === "Approved"){

                $message = "There is an approved leave for this date. Please remove it before deleting the late attendance record";

            }else{
                $leaves = Leave::findOrFail($id_leave);
                $leaves->delete();

                $deletedCount = DB::table('employee_late_attendance_minites')
                    ->where('attendance_date', $date)
                    ->where('emp_id', $empid)
                    ->delete();

                $lateattendance->delete();

                $message = "Record deleted";
            }

        }else{
            $lateattendance->delete();
            $message = "Record deleted";
        }

        return response()->json(['message' => $message ]);
    }

}
