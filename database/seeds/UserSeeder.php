<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('users')->insert([
//            'emp_id' =>'1',
//            'name' =>'admin',
//            'email' =>'admin@gmail.com',
//            'password' => Hash::make('@gmail.com'),
//        ]);

        $user = User::where('emp_id', '1')->first();

        $role = Role::where('name', 'Admin')->first();

        $permissions = Permission::pluck('id','id')->all();

        //remove permission from role
        $role->revokePermissionTo($permissions);

        $role->permissions()->sync($permissions);

        $user->assignRole([$role->name]);

    }
}
