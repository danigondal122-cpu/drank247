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
        // Previously it was franchise_stock_order
        Schema::create('franchise_stock_orders', function (Blueprint $table) {
            $table->id('fs_id');
            $table->foreignId('fs_order_id');
            $table->foreignId('fs_wh_id');
            $table->foreignId('fs_fr_id');
            $table->foreignId('fs_product_id');
            $table->integer('fs_qty');
            $table->enum('order_status', ['PENDING', 'COMPLETED', 'DISPACHED']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('franchise_stock_orders', function (Blueprint $table) {
            $table->renameColumn('fs_id', 'id');
            $table->renameColumn('fs_order_id', 'order_id');
            $table->renameColumn('fs_wh_id', 'warehouse_id');
            $table->renameColumn('fs_fr_id', 'franchise_id');
            $table->renameColumn('fs_product_id', 'product_id');

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('franchise_id')->references('id')->on('franchises');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_stock_orders');
    }
};
