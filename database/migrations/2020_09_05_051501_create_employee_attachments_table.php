<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attachments', function (Blueprint $table) {
            $table->increments('emp_ath_id');
            $table->string('emp_id'); 
            $table->string('emp_ath_file_name'); 
            $table->string('emp_ath_size')->nullable(); 
            $table->string('emp_ath_type')->nullable(); 
            $table->string('emp_ath_by')->nullable(); 
            $table->string('emp_ath_time')->nullable(); 
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
        Schema::dropIfExists('employee_attachments');
    }
}
