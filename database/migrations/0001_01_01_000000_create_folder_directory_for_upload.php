<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        File::ensureDirectoryExists(public_path('uploads/category'));
        File::ensureDirectoryExists(public_path('uploads/category/thumb'));
        File::ensureDirectoryExists(public_path('uploads/warehouse'));
        File::ensureDirectoryExists(public_path('uploads/warehouse/thumb'));
        File::ensureDirectoryExists(public_path('uploads/banner'));
        File::ensureDirectoryExists(public_path('uploads/banner/thumb'));
        File::ensureDirectoryExists(public_path('uploads/customerserviceprofile/'));
        File::ensureDirectoryExists(public_path('uploads/customerserviceprofile/thumb'));
        File::ensureDirectoryExists(public_path('uploads/product'));
        File::ensureDirectoryExists(public_path('uploads/product/thumb'));
        File::ensureDirectoryExists(public_path('uploads/adminprofile'));
        File::ensureDirectoryExists(public_path('uploads/adminprofile/thumb'));
    }
};
