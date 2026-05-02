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
        // Previously it was schedule_absense
        Schema::create('schedule_absenses', function (Blueprint $table) {
            $table->id('s_abid');
            $table->foreignId('sa_dpid');
            $table->string('sa_starttime');
            $table->string('sa_endtime');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('schedule_absenses', function (Blueprint $table) {
            $table->renameColumn('s_abid', 'id');
            $table->renameColumn('sa_dpid', 'delivery_person_id');
            $table->renameColumn('sa_starttime', 'sa_start_time');
            $table->renameColumn('sa_endtime', 'sa_end_time');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_absenses');
    }
};
