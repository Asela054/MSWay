<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    function __construct()
    {
    }


    public function index(Request $request)
    {

        $user = \Auth::user();
        $permission = $user->can('role-list');

        if(!$permission){
            abort(403);
        }

        
        $permission = Permission::get();
        return view('roles.index',compact('roles','permission'));
    }

    public function create()
    {
        $user = \Auth::user();
        $permission = $user->can('role-create');

        if(!$permission){
            abort(403);
        }

        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }


    public function store(Request $request)
    {
       
        $user = \Auth::user();
        $permission = $user->can('role-create');

        if(!$permission){
            abort(403);
        }

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->permissions()->sync($request->input('permission'));

        return response()->json(['success' => 'Role successfully Created']);
    }

    public function show($id)
    {

        $user = \Auth::user();
        $permission = $user->can('role-list');

        if(!$permission){
            abort(403);
        }

        $role = Role::find($id);
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        //return view('roles.show',compact('role','rolePermissions'));
         return response()->json([
            'role' => $role,
            'rolePermissions' => $rolePermissions,
        ]);
    }


    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        $perms_with_modules = DB::table("permissions")
            ->select('module')
            ->groupBy("module")
            ->get()
            ->toArray();

        return response()->json([
            'role' => $role,
            'permission' => $permission,
            'rolePermissions' => $rolePermissions,
            'perms_with_modules' => $perms_with_modules,
        ]);
    }



    public function update(Request $request)
    {
        $user = \Auth::user();
        $permission = $user->can('role-edit');

        if(!$permission){
            abort(403);
        }

        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $id = $request->input('hidden_id');

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->permissions()->sync($request->input('permission'));

        $userRole = $user->roles->pluck('name','name')->all();

        $user->roles()->detach();
        $user->assignRole($userRole);

        return response()->json(['success' => 'Role successfully Updated']);
    }

    public function destroy($id)
    {
        $user = \Auth::user();
        $permission = $user->can('role-delete');

        if(!$permission){
            abort(403);
        }

     
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to remove Role.')]);
        }

        $data = Role::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
