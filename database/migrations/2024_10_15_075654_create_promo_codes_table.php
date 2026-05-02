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
        // Previously it was promocode
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id('code_id');
            $table->string('code_text');
            $table->tinyInteger('discount_type')->comment('0=>flat amount,1=>in per');
            $table->decimal('discount', 10, 2)->comment('0=>unlimited,1=maximum users');
            $table->tinyInteger('limitation_type');
            $table->integer('max_users')->nullable();
            $table->integer('max_peruser');
            $table->tinyInteger('expiration_type')->comment('0=>no expiry date,1=>with expiry date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('code_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->renameColumn('code_id', 'id');
            $table->renameColumn('max_peruser', 'max_per_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
