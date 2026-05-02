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
        // Previously it was notification
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('nt_id');
            $table->string('nt_usertype');
            $table->unsignedBigInteger('nt_toid');
            $table->text('nt_text');
            $table->boolean('nt_status')->default(0)->comment('0=>unread,1->read');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('nt_id', 'id');
            $table->renameColumn('nt_usertype', 'user_type');
            $table->renameColumn('nt_toid', 'to_id');
            $table->renameColumn('nt_text', 'text');
            $table->renameColumn('nt_status', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
