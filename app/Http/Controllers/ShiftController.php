<?php

namespace App\Http\Controllers;

use App\Shift;
use App\Employee;
use App\Helpers\EmployeeHelper;
use App\ShiftType;
use App\Branch;
use App\JobCategory;
use Illuminate\Http\Request;
use Validator;
use DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('shift-list');
        if(!$permission) {
            abort(403);
        }
        $shifttype= ShiftType::where('deleted', 0)->orderBy('id', 'asc')->get();
        $employee=Employee::orderBy('id', 'desc')->get();
        $branch=Branch::orderBy('id', 'desc')->get();
        $jobcategories = JobCategory::orderBy('id', 'asc')->get();

        return view('Shift.shift',compact('employee','shifttype','branch','jobcategories'));
    }

    public function shift_list_dt(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('shift-list');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');

        $query = DB::table('employees')
            ->leftjoin('shift_types', 'shift_types.id', '=',   'employees.emp_shift')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->leftjoin('job_categories', 'job_categories.id', '=', 'employees.job_category_id')
             ->where(function($q) {
                    $q->where('shift_types.deleted', 0)->orWhereNull('shift_types.deleted');
                })
            ->select('employees.emp_id',
                'employees.calling_name',
                'employees.emp_first_name',
                'shift_types.shift_name',
                'shift_types.onduty_time',
                'shift_types.offduty_time',
                'employees.emp_name_with_initial',
                'shift_types.id as shift_type_id',
                'departments.name as dep_name',
                'employees.job_category_id',
                'job_categories.category'
            );


        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['employees.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['employees.emp_location' => $location]);
        }

        $data = $query->get();

        return Datatables::of($data)
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
            ->addColumn('action', function($row){

                $btn = ' <button name="edit"
                                        data-id="'.$row->emp_id.'"
                                        data-emp_name_with_initial="'.$row->emp_name_with_initial.'"
                                        data-shift_name="'.$row->shift_name.'"
                                        data-onduty_time="'.$row->onduty_time.'"
                                        data-offduty_time="'.$row->offduty_time.'"
                                        data-shift_type_id="'.$row->shift_type_id.'"
                                        data-job_category_id="'.($row->job_category_id ?? '').'"
                                        class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button> ';
                $btn .= '<button type="submit" name="delete" data-id="'.$row->emp_id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getshift(){
        $user = Auth::user();
        $permission = $user->can('shift-list');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $data = DB::table('employees')            
        ->leftjoin('shift_types', 'employees.emp_shift', '=', 'shift_types.id')
        ->select('employees.id', 'employees.emp_first_name', 'shift_types.shift_name', 'shift_types.onduty_time', 'shift_types.offduty_time')
        ->get();

        $shifttype= ShiftType::orderBy('id', 'asc')->get();

        return response()->json($data);
        return response()->json($shifttype);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function Shiftupdate(Request $request){
        $user = Auth::user();
        $permission = $user->can('shift-edit');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        if($request->ajax())
        {
            $data = array(
                'shift_id'       =>  $request->shifttype
            );
                DB::table('employees')
                ->where('id', $request->empid)
                ->update(['emp_shift' => $request->shifttype]);

             //   return response()->json(['success' => 'Data is successfully updated']);
            echo '<div class="alert alert-success">Shift Updated</div>';
        }

    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('shift-create');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $rules = array(
            'employee'    =>  'required',
            'shift'    =>  'required'
        
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'emp_id'        =>  $request->employee,
            'shift'        =>  $request->shift
           
            
        );

       $shift=new Shift;
       $shift->emp_id=$request->input('employee');       
       $shift->shift_id=$request->input('shift');       
       
       $shift->save();

       

        return response()->json(['success' => ' Employee Added to Shift.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('shift-edit');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }
      
        if(request()->ajax())
        {
            $data = Shift::findOrFail($id);
        
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shift $shift)
    {
        $user = Auth::user();
        $permission = $user->can('shift-edit');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        $rules = array(
            'uid'    =>  'required',
            'shift'    =>  'required'
               
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

     

        $shift=new Shift;
        $shift->emp_id=$request->uid;    
        $shift->shift_id=$request->shift; 
        
        $shift->save();
        
        $updateData = ['emp_shift' => $request->shift];
        if ($request->has('job_category') && $request->job_category != '') {
            $updateData['job_category_id'] = $request->job_category;
        }

        DB::table('employees')
        ->where('emp_id', $request->uid)
        ->update($updateData);
       

        return response()->json(['success' => 'Employee Shift & Job Category Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('shift-delete');
        if(!$permission) {
             return response()->json(['error' => 'UnAuthorized']);
        }

        DB::table('employees')
            ->where('emp_id', $id)
            ->update(['emp_shift' => '']);

        return response()->json(['success' => 'Employee Shift Deleted']);
    }

    public function dpt_allocation_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('shift-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->input('department');

        $empList = DB::table('employees')
            ->where('emp_department', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->orderBy('emp_name_with_initial')
            ->select('emp_id', 'emp_name_with_initial', 'emp_shift', 'job_category_id')
            ->get();

        $shiftTypes    = ShiftType::where('deleted', 0)->orderBy('id')->get();
        $jobCategories = JobCategory::orderBy('id')->get();

        if ($empList->isEmpty()) {
            $html = '<tr><td colspan="4" class="text-center text-muted">No employees found for the selected department.</td></tr>';
            return response()->json(['html' => $html]);
        }

        $html = '';
        foreach ($empList as $emp) {
            // Build Shift select
            $shiftSelect  = '<select class="form-control form-control-sm shift-select">';
            $shiftSelect .= '<option value="">-- Select Shift --</option>';
            foreach ($shiftTypes as $st) {
                $selected     = ($emp->emp_shift == $st->id) ? ' selected' : '';
                $shiftSelect .= '<option value="' . $st->id . '"' . $selected . '>' . htmlspecialchars($st->shift_name) . ' - ' . htmlspecialchars($st->shift_code) . '</option>';
            }
            $shiftSelect .= '</select>';

            // Build Job Category select
            $jobSelect  = '<select class="form-control form-control-sm job-cat-select">';
            $jobSelect .= '<option value="">-- Select --</option>';
            foreach ($jobCategories as $jc) {
                $selected  = ($emp->job_category_id == $jc->id) ? ' selected' : '';
                $jobSelect .= '<option value="' . $jc->id . '"' . $selected . '>' . htmlspecialchars($jc->category) . '</option>';
            }
            $jobSelect .= '</select>';

            $html .= '<tr data-emp-id="' . $emp->emp_id . '">';
            $html .= '<td>' . $emp->emp_id . '</td>';
            $html .= '<td>' . htmlspecialchars($emp->emp_name_with_initial) . '</td>';
            $html .= '<td>' . $shiftSelect . '</td>';
            $html .= '<td>' . $jobSelect . '</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }


    /**
     * Bulk-update emp_shift and job_category_id for a list of employees.
     */
    public function dpt_allocation_update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('shift-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tableData = $request->input('tableData', []);

        if (empty($tableData)) {
            return response()->json(['errors' => 'No employee data provided.']);
        }

        try {
            DB::beginTransaction();

            foreach ($tableData as $row) {
                $emp_id          = $row['emp_id'];
                $shift_id        = $row['shift_id']        ?? null;
                $job_category_id = $row['job_category_id'] ?? null;

                $updateData = [];
                if (!is_null($shift_id) && $shift_id !== '') {
                    $updateData['emp_shift'] = $shift_id;
                }
                if (!is_null($job_category_id) && $job_category_id !== '') {
                    $updateData['job_category_id'] = $job_category_id;
                }

                if (!empty($updateData)) {
                    DB::table('employees')
                        ->where('emp_id', $emp_id)
                        ->update($updateData);
                }
            }

            DB::commit();
            return response()->json(['success' => 'Shifts & Job Categories updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'An error occurred: ' . $e->getMessage()], 422);
        }
    }
}
