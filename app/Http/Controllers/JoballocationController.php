<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Joballocation;
use App\Helpers\EmployeeHelper;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Session;
use Datatables;

class JoballocationController extends Controller
{
    public function index()
    {
        $employees=DB::table('employees')->select('id','emp_id','emp_name_with_initial','emp_job_code')
        ->where('deleted',0)
        ->where('is_resigned',0)
        ->get();
        $locations=DB::table('branches')->select('*')->get();
        return view('jobmanagement.joballocation',compact('locations','employees'));
    }

    public function insert(Request $request)
    {
        $permission = \Auth::user()->can('Job-Allocation-create');
        if (!$permission) {
            abort(403);
        }

        $location = $request->input('allocation');
        $tableData = $request->input('tableData');

        foreach ($tableData as $rowtabledata) {
                $empid = $rowtabledata['col_1'];

                $allocation = new Joballocation();
                $allocation->location_id = $location;
                $allocation->employee_id = $empid;
                $allocation->status = '1';
                $allocation->created_by = Auth::id();
                $allocation->updated_by = '0';
                $allocation->save();
        }

        return response()->json(['success' => 'Job Allocation Added successfully.']);

    }


    public function allocationlist()
    {
        $allocation = DB::table('job_allocation')
        ->leftjoin('employees', 'job_allocation.employee_id', '=', 'employees.emp_id')
        ->leftjoin('branches', 'job_allocation.location_id', '=', 'branches.id')
        ->select('job_allocation.*','employees.emp_name_with_initial','employees.calling_name','branches.location')
        ->whereIn('job_allocation.status', [1, 2])
        ->get();
        return Datatables::of($allocation)
        ->addIndexColumn()
        ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
        })
        ->filterColumn('employee_display', function($query, $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
            });
        })
        ->addColumn('action', function ($row) {
            $btn = '';
                    if(Auth::user()->can('Job-Allocation-edit')){
                            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>'; 
                    }
                    if(Auth::user()->can('Job-Allocation-delete')){
                        $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit(Request $request)
    {
        $permission = \Auth::user()->can('Job-Allocation-edit');
        if (!$permission) {
            abort(403);
        }

        $id = Request('id');
        if (request()->ajax()){
        $data = DB::table('job_allocation')
        ->select('job_allocation.*')
        ->where('job_allocation.id', $id)
        ->get(); 
        return response() ->json(['result'=> $data[0]]);
        }
    }

    public function update(Request $request){
        $permission = \Auth::user()->can('Job-Allocation-edit');
        if (!$permission) {
            abort(403);
        }

        $editemployee = $request->input('editemployee');
        $editlocation = $request->input('editlocation');
        $hidden_id = $request->input('hidden_id');

            $data = array(
                'location_id' => $editlocation,
                'employee_id' => $editemployee,
                'updated_by' => Auth::id(),
            );
        
            Joballocation::where('id', $hidden_id)
            ->update($data);

        return response()->json(['success' => 'Job Allocation Updated successfully.']);
    }

    public function delete(Request $request){
        $id = Request('id');
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id()
        );
        Joballocation::where('id',$id)
        ->update($form_data);

          return response()->json(['success' => 'Job Allocation is Successfully Deleted']);
    }

}
