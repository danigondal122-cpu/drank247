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
        // Previously it was assign_allergen
        Schema::create('allergen_product', function (Blueprint $table) {
            $table->foreignId('allergen_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['product_id', 'allergen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergen_product');
    }
};
