<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Leave;

class APILeaveapproveController extends Controller
{
      public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');
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

   public function GetApplyLeavelist(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $query = DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
            ->join('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->join('employees as e', 'leaves.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('leaves.*', 'ec.emp_name_with_initial as covering_emp', 'leave_types.leave_type', 'e.emp_name_with_initial as emp_name', 'departments.name as dep_name')
            ->where(['leaves.leave_approv_person' => $request->employee])
            ->get()
            ->map(function ($row) {
                if ($row->half_short == 0.25) {
                    $row->duration_type = 'Short Leave';
                } elseif ($row->half_short == 0.5) {
                    $row->duration_type = 'Half Day';
                } elseif ($row->half_short == 1) {
                    $row->duration_type = 'Full Day';
                }
                return $row;
            });

        $data = array(
            'applyleavelist' => $query
        );

        return (new BaseController)->sendResponse($data, 'applyleavelist');
    }


    public function leave_apply_approve(Request $request)
    {
        //validate request
        $validator = \Validator::make($request->all(), [
            'status' => 'required', // Pending or Approved
            'id' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $form_data = array(
            'status' => $request->status,
            'comment' => $request->comment,
        );

        Leave::whereId($request->id)->update($form_data);

        return (new BaseController)->sendResponse($request->id, 'Leave Approved Updated');
    }

    // public function leave_apply_approved_list(Request $request)
    // {
    //     $leave_list = \App\Leave::WHERE('status', 'approved')->get();
    //     return (new BaseController)->sendResponse($leave_list, 'Leaves List');
    // }

}
