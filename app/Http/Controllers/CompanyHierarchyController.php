<?php

namespace App\Http\Controllers;

use App\CompanyHierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CompanyHierarchyController extends Controller
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
        $permission = $user->can('company-hierarchy-list');
        if (!$permission) {
            abort(403);
        }

        $hierarchy = CompanyHierarchy::orderBy('id', 'asc')->get();
        $lastOrderNumber = CompanyHierarchy::max('order_number') ?? 0;
        return view('Employeermasterfiles.companyHierarchy', compact('hierarchy', 'lastOrderNumber'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('company-hierarchy-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'position' => 'required',
            'order_number' => 'required|integer|min:1|unique:company_hierarchies'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $hierarchy = CompanyHierarchy::create([
            'position' => $request->position,
            'order_number' => $request->order_number
        ]);

        if ($hierarchy) {
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        return response()->json(['errors' => ['Failed to save data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CompanyHierarchy $hierarchy
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('company-hierarchy-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = CompanyHierarchy::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CompanyHierarchy $hierarchy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyHierarchy $hierarchy)
    {
        $user = Auth::user();
        $permission = $user->can('company-hierarchy-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'position' => 'required',
            'order_number' => 'required|integer|min:1|unique:company_hierarchies,order_number,' . $request->hidden_id
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'position' => $request->position,
            'order_number' => $request->order_number
        );

        CompanyHierarchy::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CompanyHierarchy $hierarchy
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('company-hierarchy-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = CompanyHierarchy::findOrFail($id);
        $data->delete();
    }
}
