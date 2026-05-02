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
        // Previously it was allergen
        Schema::create('allergens', function (Blueprint $table) {
            $table->id('allergen_id');
            $table->string('name');
            $table->integer('deliverect_value')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('allergens', function (Blueprint $table) {
            $table->renameColumn('allergen_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergens');
    }
};
