<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ShiftdashboardController extends Controller
{
    public function index()
    {

        return view('Dashboard.shift');
    }

    public function getshiftdashboard_EmployeeChart(Request $request)
    {
        $data = DB::table('employees')
            ->leftJoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
            ->select(
                'shift_types.id as shift_id',
                'shift_types.shift_name as shift_name',
                'employees.emp_id as emp_id'
            )
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->get();

        $groupedData = [];
        foreach ($data as $row) {
            if (!isset($groupedData[$row->shift_id])) {
                $groupedData[$row->shift_id] = [
                    'shift_id' => $row->shift_id,
                    'shift_name' => $row->shift_name,
                    'count' => 0,
                ];
            }

            $groupedData[$row->shift_id]['count'] += 1;
        }

        return response()->json($groupedData);
    }
}
