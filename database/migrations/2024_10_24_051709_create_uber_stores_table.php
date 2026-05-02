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
        Schema::create('uber_stores', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('store_id');
            $table->text('location');
            $table->text('status');
            $table->text('contact_emails');
            $table->longText('store_menu')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uber_stores');
    }
};
