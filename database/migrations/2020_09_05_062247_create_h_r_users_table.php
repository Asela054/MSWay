<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHRUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_r_users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('emp_id'); 
            $table->string('user_name'); 
            $table->string('user_password'); 
            $table->string('deleted'); 
            $table->string('status'); 
            $table->string('date_entered'); 
            $table->string('date_modified'); 
            $table->string('modified_user_id'); 
            $table->string('created_by');      
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h_r_users');
    }
}
