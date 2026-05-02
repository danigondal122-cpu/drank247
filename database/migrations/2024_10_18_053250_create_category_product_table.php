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
        // Previously it was assign_product
        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('assign_proid');
            $table->foreignId('assign_catid');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->renameColumn('assign_proid', 'product_id');
            $table->renameColumn('assign_catid', 'category_id');

            $table->unique(['category_id', 'product_id']);

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
