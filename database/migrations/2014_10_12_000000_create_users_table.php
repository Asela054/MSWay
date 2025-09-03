<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('emp_id');            
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('deleted')->nullable(); 
            $table->string('status')->nullable(); 
            $table->timestamp('date_entered')->nullable(); 
            $table->timestamp('date_modified')->nullable(); 
            $table->string('modified_user_id')->nullable(); 
            $table->string('created_by')->nullable();      
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
