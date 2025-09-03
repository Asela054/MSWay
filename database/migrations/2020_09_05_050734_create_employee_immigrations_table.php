<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeImmigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_immigrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('emp_id');           
            $table->date('emp_imm_issue_date');           
            $table->date('emp_imm_expire_date');           
            $table->string('emp_imm_eligible'); 
            $table->string('emp_imm_issueed_by'); 
            $table->date('emp_imm_review_date'); 
            $table->text('emm_imm_comments'); 
            $table->string('insert_user')->nullable();
            $table->timestamps('insert_date');
            $table->string('update_user')->nullable();
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
        Schema::dropIfExists('employee_immigrations');
    }
}
