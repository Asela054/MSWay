<?php

namespace App\Http\Controllers\Api\Agro_Ventures;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;

class ApiAgoventuresController extends Controller
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

       public function Getcompanylist(Request $request)
    {
        $companies = DB::table('companies')
        ->select('name', 'id')
        ->get();

        $data = array(
            'companies' => $companies
        );

        return (new BaseController)->sendResponse($data, 'companies');
    }

    public function attendance_list_for_month_edit(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date',
        ]);

        if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'data'    => $validator->errors(),
                ], 422);
            }

        $company_id = $request->get('company_id');
        $from_date  = $request->get('from_date');
        $to_date    = $request->get('to_date');

        $employees = DB::table('employees')
            ->where('emp_company', $company_id)
            ->select('emp_id as employee_id', 'emp_name_with_initial as emp_name')
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->get();

        $attendances = DB::table('attendances')
            ->select(
                'emp_id',
                DB::raw('DATE(date) as att_date'),
                DB::raw('MIN(timestamp) as first_ts'),
                DB::raw('MAX(timestamp) as last_ts')
            )
            ->whereIn('emp_id', $employees->pluck('employee_id')->toArray())
            ->whereBetween('date', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
            ->whereNull('deleted_at')
            ->groupBy('emp_id', DB::raw('DATE(date)'))
            ->get()
            ->groupBy('emp_id');

        $result = [];

        foreach ($employees as $employee) {
            $days = [];
            $totalMinutes = 0;

            $empRows = isset($attendances[$employee->employee_id]) ? $attendances[$employee->employee_id] : [];

            foreach ($empRows as $row) {
                $first = Carbon::parse($row->first_ts);
                $hasOut = $row->first_ts !== $row->last_ts;
                $last = $hasOut ? Carbon::parse($row->last_ts) : null;
                $minutes = $hasOut ? $first->diffInMinutes($last) : 0;

                $totalMinutes += $minutes;

                $days[] = [
                    'date'         => $row->att_date,
                    'in_time'      => $first->format('Y-m-d H:i:s'),
                    'out_time'     => $hasOut ? $last->format('Y-m-d H:i:s') : null,
                    'work_minutes' => $minutes,
                    'work_hours'   => round($minutes / 60, 2),
                ];
            }

            $result[] = [
                'emp_id'             => $employee->employee_id,
                'emp_name'           => $employee->emp_name,
                'attendances'        => $days,
            ];
        }

        return (new BaseController)->sendResponse(
            ['employees' => $result],
            'Attendances retrieved successfully'
        );
    }

}
