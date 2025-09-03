<?php

namespace App\Http\Controllers;

use App\Employee;
use App\LeaveType;
use App\EmploymentStatus;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Yajra\Datatables\Datatables;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $permission = Auth::user()->can('leave-type-list');
        if (!$permission) {
            abort(403);
        }

        $employmentstatus= EmploymentStatus::orderBy('id', 'asc')->get();
        $leavetype = DB::table('leave_types')
            ->join('employment_statuses', 'leave_types.emp_status', '=', 'employment_statuses.id')         
            ->select('leave_types.*', 'employment_statuses.emp_status')
            ->get();
        return view('Leave.leavetype',compact('leavetype','employmentstatus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'leavetype'    =>  'required',
            'empstatus'    =>  'required',
            'assignleave'    =>  'required'            
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'leave_type'        =>  $request->leavetype,
            'emp_status'        =>  $request->empstatus,            
            'assigned_leave'        =>  $request->assignleave           
            
        );

       $leavetype=new LeaveType;
       $leavetype->leave_type=$request->input('leavetype');       
       $leavetype->emp_status=$request->input('empstatus');               
       $leavetype->assigned_leave=$request->input('assignleave');    
       $leavetype->save();

       

        return response()->json(['success' => 'Leave Details Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveType $leaveType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $data = LeaveType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $rules = array(
            'leavetype'    =>  'required',
            'empstatus'    =>  'required',
            'assignleave'    =>  'required'   
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'leave_type'        =>  $request->leavetype,
            'emp_status'        =>  $request->empstatus,            
            'assigned_leave'        =>  $request->assignleave          
            
        );

        LeaveType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Leave Details Successfully Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = LeaveType::findOrFail($id);
        $data->delete();
    }

    public function LeaveBalance()
    {
        $permission = Auth::user()->can('leave-balance-report');
        if (!$permission) {
            abort(403);
        }
        return view('Leave.leave_balance');
    }

    /**
     * @throws Exception
     */
    public function leave_balance_list(Request $request)
    {
        $permission = Auth::user()->can('leave-balance-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee_sel = $request->get('employee');
        $location = $request->get('location');

        $query = \Illuminate\Support\Facades\DB::query()
            ->select('employees.*',
                'branches.location',
                'departments.name as dep_name')
            ->from('employees')
            ->leftJoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->where('employees.deleted', '=', '0')
            ->where('employees.is_resigned', '=', '0');

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee_sel != ''){
            $query->where(['employees.emp_id' => $employee_sel]);
        }

        if($location != ''){
            $query->where(['employees.emp_location' => $location]);
        }

        $employees = $query->get();

        $final_data = array();

        foreach ($employees as $employee)
        {
            $emp_join_date = $employee->emp_join_date;
            $join_year = Carbon::parse($emp_join_date)->year;
            $join_month = Carbon::parse($emp_join_date)->month;
            $join_date = Carbon::parse($emp_join_date)->day;
            $full_date = '2022-'.$join_month.'-'.$join_date;

            $q_data = DB::table('quater_leaves')
                ->where('from_date', '<', $full_date)
                ->where('to_date', '>', $full_date)
                ->first();

            $like_from_date = date('Y').'-01-01';
            $like_from_date2 = date('Y').'-12-31';

            $total_taken_annual_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $employee->emp_id)
                ->whereBetween('leaves.leave_from', [$like_from_date, $like_from_date2])
                ->where('leaves.leave_type', '=', '1')
                ->where('leaves.status', '=', 'Approved')
                ->get()->toArray();

            $current_year_taken_a_l = 0;
            foreach ($total_taken_annual_leaves as $tta){
                $leave_from = $tta->leave_from;
                $leave_to = $tta->leave_to;

                $leave_from_year = Carbon::parse($leave_from)->year;
                $leave_to_year = Carbon::parse($leave_to)->year;

                if($leave_from_year != $leave_to_year){
                    //get current year leaves for that record
                    $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                    $diff_in_days = $to->diffInDays($from);
                    $current_year_taken_a_l += $diff_in_days;

                    $jan_data = DB::table('leaves')
                        ->where('leaves.id', '=', $tta->id)
                        ->first();

                    $firstDayOfMonth = Carbon::parse($jan_data->leave_to)->startOfMonth()->toDateString();
                    $to_t = \Carbon\Carbon::createFromFormat('Y-m-d', $jan_data->leave_to);
                    $from_t = \Carbon\Carbon::createFromFormat('Y-m-d', $firstDayOfMonth);

                    $diff_in_days_f = $to_t->diffInDays($from_t);
                    $current_year_taken_a_l += $diff_in_days_f;

                }else{
                    $current_year_taken_a_l += $tta->no_of_days;
                }
            }

            $like_from_date_cas = date('Y').'-01-01';
            $like_from_date2_cas = date('Y').'-12-31';
            $total_taken_casual_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $request->emp_id)
                ->whereBetween('leaves.leave_from', [$like_from_date_cas, $like_from_date2_cas])
                ->where('leaves.leave_type', '=', '2')
                ->where('leaves.status', '=', 'Approved')
                ->get()->toArray();

            $current_year_taken_c_l = 0;

            foreach ($total_taken_casual_leaves as $tta){
                $leave_from = $tta->leave_from;
                $leave_to = $tta->leave_to;

                $leave_from_year = Carbon::parse($leave_from)->year;
                $leave_to_year = Carbon::parse($leave_to)->year;

                if($leave_from_year != $leave_to_year){
                    //get current year leaves for that record
                    $lastDayOfMonth = Carbon::parse($leave_from)->endOfMonth()->toDateString();

                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $lastDayOfMonth);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leave_from);

                    $diff_in_days = $to->diffInDays($from);
                    $current_year_taken_c_l += $diff_in_days;
                }else{
                    $current_year_taken_c_l += $tta->no_of_days;
                }
            }

            $leave_msg = '';
            if($join_year == date('Y')){
                if(!empty($q_data)){
                    $annual_leaves = $q_data->leaves;
                }
                $leave_msg = "Employee can have only a half day per month in Casual Leaves. (Employee joined in current year)";
            }else{
                $annual_leaves = 14;
                // $casual_leaves = 7;
            }


            $casual_leaves = 0;
            $join_date = new DateTime($emp_join_date);
            $current_date = new DateTime();
            $interval = $join_date->diff($current_date);
            
            $years_of_service = $interval->y;
            $months_of_service = $interval->m;
            
            // Casual leave calculation
            if ($years_of_service == 0) {
                // First year - 0.5 day for every  completed month
               $casual_leaves = number_format((7 / 12) * $months_of_service, 2);

            } else {
                $casual_leaves = 7;
            }


            $total_no_of_annual_leaves = $annual_leaves;
            $total_no_of_casual_leaves = $casual_leaves;

            $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
            $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;

            if($employee->emp_status != 2){
                $emp_status = DB::table('employment_statuses')->where('id', $employee->emp_status)->first();
                $leave_msg = 'Casual Leaves - '.$emp_status->emp_status.' Employee can have only a half day per month (Not a permanent employee)';
            }

            $results = array(
                "emp_id" => $employee->emp_id,
                "emp_name_with_initial" => $employee->emp_name_with_initial,
                "total_no_of_annual_leaves" => $total_no_of_annual_leaves,
                "total_no_of_casual_leaves" => $total_no_of_casual_leaves,
                "total_taken_annual_leaves" => $current_year_taken_a_l,
                "total_taken_casual_leaves" => $current_year_taken_c_l,
                "available_no_of_annual_leaves" => $available_no_of_annual_leaves,
                "available_no_of_casual_leaves" => $available_no_of_casual_leaves,
                "leave_msg" => $leave_msg,
            );

            $final_data[] = $results;


        }

        return Datatables::of($final_data)->make(true);

    }

}
