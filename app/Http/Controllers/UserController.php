<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session; 
use Validator;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index(Request $request)
    {
        $data = User::orderBy('emp_id','DESC')->get();
        $roles = Role::pluck('name','name')->all();
        return view('users.index',compact('data','roles'));
    }


    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    public function usercreate(Request $request)
    {
        // $rules = array(
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'string|min:6|confirmed'
        // );

        // $error = Validator::make($request->all(), $rules);

        // if ($error->fails()) {
        //     return response()->json(['errors' => $error->errors()->all()]);
        // }
        // $user = new User;
        // $user->emp_id = $request->input('userid');
        // $user->email = $request->input('email');
        // $user->password = bcrypt($request['password']);
        // $user->save();

        // return response()->json(['success' => 'User Login is successfully Created']);
        $rules = array(
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'string|min:6|confirmed'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        $user = new User;
        $user->emp_id = $request->input('userid');
        $user->name = $request->input('name');
        $user->email = $request->input('email');   
        $user->company_id = Session::get('emp_company');
        $user->password = bcrypt($request['password']);
        $user->save();
        $user->assignRole('Employee');        

        return response()->json(['success' => 'User Login is successfully Created']);
    }


    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'company_id' => 'required',
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|same:confirm-password',
    //         'roles' => 'required'
    //     ]);

    //     $input = $request->all();
    //     $input['password'] = Hash::make($input['password']);

    //     $user = User::create($input);
    //     $user->assignRole($request->input('roles'));

    //     return redirect()->route('users.index')
    //                     ->with('success','User created successfully');
    // }


    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }


    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name')->first(); // single role name

        return response()->json([
            'result' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $userRole
            ],
            'roles' => $roles
        ]);
    }




    public function destroy($id)
    {
        User::find($id)->delete();
        
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['company_id'] = Session::get('emp_company');

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

         return response()->json(['success' => 'User successfully Created']);

        
    }

    public function update(Request $request)
    {
        
        $id = $request->input('hidden_id');
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }
        $input['company_id'] = Session::get('emp_company');
        $user = User::find($id);
        $user->update($input);

        //remove roles from user
        $user->roles()->detach();
        $user->assignRole($request->input('roles'));

        return response()->json(['success' => 'User successfully Updated']);
    }
}
