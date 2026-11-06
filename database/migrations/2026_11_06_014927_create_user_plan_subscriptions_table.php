<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['ativo', 'inativo', 'cancelado', 'expirado'])->default('ativo');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('price_paid', 12, 2);
            $table->string('payment_method')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('plan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_plan_subscriptions');
    }
};
