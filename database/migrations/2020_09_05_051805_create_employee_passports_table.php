<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePassportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_passports', function (Blueprint $table) {
            $table->increments('emp_pass_id');
            $table->string('emp_id'); 
            $table->date('emp_pass_issue_date'); 
            $table->date('emp_pass_expire_date'); 
            $table->text('emp_pass_comments'); 
            $table->string('emp_pass_type'); 
            $table->string('emp_pass_status'); 
            $table->string('emp_pass_review'); 
            $table->string('insert_user');
            $table->timestamps('insert_date');
            $table->string('update_user');
            $table->date('update_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_passports');
    }
}
