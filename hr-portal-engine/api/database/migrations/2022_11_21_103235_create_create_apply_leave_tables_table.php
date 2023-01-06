<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreateApplyLeaveTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('create_apply_leave_tables', function (Blueprint $table) {
            $table->id();
            $table->string('empid');
            $table->string('leave_type');
            $table->unsignedBigInteger('leave_id');
            $table->string('duration_type');
            $table->string('reason')->nullable();
            $table->string('num_of_days');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending');
            $table->string('comments')->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `create_apply_leave_tables` ADD `medical_certificate` MEDIUMBLOB AFTER `num_of_days`");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('create_apply_leave_tables');
    }
}
