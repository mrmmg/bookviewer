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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('title', 500);

            $table->string('checksum', 32)
                ->index()
                ->comment('md5');

            $table->string('store_path');

            $table->boolean('is_public')
                ->index();

            $table->unsignedBigInteger('created_by')
                ->comment('uploader user_id')
                ->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
