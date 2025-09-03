<?php

namespace App\Http\Controllers;

use App\Leave;
use App\LeaveType;
use App\Employee;
use App\LeaveDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Yajra\Datatables\Datatables;

class LeaveController extends Controller
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
        $permission = Auth::user()->can('leave-list');
        if (!$permission) {
            abort(403);
        }

        $leavetype = LeaveType::whereBetween('id', [1, 6])->orderBy('id', 'asc')->get();
        $employee = Employee::orderBy('id', 'desc')->get();

        return view('Leave.leaveapply', compact('leavetype', 'employee'));
    }

    public function leave_list_dt(Request $request)
    {
        $permission = Auth::user()->can('leave-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query =  DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
            ->join('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->join('employees as e', 'leaves.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('leaves.*', 'ec.emp_name_with_initial as covering_emp', 'leave_types.leave_type', 'e.emp_name_with_initial as emp_name', 'departments.name as dep_name');

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['e.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['e.emp_location' => $location]);
        }

        if($from_date != '' && $to_date != ''){
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('half_or_short', function($row){

                if($row->half_short == 0.25){
                    return 'Short Leave';
                }

                if($row->half_short == 0.5){
                    return 'Half Day';
                }

                if($row->half_short == 1){
                    return 'Full Day';
                }
                return '';
            })
            ->addColumn('action', function($row){
                $btn = '';

                $permission = Auth::user()->can('leave-edit');
                if ($permission) {
                    $btn = ' <button name="edit" id="'.$row->id.'"
                            class="edit btn btn-outline-primary btn-sm" style="margin:1px;" type="submit">
                            <i class="fas fa-pencil-alt"></i>
                        </button> ';
                }

                $permission = Auth::user()->can('leave-delete');
                if ($permission) {
                    $btn .= '<button type="submit" name="delete" id="'.$row->id.'"
                            class="delete btn btn-outline-danger btn-sm" style="margin:1px;" ><i
                            class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action', 'half_or_short'])
            ->make(true);
    }

    public function approvelindex()
    {
        $permission = Auth::user()->can('leave-approve');
        if (!$permission) {
            abort(403);
        }

        return view('Leave.leaveapprovel');

    }

    public function leave_approve_list_dt(Request $request)
    {
        $permission = Auth::user()->can('leave-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

        $query =  DB::table('leaves')
            ->leftjoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
            ->leftjoin('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->leftjoin('employees as e', 'leaves.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('leaves.*',
                'ec.emp_name_with_initial as covering_emp',
                'leave_types.leave_type',
                'e.emp_name_with_initial as emp_name',
                'departments.name as dep_name'
            )->whereBetween('leave_types.id', [1, 6]);

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['e.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['e.emp_location' => $location]);
        }

        if($from_date != '' && $to_date != ''){
            $query->whereBetween('leaves.leave_from', [$from_date, $to_date]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('status',function($row){
                if($row->status == 'Pending'){
                    $btn = ' <badge class="badge badge-primary" ><i class="fas fa-spinner"></i> Pending</badge> ';
                }else if($row->status == 'Approved'){
                    $btn = ' <badge class="badge badge-success" ><i class="fas fa-check"></i> Approved</badge> ';
                }else if($row->status == 'Rejected'){
                    $btn = ' <badge class="badge badge-danger" ><i class="fas fa-times"></i> Rejected</badge> ';
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('action', function($row){

                if($row->status == 'Pending'){
                    $btn = ' <button id="view" name="view" data-id="'.$row->id.'"
                                                data-empid="'.$row->emp_id.'"
                                                class="view btn btn-outline-primary btn-sm"
                                                type="submit"> <i class="fas fa-pencil-alt"></i></button>';
                }else if($row->status == 'Approved'){
                    $btn = ' <button id="view" name="view" data-id="'.$row->id.'"
                                                data-empid="'.$row->emp_id.'"
                                                class="view btn btn-outline-primary btn-sm"
                                                type="submit"><i class="fas fa-pencil-alt"></i></button>';

                }else if($row->status == 'Rejected'){
                    $btn = ' <button id="view" name="view" data-id="'.$row->id.'"
                                                data-empid="'.$row->emp_id.'"
                                                class="view btn btn-outline-primary btn-sm"
                                                type="submit"><i class="fas fa-pencil-alt"></i></button>';
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function leaveleavecreate()
    {
        return view('Leave.leaveleavecreate');


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

    public function getEmployeeCategory(Request $request)
    {
        $short_leave_enabled = DB::table('employees')
            ->leftJoin('job_categories', 'job_categories.id',  '=', 'employees.job_category_id' )
            ->select('job_category_id', 'category', 'short_leave_enabled')
            ->first();
        return json_encode( $short_leave_enabled );
    }

    public function getemployeeleavestatus(Request $request)
    {
        if ($request->ajax()) {

            $employee = Employee::where('emp_id', $request->emp_id)->first();

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


            $like_from_date_med = date('Y').'-01-01';
            $like_from_date2_med = date('Y').'-12-31';
            $total_taken_med_leaves = DB::table('leaves')
                ->where('leaves.emp_id', '=', $request->emp_id)
                ->whereBetween('leaves.leave_from', [$like_from_date_med, $like_from_date2_med])
                ->where('leaves.leave_type', '=', '4')
                ->get()->toArray();

            $current_year_taken_med = 0;
            foreach ($total_taken_med_leaves as $tta){
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
                    $current_year_taken_med += $diff_in_days;
                }else{
                    $current_year_taken_med += $tta->no_of_days;
                }
            }


            $leave_msg = '';
            $casual_leaves = 0;
            $medical_leaves = 0;
            if($join_year == date('Y')){
                $annual_leaves = $q_data->leaves;
                $leave_msg = "Employee can have only a half day per month in Casual Leaves. (Employee joined in current year)";
            }else{
                //set from new table
                $leaves = DB::table('job_categories')->where('id', $employee->job_category_id)->first();

                $annual_leaves = $leaves->annual_leaves;
                $casual_leaves = $leaves->casual_leaves;
                $medical_leaves = $leaves->medical_leaves;
            }

            $total_no_of_annual_leaves = $annual_leaves;
            $total_no_of_casual_leaves = $casual_leaves;
            $total_no_of_med_leaves = $medical_leaves;

            $available_no_of_annual_leaves = $total_no_of_annual_leaves - $current_year_taken_a_l;
            $available_no_of_casual_leaves = $total_no_of_casual_leaves - $current_year_taken_c_l;
            $available_no_of_med_leaves = $total_no_of_med_leaves - $current_year_taken_med;

            if($employee->emp_status != 1){
                $emp_status = DB::table('employment_statuses')->where('id', $employee->emp_status)->first();
                $leave_msg = 'Casual Leaves - '.$emp_status->emp_status.' Employee can have only a half day per month (Not a permanent employee)';
            }

            $results = array(
                "total_no_of_annual_leaves" => $total_no_of_annual_leaves,
                "total_no_of_casual_leaves" => $total_no_of_casual_leaves,
                "total_no_of_med_leaves" => $total_no_of_med_leaves,
                "total_taken_annual_leaves" => $current_year_taken_a_l,
                "total_taken_casual_leaves" => $current_year_taken_c_l,
                "total_taken_med_leaves" => $current_year_taken_med,
                "available_no_of_annual_leaves" => $available_no_of_annual_leaves,
                "available_no_of_casual_leaves" => $available_no_of_casual_leaves,
                "available_no_of_med_leaves" => $available_no_of_med_leaves,
                "leave_msg" => $leave_msg
            );
            return response()->json($results);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'employee' => '',
            'leavetype' => '',
            'fromdate' => '',
            'todate' => '',
            'reson' => '',
            'approveby' => '',

        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $leave = new Leave;
        $leave->emp_id = $request->input('employee');
        $leave->leave_type = $request->input('leavetype');
        $leave->leave_from = $request->input('fromdate');
        $leave->leave_to = $request->input('todate');
        $leave->no_of_days = $request->input('no_of_days');
        $leave->half_short = $half_short;
        $leave->reson = $request->input('reson');
        $leave->comment = $request->input('comment');
        $leave->emp_covering = $request->input('coveringemployee');
        $leave->leave_approv_person = $request->input('approveby');
        $leave->leave_category = $request->input('leavecat');
        $leave->status = 'Pending';
        $leave->save();

        $users = DB::table('leave_details')
            ->where('emp_id', $request->employee)
            ->count();

        if ($users == 0) {
            $leavedetails = new LeaveDetail;
            $leavedetails->emp_id = $request->input('employee');
            $leavedetails->leave_type = $request->input('leavetype');
            $assign_leave = $request->input('assign_leave');
            $total_leave = $assign_leave - $diff_days;
            $leavedetails->total_leave = $total_leave;
            $leavedetails->save();

        } else {
            DB::table('leave_details')
                ->where('emp_id', $request->employee)
                ->where('leave_type', $request->leavetype)
                ->decrement('total_leave', $diff_days);
        }


        return response()->json(['success' => 'Leave Details Successfully Insert']);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            //$data = Leave::findOrFail($id);
            /* $data = DB::table('leaves')
             ->leftjoin('leave_details', 'leaves.emp_id', '=', 'leave_details.emp_id')
             ->join('leave_types', 'leave_details.leave_type', '=', 'leave_types.id')
             ->select('leaves.*', 'leave_details.total_leave', 'leave_types.assigned_leave')
             ->where('leaves.id' , $id)
             ->get()->toarray();*/
            $data = Leave::with('employee')
                ->with('covering_employee')
                ->with('approve_by')
                ->findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        $rules = array(
            'hidden_id' => 'required',
            'leavetype' => 'required',
            'fromdate' => 'required',
            'todate' => 'required',
            'reson' => 'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromdate);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->todate);
        $diff_days = $to->diffInDays($from);
        $half_short = $request->input('half_short');

        $no_of_days = $request->no_of_days;

        $form_data = array(
            'leave_type' => $request->leavetype,
            'leave_from' => $request->fromdate,
            'leave_to' => $request->todate,
            'no_of_days' => $no_of_days,
            'half_short' => $half_short,
            'reson' => $request->reson,
            'emp_covering' => $request->coveringemployee,
            'leave_approv_person' => $request->approveby,
            'leave_category' => $request->leavecat,
            'status' => 'Pending'

        );

        Leave::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Leave Details Successfully Updated']);
    }

    public function approvelupdate(Request $request)
    {
        $permission = Auth::user()->can('leave-approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'status' => 'required',
            'emp_id' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $status = $request->status;

        $form_data = array(
            'status' => $status,
            'comment' => $request->comment
        );

        Leave::whereId($request->id)->update($form_data);

        if ($request->status == 'Rejected') {

            $leaves = DB::table('leaves')
                ->where('id', $request->id)
                ->get();

            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $leaves[0]->leave_from);
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $leaves[0]->leave_to);
            $diff_days = $to->diffInDays($from);

            DB::table('leave_details')
                ->where('emp_id', $leaves[0]->emp_id)
                ->where('leave_type', $leaves[0]->leave_type)
                ->increment('total_leave', $diff_days);

            return response()->json(['success' => 'Leave Rejected']);

        } else {
            return response()->json(['success' => 'Leave  Approved']);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('leave-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $data = Leave::findOrFail($id);
        $data->delete();
    }
}
