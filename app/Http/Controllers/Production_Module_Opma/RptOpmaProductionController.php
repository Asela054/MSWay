<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RptOpmaProductionController extends Controller
{
   public function index()
    {
        $user = Auth::user();
        $permission = $user->can('opma-employee-production-report');
          if (!$permission) {
            abort(403);
        }

        $machines = DB::table('opma_machines')
            ->select('id', 'machine')
            ->get();
         $shift_types = DB::table('shift_types')
            ->select('id', 'shift_code','shift_name')
            ->where('deleted', 0)
            ->get();

        return view('Opma_Production.Production_Reports.rpt_employee_shiftproduction', compact('machines', 'shift_types'));
    }
}
