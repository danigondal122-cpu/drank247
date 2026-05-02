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
        Schema::create('stock_products', function (Blueprint $table) {
            $table->id('_id');
            $table->string('_name');
            $table->string('_price');
            $table->text('_description');
            $table->string('_articleNumber');
            $table->string('_alcohol');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stock_products', function (Blueprint $table) {
            $table->renameColumn('_id', 'id');
            $table->renameColumn('_name', 'name');
            $table->renameColumn('_price', 'price');
            $table->renameColumn('_description', 'description');
            $table->renameColumn('_articleNumber', 'article_number');
            $table->renameColumn('_alcohol', 'alcohol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_products');
    }
};
