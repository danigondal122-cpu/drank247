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
        // Previously it was dp_history
        Schema::create('delivery_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('history_dpid');
            $table->date('history_date');
            $table->datetime('history_starttime');
            $table->datetime('history_endtime')->nullable();
            $table->string('start_odometer');
            $table->string('end_odometer');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('delivery_histories', function (Blueprint $table) {
            $table->renameColumn('history_id', 'id');
            $table->renameColumn('history_dpid', 'delivery_person_id');
            $table->renameColumn('history_starttime', 'history_start_time');
            $table->renameColumn('history_endtime', 'history_end_time');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_histories');
    }
};
