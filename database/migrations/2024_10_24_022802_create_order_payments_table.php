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
        // Previously it was tbl_order_payment
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id('order_payment_id');
            $table->foreignId('order_id')->constrained();
            $table->string('identity_entrance_code')->nullable();
            $table->string('identity_transaction_id')->nullable();
            $table->text('identity_transaction_url');
            $table->string('paymentid', 50)->nullable();
            $table->string('identity_transaction_short_url')->nullable();
            $table->string('iban_entrance_code', 40)->nullable();
            $table->string('iban_transaction_id', 40)->nullable();
            $table->text('iban_transaction_url')->nullable();
            $table->string('iban_transaction_short_url')->nullable();
            $table->string('iban_refrence_id')->nullable();
            $table->string('iban_debtorrefrence_id')->nullable();
            $table->integer('payment_status')->default(0);
            $table->string('order_key', 100)->nullable();
            $table->string('status_code', 25)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->timestamps();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->renameColumn('order_payment_id', 'id');
            $table->renameColumn('paymentid', 'payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
