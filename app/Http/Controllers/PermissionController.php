<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use DB;

class PermissionController extends Controller
{
    public function index()
    {
         $modules = DB::table('permissions')->groupBy('module')->get();
         $permissions =DB::table('permissions')->get();
         return view('permission.index',compact('modules','permissions'));
       
    }
    public function create()
    {
         $modules = DB::table('permissions')->groupBy('module')->get();
         return view('permission.create',compact('modules'));
    }
     public function store(Request $request)
    {
        // $user = Auth::user();
        // $permission = $user->can('permissions-create');
        // if(!$permission) {
        //     return response()->json(['errors' => array('You do not have permission to insert Permission.')]);
        // }
        
         
        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'module' => $request->module, 
        ]);
        $modules = DB::table('permissions')->groupBy('module')->get();
         return response()->json(['success' => 'Permission created  successfully.']);
       
    }
    public function edit($id)
    {
        $permission =DB::table('permissions')->where('id',$id)->first();
        $modules = DB::table('permissions')->groupBy('module')->get();
        // return view('permission.edit',compact('modules','permission'));
        return response()->json([
            'permission' => $permission,
            'modules' => $modules
        ]);
        
       
    }

     public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('permissions-edit');
        if(!$permission) {
            return response()->json(['errors' => array('You do not have permission to update Permission.')]);
        }
        $id=$request->hidden_id;
        // dd($request);
        DB::table('permissions')
        ->where('id', $id)
        ->update([
            'name' => $request->name,
            'guard_name' => 'web',
            'module' => $request->module, 
        ]);

         return response()->json(['success' => 'Permission updated  successfully.']);
       
    }
    public function destroy($id)
        {
           

            $user = Auth::user();
            $permission = $user->can('permissions-delete');
            if(!$permission) {
                return response()->json(['errors' => array('You do not have permission to remove Permission.')]);
            }

            $data = Permission::findOrFail($id);
            $data->delete();

            return response()->json(['success' => 'Permission deleted successfully.']);

           
        }


}
