<?php

namespace App\Http\Controllers\Production_Module_Opma;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductionTaskdashboardController extends Controller
{
    public function index()
    {

        return view('Dashboard.opmaproductiontask');
    }
}
