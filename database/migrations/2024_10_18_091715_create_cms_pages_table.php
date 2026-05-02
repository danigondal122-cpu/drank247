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
        // Previously it was cmspages
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id('_id');
            $table->string('_page_name');
            $table->text('_page_content_eng');
            $table->text('_page_content_dutch');
            $table->timestamps();
            // $table->softDeletes();
        });

        Schema::table('cms_pages', function (Blueprint $table) {
            $table->renameColumn('_id', 'id');
            $table->renameColumn('_page_name', 'page_name');
            $table->renameColumn('_page_content_eng', 'page_content_eng');
            $table->renameColumn('_page_content_dutch', 'page_content_dutch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
