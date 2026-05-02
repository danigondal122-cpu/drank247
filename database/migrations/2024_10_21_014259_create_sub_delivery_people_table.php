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
        // Previously it was deliveryperson_sub
        Schema::create('sub_delivery_people', function (Blueprint $table) {
            $table->id('s_id');
            $table->foreignId('s_dpid');
            $table->foreignId('s_fid');
            // $table->text('s_pool');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sub_delivery_people', function (Blueprint $table) {
            $table->renameColumn('s_id', 'id');
            $table->renameColumn('s_dpid', 'delivery_person_id');
            $table->renameColumn('s_fid', 'franchise_id');

            $table->foreign('delivery_person_id')->references('id')->on('delivery_people');
            $table->foreign('franchise_id')->references('id')->on('franchises');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_delivery_people');
    }
};
