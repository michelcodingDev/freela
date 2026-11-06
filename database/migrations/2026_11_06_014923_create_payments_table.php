<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Valor bruto
            $table->decimal('platform_fee', 12, 2); // Taxa da plataforma (comissão)
            $table->decimal('gateway_fee', 12, 2); // Taxa do gateway (PagSeguro)
            $table->decimal('net_amount', 12, 2); // Valor líquido para o freelancer
            $table->string('method'); // pix, cartao, boleto, paypal
            $table->enum('status', ['pendente', 'custodia', 'liberado', 'estornado'])->default('pendente');
            $table->string('payment_intent_id')->nullable(); // ID da transação no gateway
            $table->json('gateway_response')->nullable(); // Resposta completa do gateway
            $table->timestamp('paid_at')->nullable(); // Quando o cliente pagou
            $table->timestamp('released_at')->nullable(); // Quando foi liberado para o freelancer
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('client_id');
            $table->index('freelancer_id');
            $table->index('status');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
