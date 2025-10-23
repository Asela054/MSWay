<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrainingSummaryController extends Controller
{
    public function train_summary()
    {
        
        return view('Training_Management.trainingSummary');
    }
}
