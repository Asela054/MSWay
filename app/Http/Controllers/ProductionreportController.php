<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionreportController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('employee-production-report');
          if (!$permission) {
            abort(403);
        }

        $machines = DB::table('machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('product')
            ->select('id', 'productname')
            ->get();

        return view('Production_Reports.rpt_employee_production', compact('machines', 'products'));
    }
}
