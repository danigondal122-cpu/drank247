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
        // Previously it was used_promocode
        Schema::create('used_promo_codes', function (Blueprint $table) {
            $table->id('u_id');
            $table->foreignId('pcode_id');
            $table->foreignId('c_id');
            $table->integer('used_count');
            $table->timestamps();
        });

        Schema::table('used_promo_codes', function (Blueprint $table) {
            $table->renameColumn('u_id', 'id');
            $table->renameColumn('pcode_id', 'promo_code_id');
            $table->renameColumn('c_id', 'customer_id');

            $table->foreign('promo_code_id')->references('id')->on('promo_codes');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_promo_codes');
    }
};
