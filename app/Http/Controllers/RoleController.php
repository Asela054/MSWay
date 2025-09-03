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

        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }

    public function show($id)
    {

        $user = \Auth::user();
        $permission = $user->can('role-list');

        if(!$permission){
            abort(403);
        }

        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        return view('roles.show',compact('role','rolePermissions'));
    }


    public function edit($id)
    {
        $user = \Auth::user();
        $permission = $user->can('role-edit');

        if(!$permission){
            abort(403);
        }

        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        $perms_with_modules = DB::table("permissions")
            ->select('module')
            ->groupBy("module")
            ->get()
            ->toArray();

        return view('roles.edit',compact('role','permission','rolePermissions', 'perms_with_modules'));
    }


    public function update(Request $request, $id)
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

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->permissions()->sync($request->input('permission'));

        $userRole = $user->roles->pluck('name','name')->all();

        $user->roles()->detach();
        $user->assignRole($userRole);

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }

    public function destroy($id)
    {
        $user = \Auth::user();
        $permission = $user->can('role-delete');

        if(!$permission){
            abort(403);
        }

        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}
