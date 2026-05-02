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
        // Previously it was assign_module
        Schema::create('admin_module', function (Blueprint $table) {
            // $table->id('assign_id');
            $table->foreignId('admin_id')->constrained();
            $table->foreignId('module_id')->constrained();
            // $table->timestamp('created_at')->useCurrent();

            $table->unique(['admin_id', 'module_id']);
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_module');
    }
};
