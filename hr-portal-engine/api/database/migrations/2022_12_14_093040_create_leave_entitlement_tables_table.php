<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveEntitlementTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_entitlement_tables', function (Blueprint $table) {
            $table->id();
            $table->string('empid')->nullable();
            $table->string('annual_leave')->nullable();
            $table->string('sick_leave')->nullable();
            $table->string('optional_leave')->nullable();
            $table->string('compensatory_leave')->nullable();
            $table->string('marriage_leave')->nullable();
            $table->string('bereavement_leave')->nullable();
            $table->string('maternity_leave')->nullable();
            $table->string('paternity_leave')->nullable();
            $table->string('childcare_leave')->nullable();
            $table->string('national_service_leave')->nullable();
            $table->string('hospitalization_leave')->nullable();
            $table->string('carers_leave')->nullable();
            $table->timestamps();
        });
        Schema::table('leave_entitlement_tables', function (Blueprint $table) {
            $table->foreign('empid')->references('employee_id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_entitlement_tables');
    }
}
