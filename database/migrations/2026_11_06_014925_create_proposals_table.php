<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('offer', 12, 2); // Valor proposto pelo freelancer
            $table->decimal('final_offer', 12, 2); // Valor com taxa da plataforma
            $table->integer('estimated_days'); // Prazo estimado em dias
            $table->text('details'); // Detalhes da proposta
            $table->enum('status', ['pendente', 'aceita', 'rejeitada', 'retirada'])->default('pendente');
            $table->boolean('is_promoted')->default(false);
            $table->decimal('promoted_fee', 12, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('freelancer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
