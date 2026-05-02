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
        // Previously it was stock_orderdetail
        Schema::create('stock_order_details', function (Blueprint $table) {
            $table->id('_id');
            $table->foreignId('order_id');
            $table->foreignId('product_id')->constrained();
            $table->integer('qty');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stock_order_details', function (Blueprint $table) {
            $table->renameColumn('_id', 'id');
            $table->renameColumn('order_id', 'stock_order_id');

            $table->foreign('stock_order_id')->references('id')->on('stock_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_order_details');
    }
};
