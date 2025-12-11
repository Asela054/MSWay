<?php

namespace App\Http\Controllers;

use App\Coverup_detail;
use App\Employee;
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Datatables;



class CoverupController extends Controller
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
        $permission = Auth::user()->can('Coverup-list');
        if (!$permission) {
            abort(403);
        }
        $employee = Employee::orderBy('id', 'desc')->get();

        return view('Leave.coverup_details', compact('employee'));
    }

    public function coverup_list_dt(Request $request)
    {
        $permission = Auth::user()->can('Coverup-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');

        // Get accessible employee IDs based on user access rights
        $userId = Auth::id();
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId);

        $query =  DB::table('coverup_details')
            // ->join('employees as ec', 'coverup_details.emp_id', '=', 'ec.emp_id')
            ->join('employees as e', 'coverup_details.emp_id', '=', 'e.emp_id')
            ->leftjoin('branches', 'e.emp_location', '=', 'branches.id')
            ->leftjoin('departments', 'e.emp_department', '=', 'departments.id')
            ->select('coverup_details.*', 'e.emp_name_with_initial','e.calling_name', 'departments.name as dep_name');

         // Apply user access rights filter
        if (!empty($accessibleEmployeeIds)) {
            $query->whereIn('e.emp_id', $accessibleEmployeeIds);
        }

        if($department != ''){
            $query->where(['departments.id' => $department]);
        }

        if($employee != ''){
            $query->where(['e.emp_id' => $employee]);
        }

        if($location != ''){
            $query->where(['e.emp_location' => $location]);
        }

        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
              ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
            })
                ->filterColumn('employee_display', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('e.emp_name_with_initial', 'like', "%{$keyword}%")
                    ->orWhere('e.calling_name', 'like', "%{$keyword}%")
                    ->orWhere('e.emp_id', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function($row){
                $btn = '';

                    $btn = ' <button name="edit" id="'.$row->id.'"
                            class="edit btn btn-primary btn-sm" style="margin:1px;" type="submit" data-toggle="tooltip" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </button> ';
               
                    $btn .= '<button type="submit" name="delete" id="'.$row->id.'"
                            class="delete btn btn-danger btn-sm" style="margin:1px;" data-toggle="tooltip" title="Remove"><i
                            class="far fa-trash-alt"></i></button>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $rules = array(
            'coveringemployee' => '',
            'date' => 'date',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $start_time = Carbon::createFromFormat('H:i', $request->input('start_time'));
        $end_time = Carbon::createFromFormat('H:i', $request->input('end_time'));

        $diffInMinutes = $start_time->diffInMinutes($end_time);
        $covering_hours = round($diffInMinutes / 60, 2); 

        $coverup_detail = new Coverup_detail;
        $coverup_detail->emp_id = $request->input('coveringemployee');
        $coverup_detail->date = $request->input('date');
        $coverup_detail->start_time = $request->input('start_time');
        $coverup_detail->end_time = $request->input('end_time');
        $coverup_detail->covering_hours = $covering_hours; 
        $coverup_detail->created_at = Carbon::now();
        $coverup_detail->save();

        return response()->json(['success' => 'Coverup Details Added Successfully']);
    }


    public function edit($id)
    {
        $permission = Auth::user()->can('Coverup-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        if (request()->ajax()) {
            $data = Coverup_detail::with('employee')
                ->with('covering_employee')
                ->findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Coverup_detail $coverup_detail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coverup_detail $coverup_detail)
    {
        $permission = Auth::user()->can('Coverup-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if ($request->has('start_time') && $request->has('end_time')) {
            try {
                $request->merge([
                    'start_time' => Carbon::parse($request->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($request->end_time)->format('H:i'),
                ]);
            } catch (\Exception $e) {
                return response()->json(['errors' => ['Invalid time format for start_time or end_time.']]);
            }
        }

        $rules = array(
            'hidden_id' => 'exists:coverup_details,id',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'date' => 'date',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $start_time = Carbon::createFromFormat('H:i', $request->start_time);
        $end_time = Carbon::createFromFormat('H:i', $request->end_time);

        $diffInMinutes = $start_time->diffInMinutes($end_time);
        $covering_hours = round($diffInMinutes / 60, 2); 
        
        $form_data = array(
            'emp_id' => $request->coveringemployee,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'covering_hours' => $covering_hours, 
            'updated_at' => Carbon::now(),
        );

        Coverup_detail::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Covering Details Successfully Updated']);
    }


    public function destroy($id)
    {
        $permission = Auth::user()->can('Coverup-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $data = Coverup_detail::findOrFail($id);
        $data->delete();
    }




}
