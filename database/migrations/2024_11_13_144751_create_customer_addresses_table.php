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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('address_default');
            $table->foreignId('address_custid');
            $table->text('address_address');
            $table->string('address_postcode');
            $table->string('address_latitude');
            $table->string('address_longitude');
            $table->integer('address_manual')->default(0);
            $table->string('address_houseno');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->renameColumn('address_id', 'id');
            $table->renameColumn('address_default', 'default');
            $table->renameColumn('address_custid', 'customer_id');
            $table->renameColumn('address_address', 'address');
            $table->renameColumn('address_postcode', 'post_code');
            $table->renameColumn('address_latitude', 'latitude');
            $table->renameColumn('address_longitude', 'longitude');
            $table->renameColumn('address_manual', 'manual');
            $table->renameColumn('address_houseno', 'house_no');

            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('order_address_id')->references('id')->on('customer_addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
