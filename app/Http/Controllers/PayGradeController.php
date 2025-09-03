<?php

namespace App\Http\Controllers;

use App\PayGrade;
use http\Env\Response;
use Illuminate\Http\Request;
use Validator;

class PayGradeController extends Controller
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
        $permission = $user->can('pay-grade-list');
        if(!$permission){
            abort(403);
        }

        $paygrade= PayGrade::orderBy('id', 'asc')->get();
        return view('Job.payGrade',compact('paygrade'));
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
        $permission = $user->can('pay-grade-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $rules = array(
            'paygrade'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'paygrade'        =>  $request->paygrade
            
        );

        $paygrade=new PayGrade;
       $paygrade->pay_grade=$request->input('paygrade');       
       $paygrade->save();

       

        return response()->json(['success' => 'Data Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PayGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function show(PayGrade $payGrade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PayGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('pay-grade-edit');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        if(request()->ajax())
        {
            $data = PayGrade::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PayGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayGrade $payGrade)
    {
        $user = auth()->user();
        $permission = $user->can('pay-grade-edit');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $rules = array(
            'paygrade'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'pay_grade'    =>  $request->paygrade
            
        );

        PayGrade::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Pay Grade is Successfully Updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PayGrade  $payGrade
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('pay-grade-delete');
        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 403);
        }

        $data = PayGrade::findOrFail($id);
        $data->delete();
    }
}
