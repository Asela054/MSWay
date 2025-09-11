<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\Branch;
use App\Attendance;
use App\EmployeePaySlip;
use App\EmployeeSalary;
use App\Helpers\EmployeeHelper;
use App\Holiday;
use App\Leave;
use App\PayrollProfile;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use stdClass;
use Session;


class Report extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getemployeelist()
    {
        $permission = Auth::user()->can('employee-report');
        if (!$permission) {
            abort(403);
        }

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }
        
        return view('Report.employeereport', compact('company_name'));
    }

    public function employee_report_list(Request $request)
    {
        $permission = Auth::user()->can('employee-report');
        if (!$permission) {
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
        $totalRecords = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->count();

        $query = DB::table('employees');
        $query->select('count(*) as allcount');
        $query->where(function ($querysub) use ($searchValue) {
            $querysub->where('employees.id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                ->orWhere('departments.name', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_birthday', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_mobile', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_work_telephone', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_national_id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_gender', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_email', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_2', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_addressT1', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_T2', 'like', '%' . $searchValue . '%')
                ->orWhere('employment_statuses.emp_status', 'like', '%' . $searchValue . '%')
                ->orWhere('job_titles.title', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_permanent_date', 'like', '%' . $searchValue . '%');
        })
        ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0);

        if ($department != "" && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }

        $totalRecordswithFilter = $query->count();

        // Fetch records
        $query = DB::table('employees');
        $query->where(function ($querysub) use ($searchValue) {
            $querysub->where('employees.id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('branches.location', 'like', '%' . $searchValue . '%')
                ->orWhere('departments.name', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_birthday', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_mobile', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_work_telephone', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_name_with_initial', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_national_id', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_gender', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_email', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_2', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_addressT1', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_address_T2', 'like', '%' . $searchValue . '%')
                ->orWhere('employment_statuses.emp_status', 'like', '%' . $searchValue . '%')
                ->orWhere('job_titles.title', 'like', '%' . $searchValue . '%')
                ->orWhere('employees.emp_permanent_date', 'like', '%' . $searchValue . '%');
        })
        ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
        ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
        ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
        ->leftjoin('employment_statuses', 'employment_statuses.id', '=', 'employees.emp_status')
        ->where('employees.deleted', 0)
        ->where('employees.is_resigned', 0);

        if ($department != "" && $department != 'All') {
            $query->where('employees.emp_department', $department);
        }

        $query->select(
            "employees.id",
            "employees.emp_name_with_initial",
            "employees.calling_name",
            "employees.emp_id",
            "branches.location",
            "departments.name as dept_name",
            "employees.emp_birthday",
            "employees.emp_mobile",
            "employees.emp_work_telephone",
            "employees.emp_name_with_initial",
            "employees.emp_national_id",
            "employees.emp_gender",
            "employees.emp_email",
            "employees.emp_address",
            "employees.emp_address_2",
            "employees.emp_addressT1",
            "employees.emp_address_T2",
            "employment_statuses.emp_status as e_status",
            "job_titles.title",
            "employees.emp_permanent_date"
        );

        $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage);

        $records = $query->get();

        $data_arr = array();
        foreach ($records as $record) {
            $data_arr[] = array(
                "id" => $record->id,
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "employee_display" => EmployeeHelper::getDisplayName((object)[
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' => $record->emp_name_with_initial,
                    'calling_name' => $record->calling_name
                ]),
                "location" => $record->location,
                "dept_name" => $record->dept_name,
                "emp_birthday" => $record->emp_birthday,
                "emp_mobile" => $record->emp_mobile,
                "emp_work_telephone" => $record->emp_work_telephone,
                "emp_national_id" => $record->emp_national_id,
                "emp_gender" => $record->emp_gender,
                "emp_email" => $record->emp_email,
                "emp_address" => $record->emp_address . $record->emp_address_2,
                "emp_addressT" => $record->emp_addressT1 . $record->emp_address_T2,
                "e_status" => $record->e_status,
                "title" => $record->title,
                "emp_permanent_date" => $record->emp_permanent_date
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

    public function exportempoloyeereport()
    {

        $emp_data = DB::table('employees')
            ->join('job_titles', 'employees.emp_job_code', '=', 'job_titles.id')
            ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->select('employees.*', 'job_titles.title', 'branches.location')
            ->get();


        $emp_array[] = array('Employee Id', 'Name with Initial', 'Home Address', 'Date of Birth', 'Mobile No', 'NIC No', 'Gender', 'Email', 'Job Category', 'Permenent Date'
        , 'Employee No', 'EPF No', 'Joined Date', 'Location');
        foreach ($emp_data as $employee) {
            $emp_array[] = array(
                'Employee Id' => $employee->id,
                'Name with Initial' => $employee->emp_name_with_initial,
                'Home Address' => $employee->emp_address,
                'Date of Birth' => $employee->emp_birthday,
                'Mobile No' => $employee->emp_mobile,
                'NIC No' => $employee->emp_national_id,
                'Gender' => $employee->emp_gender,
                'Email' => $employee->emp_email,
                'Job Category' => $employee->title,
                'Permenent Date' => $employee->emp_permanent_date,
                'Employee No' => $employee->id,
                'EPF No' => $employee->emp_etfno,
                'Joined Date' => $employee->emp_join_date,
                'Location' => $employee->location


            );
        }
        Excel::create('Employee List', function ($excel) use ($emp_array) {
            $excel->setTitle('Employee List');
            $excel->sheet('Employee List', function ($sheet) use ($emp_array) {
                $sheet->fromArray($emp_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */

    function employee_fetch_data(Request $request)
    {

        // dd($request->employee);
        if ($request->ajax()) {
            if ($request->employee != '') {

                $data = DB::query()
                    ->select('at1.*', DB::raw('Max(at1.timestamp) as lasttimestamp'),
                        'employees.emp_name_with_initial', 'branches.location')
                    ->from('attendances as at1')
                    ->Join('employees', 'at1.uid', '=', 'employees.emp_id')
                    ->leftjoin('branches', 'employees.emp_location', '=', 'branches.id')
                    ->where('employees.emp_id', $request->employee)
                    ->groupBy('at1.uid', 'at1.date')
                    ->get();


            }

            echo json_encode($data);

        }

    }

}
