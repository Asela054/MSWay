<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWorkRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_work_rates', function (Blueprint $table) {
            $table->increments('id')->length(11);
            $table->integer('emp_id')->length(11);
            $table->string('emp_etfno')->length(255);
            $table->integer('work_year')->length(11);
            $table->integer('work_month')->length(11);
            $table->tinyInteger('work_days')->length(4);
            $table->tinyInteger('leave_days')->length(4);
            $table->tinyInteger('nopay_days')->length(4);
            $table->double('normal_rate_otwork_hrs',10,2);
            $table->double('double_rate_otwork_hrs',10,2);
            $table->string('created_by')->length(255);
            $table->string('updated_by')->length(255);
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
        Schema::dropIfExists('employee_work_rates');
    }
}
