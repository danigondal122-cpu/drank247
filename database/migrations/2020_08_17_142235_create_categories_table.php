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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->foreignId('category_parentid')->nullable();
            $table->string('category_name');
            $table->string('image');
            $table->text('description');
            $table->boolean('is_popular')->default(false);
            $table->unsignedBigInteger('category_order');
            $table->boolean('is_show')->default(false);
            $table->foreignId('uber_product_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'id');
            $table->renameColumn('category_parentid', 'category_id');
            $table->renameColumn('uber_product_type', 'product_type_id');

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('product_type_id')->references('id')->on('product_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
