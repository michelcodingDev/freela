<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Pro, Premium
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price_monthly', 12, 2); // Preço mensal da assinatura
            $table->decimal('commission_percentage', 5, 2); // % de comissão da plataforma
            $table->decimal('min_commission', 12, 2)->default(0.00); // Comissão mínima
            $table->integer('proposals_per_month'); // Limite de propostas por mês
            $table->integer('max_portfolio_items'); // Limite de itens no portfólio
            $table->integer('withdrawal_days'); // Dias úteis para saque
            $table->boolean('is_featured')->default(false); // Plano destacado
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
