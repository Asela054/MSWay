<?php

use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            'id' =>'1',
            'emp_etfno' =>'1',
            'emp_first_name' =>'admin',
            'emp_med_name' =>'admin',
            'emp_last_name' =>'admin',
                    
        ]);
    }
}
