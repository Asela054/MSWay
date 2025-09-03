<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductionEndingController extends Controller
{
    public function index()
    {
        return view('Daily_Production.daily_ending');
    }
    
}
