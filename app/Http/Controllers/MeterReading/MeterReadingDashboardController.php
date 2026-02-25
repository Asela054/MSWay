<?php

namespace App\Http\Controllers\MeterReading;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeterReadingDashboardController extends Controller
{
    public function index()
    {

        return view('Dashboard.meterreading');
    }
}
