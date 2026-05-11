<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ERP_KT\SpecialRate;
use Illuminate\Support\Facades\DB;
use Validator;

class ERPSpecialRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-list');
        if (!$permission) {
            abort(403);
        }
        return view('ERP_KT.special_rate');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'job_title'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'job_title'=>$request->job_title,
            'emp_id'=>$request->employee,
            'rate'=>$request->rate,
            'remarks'=>$request->remarks,
        );

        $special_rate=new SpecialRate;
        $special_rate->job_title=$request->input('job_title');
        $special_rate->emp_id=$request->input('employee');
        $special_rate->rate=$request->input('rate');
        $special_rate->remarks=$request->input('remarks');       
        $special_rate->save();

        return response()->json(['success' => 'Speical Rate Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = SpecialRate::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
          'job_title'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

       $form_data = array(
            'job_title'=>$request->job_title,
            'emp_id'=>$request->employee,
            'rate'=>$request->rate,
            'remarks'=>$request->remarks,
        );

        SpecialRate::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Special Rate Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-machine-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = SpecialRate::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Special Rate Deleted Successfully.']);
    }

     public function jobTitleList(Request $request)
    {
        $search = $request->term;
        $titles = DB::table('job_titles')
            ->where('title', 'like', '%' . $search . '%')
            ->get(['id', 'title']);
        return response()->json(['results' => $titles->map(function ($t) {
            return ['id' => $t->id, 'text' => $t->title];
        })]);
    }

    //job_title_id filter by employee
    public function employeeListByTitle(Request $request)
    {
        $search       = $request->term;
        $jobTitleId   = $request->job_title_id;
        $query = DB::table('employees AS e')
            ->join('job_titles AS jt', 'jt.id', '=', 'e.emp_job_code');
        if ($jobTitleId) {
            $query->where('e.emp_job_code', $jobTitleId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('e.emp_name_with_initial', 'like', '%' . $search . '%')
                    ->orWhere('e.calling_name', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->get(['e.emp_id', DB::raw("CONCAT(e.emp_name_with_initial,' - ',e.calling_name) as emp_name")]);

        return response()->json(['results' => $employees->map(function ($e) {
            return ['id' => $e->emp_id, 'text' => $e->emp_name];
        })]);
    }
}
