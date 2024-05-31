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
        Schema::create('hashcache', function (Blueprint $table) {
            $table->id();
            $table->string('long_hash',40);
            $table->string('short_hash',40);
            $table->integer('size');
            $table->string('file_path')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashcache');
    }
};
