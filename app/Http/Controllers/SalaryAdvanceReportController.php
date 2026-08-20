<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\SalaryAdvance;
use Auth;
use Carbon\Carbon;
use Datatables;

class SalaryAdvanceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-list');

        if(!$permission) {
            abort(403);
        }

        return view('Report.salaryAdvanceReport');
    }
}