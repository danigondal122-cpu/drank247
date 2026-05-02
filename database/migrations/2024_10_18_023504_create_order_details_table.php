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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('od_id');
            $table->foreignId('od_orderid');
            $table->foreignId('od_productid')->nullable();
            $table->integer('od_qty');
            $table->decimal('od_itemprice', 10, 2);
            $table->decimal('od_total', 10, 2);
            $table->decimal('od_vatprice', 10, 2);
            $table->decimal('od_vattotal', 10, 2);
            $table->text('product_details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->renameColumn('od_id', 'id');
            $table->renameColumn('od_orderid', 'order_id');
            $table->renameColumn('od_productid', 'product_id');
            $table->renameColumn('od_itemprice', 'od_item_price');
            $table->renameColumn('od_vatprice', 'od_vat_price');
            $table->renameColumn('od_vattotal', 'od_vat_total');

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
