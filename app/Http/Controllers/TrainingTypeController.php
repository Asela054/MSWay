<?php

namespace App\Http\Controllers;

use App\TrainingType;
use App\TrainingAllocation;
use App\TrainingEmpAllocation;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TrainingTypeController extends Controller
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
        $user = auth()->user();
        $permission = $user->can('trainingType-list');
        if (!$permission) {
            abort(403);
        }

        $type= TrainingType::orderBy('id', 'asc')
            ->where('status',1)
            ->get();
        return view('Training_Management.trainingType',compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $user = auth()->user();
        $permission = $user->can('trainingType-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'=>  $request->name,
            'purpose'=>  $request->purpose
            
        );

        $type=new TrainingType;
        $type->name=$request->input('name');
        $type->purpose=$request->input('purpose'); 
        $type->status=1;      
        $type->save();

        return response()->json(['success' => 'Training Type Added Successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TrainingType  $type
     * @return \Illuminate\Http\Response
     */
    public function show(TrainingType $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TrainingType  $type
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingType-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = TrainingType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TrainingType  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrainingType $type)
    {
        $user = auth()->user();
        $permission = $user->can('trainingType-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'    =>  $request->name,
            'purpose'    =>  $request->purpose
            
        );
        TrainingType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TrainingType  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('trainingType-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            $data = TrainingType::findOrFail($id);
            $data->status = 3;
            $data->save();
            
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete record'], 500);
        }
    }

    // Type List
    public function trainType_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = TrainingType::where('name', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('status', 1)
                ->orderBy('name')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'),DB::raw('name as text'), DB::raw('name as name')]);

            $count = TrainingType::count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    public function trainVenue_list_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $type = Input::get('type');

            if (empty($type)) {
            }
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = TrainingAllocation::where('venue', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('status', 1)
                ->where('type_id', $type)
                ->orderBy('venue')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('id as id'),DB::raw('venue as text')]);

            $count = TrainingAllocation::where('type_id', $type)->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    public function trainEmp_list_sel2(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->input('page', 1);
            $venue = $request->input('venue', '');
            $term = $request->input('term', '');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $query = TrainingEmpAllocation::join('employees', 'training_emp_allocations.emp_id', '=', 'employees.emp_id')
                ->where('training_emp_allocations.allocation_id', $venue)
                ->select(
                    DB::raw('employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                );
            if (!empty($term)) {
                $query->where(function ($q) use ($term) {
                    $q->where('employees.emp_name_with_initial', 'LIKE', '%' . $term . '%')
                        ->orWhere('employees.calling_name', 'LIKE', '%' . $term . '%');
                });
            }

            $breeds = $query
                ->select(
                    DB::raw('DISTINCT employees.emp_id as id'),
                    DB::raw('CONCAT(employees.emp_name_with_initial, " - ", employees.calling_name) as text')
                )
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = TrainingEmpAllocation::where('allocation_id', $venue)->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }
}
