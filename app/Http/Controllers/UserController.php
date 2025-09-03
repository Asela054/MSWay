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

class UserController extends Controller
{

   public function index(Request $request)
    {
        $data = User::orderBy('emp_id','DESC')->get();
        return view('users.index',compact('data'));
    }


    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
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
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }


    // public function update(Request $request, $id)
    // {
    //     $this->validate($request, [
    //         'company_id' => 'required',
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,'.$id,
    //         'password' => 'same:confirm-password',
    //         'roles' => 'required'
    //     ]);

    //     $input = $request->all();
    //     if(!empty($input['password'])){
    //         $input['password'] = Hash::make($input['password']);
    //     }else{
    //         $input = Arr::except($input,array('password'));
    //     }

    //     $user = User::find($id);
    //     $user->update($input);

    //     //remove roles from user
    //     $user->roles()->detach();
    //     $user->assignRole($request->input('roles'));

    //     return redirect()->route('users.index')
    //                     ->with('success','User updated successfully');
    // }

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

        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    public function update(Request $request, $id)
    {
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

        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
}
