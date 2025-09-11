<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class EmployeeTaskProductController extends Controller
{
    public function employeetaskproduct()
    {
        $user = Auth::user();
        $permission = $user->can('task-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tasks = DB::table('task')
            ->select('id', 'taskname')
            ->get();

        $products = DB::table('product')
            ->select('id', 'productname')
            ->get();

        return view('Daily_Task.employee_task_product', compact('tasks', 'products'));
    }
}
