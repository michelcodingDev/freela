<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: React, PHP, Design de Logotipo
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
