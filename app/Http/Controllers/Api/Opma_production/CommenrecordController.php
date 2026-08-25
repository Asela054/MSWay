<?php

namespace App\Http\Controllers\Api\Opma_production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController;

class CommenrecordController extends Controller
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

    public function Getemployees(Request $request)
    {
        $employees = DB::table('employees')
        ->select('emp_id', 'emp_name_with_initial','emp_department','emp_company','emp_job_code')
        ->where('deleted', 0)
        ->where('is_resigned', 0)
        ->get();

        $data = array(
            'employees' => $employees
        );

        return (new BaseController)->sendResponse($data, 'employees');
    }

       public function Getcompanylist(Request $request)
    {
        $companies = DB::table('companies')
        ->select('*')
        ->get();

        $data = array(
            'companies' => $companies
        );

        return (new BaseController)->sendResponse($data, 'companies');
    }

     public function Getdepartmentlist(Request $request)
    {
        $id = Request('Companyid');
        $departments = DB::table('departments')
        ->select('*')
        ->where('company_id', $id)
        ->get();

        $data = array(
            'departments' => $departments
        );

        return (new BaseController)->sendResponse($data, 'departments');
    }
}
