<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // Caminho do arquivo
            $table->string('external_url')->nullable(); // URL externa (GitHub, Behance, etc)
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('freelancer_id');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
