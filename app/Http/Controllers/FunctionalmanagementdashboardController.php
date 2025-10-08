<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FunctionalmanagementdashboardController extends Controller
{
    public function index()
    {
    
    return view('Dashboard.functional');
    }
}