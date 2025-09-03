<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFingerprintUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fingerprint_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userid'); 
            $table->string('name'); 
            $table->string('cardno'); 
            $table->string('uid'); 
            $table->string('role'); 
            $table->string('password'); 
            $table->string('devicesno'); 
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
        Schema::dropIfExists('fingerprint_users');
    }
}
