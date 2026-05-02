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
        // Previously it was admin
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('reset_token')->nullable();
            $table->enum('admin_type', ['admin', 'superadmin']);
            $table->boolean('is_accountant')->default(false)->comment('0=no,1=yes');
            $table->string('admin_mobileno')->nullable();
            $table->string('admin_phone')->nullable();
            $table->string('admin_street')->nullable();
            $table->string('admin_city')->nullable();
            $table->string('admin_state')->nullable();
            $table->string('admin_postcode')->nullable();
            $table->string('admin_company')->nullable();
            $table->string('admin_vat')->nullable();
            $table->string('admin_commerce_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->renameColumn('admin_id', 'id');
            $table->renameColumn('admin_mobileno', 'admin_mobile_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
