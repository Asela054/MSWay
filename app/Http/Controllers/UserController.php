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
        $user     = User::find($id);
        $roles    = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name')->first();

        // Use distinct to avoid duplicates when multiple branches exist per company
        $userCompanies = DB::table('user_has_companies')
            ->join('companies', 'user_has_companies.company_id', '=', 'companies.id')
            ->where('user_has_companies.user_id', $id)
            ->select('companies.id', 'companies.name as text')
            ->distinct()
            ->get()
            ->map(function ($company) {
                return ['id' => $company->id, 'text' => $company->text];
            });

        $userLocations = DB::table('user_has_companies')
            ->join('branches', 'user_has_companies.branch_id', '=', 'branches.id')
            ->where('user_has_companies.user_id', $id)
            ->whereNotNull('user_has_companies.branch_id')
            ->select('branches.id', 'branches.location as text')
            ->distinct()
            ->get()
            ->map(function ($branch) {
                return ['id' => $branch->id, 'text' => $branch->text];
            });

        return response()->json([
            'result' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $userRole,
                'companies' => $userCompanies,
                'locations' => $userLocations,
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

        $companies = $request->input('company', []);
        $locations = $request->input('location', []);

        if (!empty($companies)) {
            foreach ($companies as $companyId) {
                if (!empty($locations)) {
                    // Create one row per branch under this company
                    foreach ($locations as $locationId) {
                        UserCompany::create([
                            'user_id'    => $user->id,
                            'company_id' => $companyId,
                            'branch_id'  => $locationId,
                        ]);
                    }
                } else {
                    // No branches selected — store company with null branch
                    UserCompany::create([
                        'user_id'    => $user->id,
                        'company_id' => $companyId,
                        'branch_id'  => null,
                    ]);
                }
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

        $companies = $request->input('company', []);
        $locations = $request->input('location', []);

        if (!empty($companies)) {
            foreach ($companies as $companyId) {
                if (!empty($locations)) {
                    foreach ($locations as $locationId) {
                        UserCompany::create([
                            'user_id'    => $id,
                            'company_id' => $companyId,
                            'branch_id'  => $locationId,
                        ]);
                    }
                } else {
                    UserCompany::create([
                        'user_id'    => $id,
                        'company_id' => $companyId,
                        'branch_id'  => null,
                    ]);
                }
            }
        }

        return response()->json(['success' => 'User successfully Updated']);
    }
}
