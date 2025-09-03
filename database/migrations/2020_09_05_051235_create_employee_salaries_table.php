<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->increments('emp_sal_id');
            $table->string('emp_id'); 
            $table->string('emp_sal_grade'); 
            $table->string('emp_sal_currency'); 
            $table->double('emp_sal_basic_salary'); 
            $table->string('emp_sal_period_code'); 
            $table->string('emp_sal_comments'); 
            $table->string('emp_imm_eligible'); 
            $table->string('emp_sal_account'); 
            $table->double('emp_sal_amount'); 
            $table->string('emp_sal_ac_type'); 
            $table->string('emp_sal_transaction_type');
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
        Schema::dropIfExists('employee_salaries');
    }
}
