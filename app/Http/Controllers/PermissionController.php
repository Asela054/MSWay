<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
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
         
        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'module' => $request->module, 
        ]);
        $modules = DB::table('permissions')->groupBy('module')->get();
       return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }
    public function edit($id)
    {
        $permission =DB::table('permissions')->where('id',$id)->first();
        $modules = DB::table('permissions')->groupBy('module')->get();
        return view('permission.edit',compact('modules','permission'));
        
       
    }

     public function update(Request $request,$id)
    {
        DB::table('permissions')
        ->where('id', $id)
        ->update([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name'),
            'module' => $request->input('module'),
        ]);

    return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
       
    }
    public function destroy($id)
        {
            DB::table('permissions')->where('id', $id)->delete();

            return redirect()->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        }


}
