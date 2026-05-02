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
        // Previously it was message
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->enum('message_to', ['deliveryperson', 'customer', 'franchise']);
            $table->text('message_user')->nullable();
            $table->text('message_text');
            $table->tinyInteger('message_status')->comment('0=>no,1=>yes')->default(0);
            $table->text('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('message_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
