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
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;
use App\Services\LeavepolicyService;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      protected $leavePolicyService;

    public function __construct(LeavepolicyService $leavePolicyService)
    {
        $this->leavePolicyService = $leavePolicyService;
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

         // Get accessible employee IDs based on user access rights
        $userId = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);
        
        // Return empty HTML if no accessible employees
        if (empty($accessibleEmployeeIds)) {
            return response()->json(['html' => '']);
        }

        $query = \Illuminate\Support\Facades\DB::query()
            ->select('employees.*',
                'branches.location',
                'departments.name as dep_name')
            ->from('employees')
            ->leftJoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->where('employees.deleted', '=', '0')
            ->whereIn('employees.emp_id', $accessibleEmployeeIds)
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
            $empid = $employee->emp_id;

            $formated_from_date = date('Y').'-01-01';
            $formated_fromto_date = date('Y').'-12-31';

           $current_year_taken_a_l = (new \App\Leave)->taken_annual_leaves($empid, $formated_from_date, $formated_fromto_date);

           $current_year_taken_c_l = (new \App\Leave)->taken_casual_leaves($empid, $formated_from_date, $formated_fromto_date);
        
           $leave_msg = '';


            $annualData = $this->leavePolicyService->calculateAnnualLeaves($employee->emp_join_date, $employee->emp_id);
            $annual_leaves = $annualData['annual_leaves'];
            $leave_msg = $annualData['leave_msg'];

            $casual_leaves = $this->leavePolicyService->calculateCasualLeaves($employee->emp_join_date);


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
                 "employee_display" => EmployeeHelper::getDisplayName($employee), 
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
