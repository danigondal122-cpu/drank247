<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id('stock_id');
            $table->foreignId('stock_product');
            $table->foreignId('stock_franchisee');
            $table->unsignedInteger('stock_current')->nullable();
            $table->unsignedInteger('stock_minimum')->nullable();
            $table->integer('max_stock_order')->nullable();
            $table->boolean('is_reminder_set')->default(0)->comment('0=>unset,1=>set');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->renameColumn('stock_id', 'id');
            $table->renameColumn('stock_product', 'product_id');
            $table->renameColumn('stock_franchisee', 'franchise_id');

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('franchise_id')->references('id')->on('franchises');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
