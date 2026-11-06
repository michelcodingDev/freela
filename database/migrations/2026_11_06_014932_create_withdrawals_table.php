<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Valor solicitado
            $table->decimal('fee', 12, 2); // Taxa do saque
            $table->decimal('net_amount', 12, 2); // Valor líquido
            $table->string('pix_key'); // Chave Pix para recebimento
            $table->enum('status', ['solicitado', 'processando', 'pago', 'cancelado'])->default('solicitado');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('freelancer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
