<?php

namespace App\Http\Controllers;

use App\AttendanceClear;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AttendanceClearController extends Controller
{
    public function attendance_clear_list(Request $request)
    {
        $permission = Auth::user()->can('attendance-device-clear-list');
        if(!$permission){
            abort(403);
        }

        return view('Attendent.attendance_clear_list');
    }

    public function attendance_clear_list_dt(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('attendance-device-clear-list');
        if (!$permission) {
            return response()->json(['error' => 'You do not have permission.']);
        }


        $query = AttendanceClear::with('user', 'device', 'branch');
        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}
