<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserCompany;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Validator;


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
        
        // $userCompany = new UserCompany;
        // $userCompany->user_id = $user->id;
        // $userCompany->company_id = Session::get('emp_company');
        // $userCompany->save();

        return response()->json(['success' => 'User Login is successfully Created']);
    }


    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }


    public function edit($id)
    {
        $user = User::find($id);
        $roles    = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name')->first();

        // Get assigned company IDs and format for Select2
        $userCompanies = $user->companies->map(function ($company) {
            return ['id' => $company->id, 'text' => $company->name];
        });

        return response()->json([
            'result' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $userRole,
                'companies' => $userCompanies,
            ],
            'roles' => $roles
        ]);
    }




    public function destroy($id)
    {
        $user = User::find($id);
        
        if ($user && $user->status != '1') {
            $user->delete();
        }
        
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['company_id'] = Session::get('emp_company');

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $companies = $request->input('company');
        if (!empty($companies)) {
            foreach ($companies as $companyId) {
                UserCompany::create([
                    'user_id'    => $user->id,
                    'company_id' => $companyId,
                ]);
            }
        }

        return response()->json(['success' => 'User successfully Created']);
    }

    public function update(Request $request)
    {
        $id = $request->input('hidden_id');

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }
        $input['company_id'] = Session::get('emp_company');

        $user = User::find($id);
        $user->update($input);

        $user->roles()->detach();
        $user->assignRole($request->input('roles'));

        UserCompany::where('user_id', $id)->delete();
        $companies = $request->input('company');
        if (!empty($companies)) {
            foreach ($companies as $companyId) {
                UserCompany::create([
                    'user_id'    => $id,
                    'company_id' => $companyId,
                ]);
            }
        }

        return response()->json(['success' => 'User successfully Updated']);
    }
}
