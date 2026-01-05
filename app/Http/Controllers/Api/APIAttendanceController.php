<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\EmployeeAvailability;
use DateTime;

class APIAttendanceController extends Controller
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

    // Mark employee availability in attendance section dashboard
     public function MarkEmployeeAvailability(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'date' => 'required',
            'session' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        //check if record find
        $record = EmployeeAvailability::where('emp_id', $request->employee_id)->where('date', $request->date)->first();

        if (EMPTY($record)){
            $obj = new EmployeeAvailability();
            $obj->emp_id = $request->employee_id;
            $obj->date = $request->date;
            $obj->availability = '1';
            $obj->session = $request->session;
            $obj->save();
        }else{
            $record->session = $request->session;
            $record->save();
        }

        return (new BaseController)->sendResponse(array(), 'Record Inserted');

    }

    

}
