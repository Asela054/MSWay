<?php

namespace App\Http\Controllers;

use App\FinancialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class FinancialCategoryController extends Controller
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
        $permission = $user->can('financial-category-list');
        if (!$permission) {
            abort(403);
        }

        $category = FinancialCategory::orderBy('id', 'asc')->get();
        return view('Employeermasterfiles.financialCategory', compact('category'));
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
        $permission = $user->can('financial-category-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'category' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $financial = FinancialCategory::create([
            'category' => $request->category
        ]);

        if ($financial) {
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        return response()->json(['errors' => ['Failed to save data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\FinancialCategory $financial
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('financial-category-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = FinancialCategory::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\FinancialCategory $financial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FinancialCategory $financial)
    {
        $user = Auth::user();
        $permission = $user->can('financial-category-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'category' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'category' => $request->category
        );

        FinancialCategory::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\FinancialCategory $financial
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('financial-category-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = FinancialCategory::findOrFail($id);
        $data->delete();
    }
}
