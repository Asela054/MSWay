<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('emp_id'); 
            $table->string('emp_level'); 
            $table->string('emp_institute'); 
            $table->string('emp_specification'); 
            $table->string('emp_year'); 
            $table->string('emp_gpa'); 
            $table->string('emp_start_date'); 
            $table->string('emp_end_date'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_educations');
    }
}
