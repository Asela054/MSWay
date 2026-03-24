<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OPMA_APIProductionDashboardController extends Controller
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

    Public function GetproductionList(Request $request){

    $validator = \Validator::make($request->all(), [
            'employee' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $query = DB::table('opma_employee_production AS ep')
                    ->leftJoin('opma_machines AS m', 'ep.machine_id', '=', 'm.id')
                    ->leftJoin('opma_styles AS p', 'ep.product_id', '=', 'p.id')
                    ->leftJoin('employees AS e', 'ep.emp_id', '=', 'e.emp_id')
                    ->select(
                        'ep.id',
                        'ep.emp_id',
                        'e.emp_name_with_initial',
                        'ep.date',
                        'm.machine',
                        'p.title',
                        'ep.amount',
                        'ep.description',
                        'ep.Produce_qty',
                        'ep.precentage',
                        'ep.damage_precentage',
                        'ep.damage_qty',
                        'ep.perfomance'
                    )
                    ->where('ep.status', 1)
                    ->where('ep.emp_id', $request->employee)
                    ->get();
        
        $data = array(
            'productionlist' => $query
        );

        return (new BaseController)->sendResponse($data, 'productionlist');
    }

}
