<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_pictures', function (Blueprint $table) {
            $table->increments('emp_pic_id');
            $table->string('emp_id'); 
            $table->string('emp_pic_picture')->nullable(); 
            $table->string('emp_pic_filename')->nullable(); 
            $table->string('emp_file_width')->nullable(); 
            $table->string('emp_file_height')->nullable(); 
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
        Schema::dropIfExists('employee_pictures');
    }
}
