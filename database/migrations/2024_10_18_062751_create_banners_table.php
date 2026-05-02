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
        // Previously it was banner
        Schema::create('banners', function (Blueprint $table) {
            $table->id('_id');
            $table->string('image');
            $table->timestamps();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->renameColumn('_id', 'id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
