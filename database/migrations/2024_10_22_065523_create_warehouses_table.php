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
        // Previously it was warehouse
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id('wh_id');
            $table->string('wh_name');
            $table->string('wh_logo');
            $table->string('wh_minprice');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->renameColumn('wh_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
