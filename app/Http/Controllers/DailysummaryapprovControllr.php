<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class DailysummaryapprovControllr extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Production-OT-Daily-Approve');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        return view('Daily_summary.daily_approve');
    }
}
