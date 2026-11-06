<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // criar, editar, excluir, aprovar, rejeitar, etc
            $table->string('model_type'); // Nome do modelo (Project, Payment, User, etc)
            $table->unsignedBigInteger('model_id'); // ID do registro
            $table->json('old_values')->nullable(); // Valores antes da alteração
            $table->json('new_values')->nullable(); // Valores após a alteração
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
