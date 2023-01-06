<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveAccrualRulesTable extends Migration
{
    /**
     * Run the migrations.
    
     * @return void
     */
    public function up()
    {
        Schema::create('leave_accrual_rules', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('accrual')->nullable();
            $table->unsignedBigInteger('leave_type');
            // $table->string('no_of_days');
            $table->string('carry_forword')->nullable();
            $table->string('max_limit')->nullable();
            $table->timestamps();
        });

        Schema::table('leave_accrual_rules', function (Blueprint $table) {
            $table->foreign('leave_type')->references('id')->on('leave_names')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_accrual_rules');
    }
}
