<?php

namespace App\Http\Controllers\Api;

use App\EmployeeAvailability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class APIEmployeeController extends Controller
{
     public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }



        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    
    public function GetEmployeeProfileDetails(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $employee = DB::table('employees')
                ->select(
                    'employees.*',
                    'companies.name as company_name',
                    'companies.email as company_email',
                    'departments.name as department_name',
                     'branches.location as employee_location',
                    'shift_types.shift_name as shift_type_name',
                    'employee_pictures.emp_pic_filename as profile_picture' )
            ->leftJoin('companies', 'employees.emp_company', '=', 'companies.id')
            ->leftJoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftJoin('branches', 'employees.emp_location', '=', 'branches.id')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->leftJoin('employee_pictures', 'employees.emp_id', '=', 'employee_pictures.emp_id')
            ->where('employees.emp_id', $request->employee_id)
            ->first();

        $date = date('Y-m-d');
        $employee_availability = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $date)->first();

        $emp_avail_data = array();

        if(empty($employee_availability)){
            $emp_avail_data['session'] = '';
        }else{
            $emp_avail_data['session'] = $employee_availability->session;
        }

        if ($employee->profile_picture) {
            $employee->profile_picture = url('/public/images/' . $employee->profile_picture);
        }
        
        $data = array(
            'employee' => $employee,
            'employee_availability' => $emp_avail_data
        );

        if(EMPTY($employee)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'Invalid Employee ID']);
        }

        return (new BaseController)->sendResponse($data, 'Employee Details');
    }

    public function Getemployees(Request $request)
    {
        $employees = DB::table('employees')
        ->select('emp_name_with_initial','emp_id','id')
        ->where('deleted', 0)
        ->get();

        $data = array(
            'employees' => $employees
        );

        return (new BaseController)->sendResponse($data, 'employees');
    }

    public function Getapprovepersons(Request $request)
    {

        $employees = DB::table('employees')
        ->select('emp_name_with_initial','emp_id','id')
        ->where('deleted', 0)
        ->where('leave_approve_person', 1)
        ->get();

        $data = array(
            'approve_persons' => $employees
        );

        return (new BaseController)->sendResponse($data, 'employees');
    }
}
