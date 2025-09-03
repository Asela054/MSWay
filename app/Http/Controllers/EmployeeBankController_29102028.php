<?php

namespace App\Http\Controllers;

use App\Daily_task;
use App\EmployeeBank;
use Illuminate\Http\Request;
use Validator;
use DB;
use Session;

class EmployeeBankController extends Controller
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
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = \Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, array(
            'emp_id' => 'required',
            'branch_id' => 'required',
            'bank_code' => 'required',
            'bank_ac_no' => 'required'
        ));

        //get bank_branch code by id
        $branch_code = DB::table('bank_branches')->where('id', $request->input('branch_id') )->value('code');
        //$emp_id = DB::table('employees')->where('id', $request->input('emp_id') )->value('emp_id');

        $emp_id = $request->input('emp_id');
        $employeebank = new EmployeeBank;
        $employeebank->emp_id = $request->input('emp_id');
        $employeebank->branch_code = $branch_code;
        $employeebank->bank_code = $request->input('bank_code');
        $employeebank->bank_ac_no = $request->input('bank_ac_no');
        $employeebank->save();

        Session::flash('message', 'The Bank Details Successfully Saved');
        Session::flash('alert-class', 'alert-success');


        return redirect('viewbankDetails/' . $emp_id);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\EmployeeBank $employeeBank
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = \Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $employeebank = DB::table('employee_banks as eb')
            ->select(
                'eb.id',
                'eb.bank_ac_no',
                'b.code as bankcode',
                'b.bank',
                'bb.branch',
                'bb.code as branchCode'
            )
            ->leftJoin('bank_branches as bb', function($join)
            {
                $join->on('bb.code', '=', 'eb.branch_code');
                $join->on('bb.bankcode', '=', 'eb.bank_code');
            })
            ->leftjoin('banks as b', 'b.code', '=', 'bb.bankcode')
            ->where('eb.emp_id', $id)->get();

        return view('Employee.viewbankDetails', compact('employeebank', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\EmployeeBank $employeeBank
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeBank $employeeBank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\EmployeeBank $employeeBank
     * @return \Illuminate\Http\Response
     */
    public function update(REQUEST $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\EmployeeBank $employeeBank
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $permission = \Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = EmployeeBank::findOrFail($id);
        $data->delete();

        Session::flash('message', 'Deleted!');
        Session::flash('alert-class', 'alert-success');
    }

    public function empBankReport()
    {
        $permission = \Auth::user()->can('employee-bank-report');
        if(!$permission)
        {
            abort(403);
        }
        return view('Employee.bankReport');
    }

    public function bank_report_list(Request $request)
    {
        $permission = \Auth::user()->can('employee-bank-report');
        if(!$permission)
        {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        ## Read value
        $department = $request->get('department');
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = DB::table('employee_banks as eb')
            ->select('count(*) as allcount')
            ->leftJoin('bank_branches as bb', function($join)
            {
                $join->on('bb.code', '=', 'eb.branch_code');
                $join->on('bb.bankcode', '=', 'eb.bank_code');
            })
            ->leftjoin('banks as b', 'b.code', '=', 'bb.bankcode')
            ->leftjoin('employees', 'employees.id', '=', 'eb.emp_id')
            //->where('employees.deleted', '0')
            ->count();

        $query = DB::table('employee_banks as eb');
        $query->select('count(*) as allcount')
            ->where(function ($querysub) use ($searchValue) {
                $querysub->where('b.bank', 'like', '%' . $searchValue . '%')
                    ->orWhere('b.code', 'like', '%' . $searchValue . '%')
                    ->orWhere('bb.branch', 'like', '%' . $searchValue . '%')
                    ->orWhere('bb.code', 'like', '%' . $searchValue . '%')
                    ->orWhere('employees.emp_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                    ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                    ->orWhere('departments.name', 'like', '%' . $searchValue . '%');
            })
            ->leftJoin('bank_branches as bb', function($join)
            {
                $join->on('bb.code', '=', 'eb.branch_code');
                $join->on('bb.bankcode', '=', 'eb.bank_code');
            })
            ->leftjoin('banks as b', 'b.code', '=', 'bb.bankcode')
            ->leftjoin('employees', 'employees.id', '=', 'eb.emp_id')
            ->leftjoin('employment_statuses', 'employees.emp_status', '=', 'employment_statuses.id')
            ->leftjoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id');
            //->where('employees.deleted', '0');
        if ($department != "") {
            $query->where('employees.emp_department', $department);
        }


        $totalRecordswithFilter = $query->count();

        // Fetch records
        $query = DB::table('employee_banks as eb');
        $query->where(function ($querysub) use ($searchValue) {
            $querysub->where('b.bank', 'like', '%' . $searchValue . '%')
                ->orWhere('b.code', 'like', '%' . $searchValue . '%')
                ->orWhere('bb.branch', 'like', '%' . $searchValue . '%')
                ->orWhere('bb.code', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                ->orWhere('departments.name', 'like', '%' . $searchValue . '%');
        })
            ->leftJoin('bank_branches as bb', function($join)
            {
                $join->on('bb.code', '=', 'eb.branch_code');
                $join->on('bb.bankcode', '=', 'eb.bank_code');
            })
            ->leftjoin('banks as b', 'b.code', '=', 'bb.bankcode')
            ->leftjoin('employees', 'employees.id', '=', 'eb.emp_id')
            ->leftjoin('employment_statuses', 'employees.emp_status', '=', 'employment_statuses.id')
            ->leftjoin('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id');
            //->where('employees.deleted', '0');
        if ($department != "") {
            $query->where('employees.emp_department', $department);
        }
        $query->select(
            "eb.id",
            "eb.bank_ac_no",
            "b.bank",
            "b.code as bank_code",
            "bb.branch",
            "bb.code as branch_code",
            "employees.emp_id",
            "employees.emp_name_with_initial",
            "branches.location",
            "departments.name as dept_name"
        );

        $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage);
        $records = $query->get();

        $data_arr = array();

        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $record->id,
                "bank_ac_no" => $record->bank_ac_no,
                "bank" => $record->bank,
                "bank_code" => $record->bank_code,
                "branch" => $record->branch,
                "branch_code" => $record->branch_code,
                "emp_id" => $record->emp_id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "location" => $record->location,
                "dept_name" => $record->dept_name
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

}
