<?php

namespace App\Http\Controllers\ProductionEmployee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmpProductionDashboardController extends Controller
{
    public function index()
    {

        return view('ProductionEmployee.ProductionEmployee');
    }
}
