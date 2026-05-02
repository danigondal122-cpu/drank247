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
        // Previously it was message_user
        Schema::create('message_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_id');
            $table->text('m_user');
            $table->foreignId('m_userid');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('message_users', function (Blueprint $table) {
            $table->renameColumn('m_id', 'message_id');
            $table->renameColumn('m_userid', 'm_user_id');

            $table->foreign('message_id')->references('id')->on('messages');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_users');
    }
};
