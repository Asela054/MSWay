<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionreportController extends Controller
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

        $products = DB::table('opma_styles')
            ->select('id', 'title')
            ->get();

        return view('Opma_Production.Production_Reports.rpt_employee_production', compact('machines', 'products'));
    }


    public function dailyreport()
    {
        $user = Auth::user();
        $permission = $user->can('opma-employee-production-report');
          if (!$permission) {
            abort(403);
        }
        return view('Opma_Production.Production_Reports.rpt_employee_dailysummary');
    }
}
