<?php

namespace App\Http\Controllers\ERP_KT;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ERPDashboardController extends Controller
{
    public function index()
    {

        return view('ERP_KT.erp_ktdashboard');
    }
}
