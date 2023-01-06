<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('empid')->unique();
            // $table->foreign('empid')->references('employee_id')->on('users')->onUpdate('cascade');
            $table->date('start_Date')->nullable();
            $table->string('employee_grade')->nullable();
            // $table->string('employee_name');
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('employement_type')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('land_ext')->nullable();
            $table->string('mobile_number')->nullable();
            // $table->string('email')->unique();
            $table->string('reporting_to')->nullable();
            $table->date('birth_dates')->nullable();
            $table->string('address', 1000)->nullable();
            $table->string('probation_period')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->string('remarks')->nullable();
            $table->date('new_confirmationdate')->nullable();
            $table->date('terminationdate')->nullable();
            $table->date('resignation')->nullable();
            $table->date('last_workingdate')->nullable();
            $table->string('status')->default('active');
            $table->boolean('display_to_employees')->default(1);
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `employees` ADD `profile_pic` MEDIUMBLOB AFTER `birth_dates`");
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
