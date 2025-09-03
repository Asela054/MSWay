<?php

namespace App\Http\Controllers;

use App\Commen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShiftdashboardController extends Controller
{
    public function index()
    {

    return view('Dashboard.shift');
    }
}