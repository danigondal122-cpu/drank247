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
        // Previously it was stock_order
        Schema::create('stock_orders', function (Blueprint $table) {
            $table->id('stock_orderid');
            $table->integer('order_reference')->nullable();
            $table->foreignId('f_id');
            $table->enum('order_status', ['PENDING', 'COMPLETED'])->default('PENDING');
            $table->unsignedTinyInteger('order_to')->comment("'0'=>Stock API,'1'=>Other wholesale company,'2'=>247Drank owe warehouse");
            $table->enum('order_type', ['D', 'P'])->default('P');
            $table->timestamp('pickup_delivery_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stock_orders', function (Blueprint $table) {
            $table->renameColumn('stock_orderid', 'id');
            $table->renameColumn('f_id', 'franchise_id');

            $table->foreign('franchise_id')->references('id')->on('franchises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_orders');
    }
};
