<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('experience_level'); // iniciante, intermediario, especialista
            $table->enum('visibility', ['publico', 'privado'])->default('publico');
            $table->enum('status', [
                'aguardando_aprovacao',
                'aguardando_propostas',
                'aguardando_pagamento',
                'em_andamento',
                'aguardando_liberacao',
                'concluido',
                'cancelado',
                'disputa',
                'fechado',
                'reprovado',
            ])->default('aguardando_aprovacao');
            $table->date('proposal_deadline'); // Prazo para receber propostas
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->boolean('is_featured')->default(false); // Turbinado
            $table->decimal('featured_fee', 12, 2)->default(0.00); // Taxa do destaque
            $table->text('attachment_path')->nullable(); // Anexo do projeto
            $table->text('admin_notes')->nullable(); // Notas da moderação
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('category_id');
            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
