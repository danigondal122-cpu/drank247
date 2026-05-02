<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Previously it was delivery_timeschedule
        Schema::create('delivery_time_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->string('start_time0');
            $table->string('start_time1');
            $table->string('end_time0');
            $table->string('end_time1');
            $table->boolean('is_checked')->default(1)->comment('0->unchecked,1->checked');
            $table->timestamps();
        });

        Schema::table('delivery_time_schedules', function (Blueprint $table) {
            $table->renameColumn('start_time0', 'start_time_0');
            $table->renameColumn('start_time1', 'start_time_1');
            $table->renameColumn('end_time0', 'end_time_0');
            $table->renameColumn('end_time1', 'end_time_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_time_schedules');
    }
};
