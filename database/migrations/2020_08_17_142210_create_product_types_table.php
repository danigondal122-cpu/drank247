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
        // Previously it was product_type
        Schema::create('product_types', function (Blueprint $table) {
            $table->id('product_type_id');
            $table->string('product_type');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('product_types', function (Blueprint $table) {
            $table->renameColumn('product_type_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};
