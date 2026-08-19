<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\SalaryIncentive;
use Auth;
use Carbon\Carbon;

class SalaryIncentiveReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-list');

        if(!$permission) {
            abort(403);
        }

        return view('Report.salaryIncentiveReport');
    }
}