<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating'); // 1 a 5 estrelas
            $table->text('comment')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
