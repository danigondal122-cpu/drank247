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
        // TODO: remove this table?
        // Previously it was uber
        Schema::create('ubers', function (Blueprint $table) {
            $table->id();
            $table->integer('ordeR_id');
            $table->text('data');
            $table->timestamps();
        });

        Schema::table('ubers', function (Blueprint $table) {
            $table->renameColumn('ordeR_id', 'order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubers');
    }
};
