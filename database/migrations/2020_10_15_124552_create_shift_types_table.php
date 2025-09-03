<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_types', function (Blueprint $table) {
            $table->increments('id');
             $table->string('shift_name');
             $table->string('onduty_time');
             $table->string('offduty_time');
             $table->string('late_time');
             $table->string('leave_early_time');
             $table->string('begining_checkin');
             $table->string('begining_checkout');
             $table->string('ending_checkin');
             $table->string('ending_checkout');
             $table->string('workdays_count');
             $table->string('minute_count');
             $table->string('must_checkin');
             $table->string('must_checkout');
             $table->string('color');
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
        Schema::dropIfExists('shift_types');
    }
}
