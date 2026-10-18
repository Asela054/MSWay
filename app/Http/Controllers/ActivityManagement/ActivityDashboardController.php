<?php

namespace App\Http\Controllers\ActivityManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityDashboardController extends Controller
{
    public function index()
    {

        return view('ActivityManagement.activity_dashboard');
    }
}
