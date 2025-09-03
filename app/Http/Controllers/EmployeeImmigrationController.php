<?php

namespace App\Http\Controllers;

use App\EmployeeImmigration;
use App\EmployeeAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;

class EmployeeImmigrationController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
 
    {
        $user = Auth::user();
        $permission = $user->can('employee-edit');
        if (!$permission) {
            abort(403);
        }

        $this->validate($request, array(
            'issue_date' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'eligible' => 'required|string|max:255',  
            'review_date' => 'required|string|max:255',  
            'issueed_by' => 'required|string|max:255',  
            'comments' => 'required|string|max:255',
         ));

         
         $emp_id=$request->input('emp_id');

         $immigration=new EmployeeImmigration;
        
         $immigration->emp_id=$request->input('emp_id');
         $immigration->emp_imm_issue_date=$request->input('issue_date');
         $immigration->emp_imm_expire_date=$request->input('expire_date');
         $immigration->emp_imm_eligible=$request->input('eligible');
         $immigration->emp_imm_review_date=$request->input('review_date');
         $immigration->emp_imm_issueed_by=$request->input('issueed_by');
         $immigration->emm_imm_comments=$request->input('comments');

         $immigration->save();

         Session::flash('success','The Immigration Details Successfuly Saved');
      
     
         return redirect('viewImmigration/'.$emp_id);

    }

    public function immigrationattacment(Request $request){

        $permission = \Auth::user()->can('employee-edit');
        if (!$permission) {
            abort(403);
        }

        $file = $request->file('empattachment');
        $name = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = public_path('/Passport');
        $file->move($destinationPath, $name);


        $employeeattachment=new EmployeeAttachment;
            $emp_id=$request->input('id');
            $employeeattachment->emp_id=$request->input('id');
            $employeeattachment->emp_ath_file_name= $name;
            $employeeattachment->emp_ath_type='immigration';
            $employeeattachment->save();

            return redirect('viewImmigration/'.$emp_id);
    }


    public function show($id)
    {
        $permission = \Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $immigration = EmployeeImmigration::where('emp_id',$id)->get();

        //dd($immigration);
       
        return view('Employee.viewImmigration',compact('immigration','id'));
    }

    public function edit_json($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = EmployeeImmigration::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request )
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'issue_date' => 'required|string|max:255',
            'expire_date' => 'required|string|max:255',
            'eligible' => 'required|string|max:255',
            'review_date' => 'required|string|max:255',
            'issueed_by' => 'required|string|max:255',
            'comments' => 'required|string|max:255',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'emp_imm_issue_date' => $request->issue_date,
            'emp_imm_expire_date' => $request->expire_date,
            'emp_imm_eligible' => $request->eligible,
            'emp_imm_review_date' => $request->review_date,
            'emp_imm_issueed_by' => $request->issueed_by,
            'emm_imm_comments' => $request->comments,
        );

        EmployeeImmigration::whereId($request->edit_id)->update($form_data);

        return response()->json(['success' => 'Immigration updated successfully']);
    }

    /**
     * @throws \Exception
     */
    public function destroy($id)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = (new \App\EmployeeImmigration)->findOrFail($id);
        $data->delete();
    }
}
