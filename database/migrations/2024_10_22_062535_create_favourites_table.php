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
        // Previously it was favourite
        Schema::create('favourites', function (Blueprint $table) {
            $table->id('fav_id');
            $table->foreignId('fav_custid');
            $table->foreignId('fav_itemid');
            // $table->integer('fav_qty');
            // $table->decimal('fav_itemprice', 10, 2);
            // $table->decimal('fav_total', 10, 2);
            // $table->decimal('fav_vatprice', 10, 2);
            // $table->decimal('fav_vattotal', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('favourites', function (Blueprint $table) {
            $table->renameColumn('fav_id', 'id');
            $table->renameColumn('fav_custid', 'customer_id');
            $table->renameColumn('fav_itemid', 'product_id');

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
