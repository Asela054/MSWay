<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_dependents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('emp_id'); 
            $table->string('emp_dep_name'); 
            $table->string('emp_dep_relation'); 
            $table->string('emp_dep_type')->nullable(); 
            $table->date('emp_dep_birthday');           
            $table->string('insert_user')->nullable();
            $table->timestamps('insert_date');
            $table->string('update_user')->nullable();
            $table->date('update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_dependents');
    }
}
