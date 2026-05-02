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
        // Previously it was invoicepdf_details
        Schema::create('invoice_pdfs', function (Blueprint $table) {
            $table->id();
            $table->string('orderId');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('amount', 50);
            $table->string('paid_amount', 50);
            $table->foreignId('f_id');
            $table->string('pdf_name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('invoice_pdfs', function (Blueprint $table) {
            $table->renameColumn('orderId', 'order_id');
            $table->renameColumn('f_id', 'franchise_id');

            $table->foreign('franchise_id')->references('id')->on('franchises');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_pdfs');
    }
};
