<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Session;


class RptAssignedDevicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getassigneddevicelist()
    {
        $permission = Auth::user()->can('employee-report');
        if (!$permission) {
            abort(403);
        }

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }
        
        return view('Report.assignedDeviceReport', compact('company_name'));
    }
}