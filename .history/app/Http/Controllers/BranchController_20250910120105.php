<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use Validator;

class BranchController extends Controller
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
        $permission = $user->can('location-list');
        if(!$permission) {
            abort(403);
        }

        $branch= Branch::orderBy('id', 'asc')->get();
        return view('Organization.branch',compact('branch'));
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
        $permission = $user->can('location-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'location'    =>  'required',
            'contactno'    =>  'required|Numeric',
            'epf'    =>  'required',
            'etf'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $branch=new Branch;
       $branch->location=$request->input('location');       
       $branch->contactno=$request->input('contactno');       
       $branch->epf=$request->input('epf');
       $branch->etf=$request->input('etf');
       $branch->save();
       
        return response()->json(['success' => 'branch Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax())
        {
            $user = auth()->user();
            $permission = $user->can('location-edit');
            if(!$permission) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $data = Branch::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        $user = auth()->user();
        $permission = $user->can('location-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'location'    =>  'required|String',
            'contactno'    =>  'required|Numeric'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'location'    =>  $request->location,
            'contactno'        =>  $request->contactno
            
        );

        Branch::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Branch is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('location-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = Branch::findOrFail($id);
        $data->delete();
    }
}
