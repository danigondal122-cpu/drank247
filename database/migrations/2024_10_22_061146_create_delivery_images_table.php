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
        // Previously it was dp_images
        Schema::create('delivery_images', function (Blueprint $table) {
            $table->id('dp_im_id');
            $table->foreignId('dp_im_historyid');
            $table->enum('dp_im_type', ['start', 'end']);
            $table->string('dp_im_name');
            $table->timestamps();
        });

        Schema::table('delivery_images', function (Blueprint $table) {
            $table->renameColumn('dp_im_id', 'id');
            $table->renameColumn('dp_im_historyid', 'delivery_history_id');

            $table->foreign('delivery_history_id')->references('id')->on('delivery_histories');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_images');
    }
};
