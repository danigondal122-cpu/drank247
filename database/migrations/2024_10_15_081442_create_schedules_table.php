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
        // Previously it was schedule
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('s_id');
            $table->foreignId('s_dpid');
            $table->foreignId('s_fid');
            $table->string('s_time');
            $table->dateTime('s_startdate');
            $table->dateTime('s_enddate');
            $table->foreignId('s_pool');
            $table->integer('s_status')->default(9);
            $table->dateTime('s_approvedtime')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('s_id', 'id');
            $table->renameColumn('s_dpid', 'delivery_person_id');
            $table->renameColumn('s_fid', 'franchise_id');
            $table->renameColumn('s_time', 'time');
            $table->renameColumn('s_startdate', 'start_date');
            $table->renameColumn('s_enddate', 'end_date');
            $table->renameColumn('s_pool', 'pool_id');
            $table->renameColumn('s_status', 'status');
            $table->renameColumn('s_approvedtime', 'approved_time');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
            $table->foreign('franchise_id')->references('id')->on('franchises');
            $table->foreign('pool_id')->references('id')->on('pools');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
