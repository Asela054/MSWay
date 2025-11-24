<?php

namespace App\Http\Controllers;

use App\PayGroup;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class PayGroupController extends Controller
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
        $permission = $user->can('pay-group-list');
        if (!$permission) {
            abort(403);
        }

        $group = PayGroup::join('users', 'user_has_pay_groups.user_id', '=', 'users.id')
            ->select('user_has_pay_groups.*', 'users.name as user_name')
            ->orderBy('user_has_pay_groups.id', 'asc')
            ->get();
            
        $users = User::orderBy('id', 'asc')->get();
        return view('UserPayGroup.userGroup', compact('group', 'users'));
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
        $permission = $user->can('pay-group-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'user' => 'required',
            'pay_group' => 'required|integer'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $group = PayGroup::create([
            'user_id' => $request->user,
            'group_id' => $request->pay_group
        ]);

        if ($group) {
            return response()->json(['success' => 'Data Added successfully.']);
        }
        
        return response()->json(['errors' => ['Failed to save data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\PayGroup $group
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('pay-group-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = PayGroup::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PayGroup $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayGroup $group)
    {
        $user = Auth::user();
        $permission = $user->can('pay-group-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'user' => 'required',
            'pay_group' => 'required|integer'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'user_id' => $request->user,
            'group_id' => $request->pay_group
        );

        PayGroup::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\PayGroup $group
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('pay-group-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = PayGroup::findOrFail($id);
        $data->delete();
    }
}
