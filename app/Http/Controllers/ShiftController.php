<?php

namespace App\Http\Controllers;

use App\Shift;
use App\Employee;
use App\ShiftType;
use App\Branch;
use Illuminate\Http\Request;
use Validator;
use DB;
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
        $shifttype= ShiftType::orderBy('id', 'asc')->get();
        $employee=Employee::orderBy('id', 'desc')->get();
        $branch=Branch::orderBy('id', 'desc')->get();

    //   dd($shift);
        return view('Shift.shift',compact('employee','shifttype','branch'));
    }

    public function shift_list_dt(Request $request)
    {
        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');

        $query = DB::table('employees')
            ->leftjoin('shift_types', 'shift_types.id', '=',   'employees.emp_shift')
            ->leftjoin('departments', 'employees.emp_department', '=', 'departments.id')
            ->select('employees.emp_id',
                'employees.emp_first_name',
                'shift_types.shift_name',
                'shift_types.onduty_time',
                'shift_types.offduty_time',
                'employees.emp_name_with_initial',
                'shift_types.id as shift_type_id',
                'departments.name as dep_name'
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
            ->addColumn('action', function($row){

                $btn = ' <button name="edit"
                                        data-id="'.$row->emp_id.'"
                                        data-emp_name_with_initial="'.$row->emp_name_with_initial.'"
                                        data-shift_name="'.$row->shift_name.'"
                                        data-onduty_time="'.$row->onduty_time.'"
                                        data-offduty_time="'.$row->offduty_time.'"
                                        data-shift_type_id="'.$row->shift_type_id.'"
                                        class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button> ';
                $btn .= '<button type="submit" name="delete" data-id="'.$row->emp_id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getshift(){
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
        
        DB::table('employees')
        ->where('emp_id', $request->uid)
        ->update(['emp_shift' => $request->shift]);
       

        return response()->json(['success' => 'Employee Shift Changed']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('employees')
            ->where('emp_id', $id)
            ->update(['emp_shift' => '']);

        return response()->json(['success' => 'Employee Shift Deleted']);
    }
}
