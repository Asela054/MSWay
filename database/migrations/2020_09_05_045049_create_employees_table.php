<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('emp_id');
            $table->string('emp_etfno');
            $table->string('emp_name_with_initial');
            $table->string('emp_first_name');
            $table->string('emp_med_name');
            $table->string('emp_last_name');
            $table->string('emp_status');
            $table->string('emp_nick_name')->nullable();
            $table->date('emp_birthday')->nullable();
            $table->string('emp_gender')->nullable();
            $table->string('emp_marital_status')->nullable();
            $table->string('emp_nationality')->nullable();
            $table->string('emp_salary_grade')->nullable();
            $table->date('emp_join_date')->nullable();
            $table->date('emp_permanent_date')->nullable();
            $table->string('emp_address')->nullable();
            $table->string('emp_address_2')->nullable();
            $table->string('emp_national_id')->nullable();
            $table->string('emp_con_mobile')->nullable();
            $table->string('emp_work_telephone')->nullable();
            $table->string('emp_mobile')->nullable();
            $table->string('emp_drive_license')->nullable();
            $table->string('emp_license_expire_date')->nullable();
            $table->string('emp_work_phone_no')->nullable();
            $table->string('emp_email')->nullable();
            $table->string('emp_other_email')->nullable();
            $table->string('emp_home_no')->nullable();
            $table->string('emp_location')->nullable();
            $table->string('emp_city')->nullable();
            $table->string('emp_province')->nullable();
            $table->string('emp_country')->nullable();
            $table->string('emp_postal_code')->nullable();
            $table->string('emp_job_code')->nullable();           
            $table->string('modified_user_id')->nullable(); 
            $table->string('created_by')->nullable();   
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
        Schema::dropIfExists('employees');
    }
}
