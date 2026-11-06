<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->text('reason'); // Motivo da disputa
            $table->text('client_argument')->nullable();
            $table->text('freelancer_argument')->nullable();
            $table->enum('status', ['aberta', 'em_analise', 'resolvida'])->default('aberta');
            $table->enum('winner', ['cliente', 'freelancer', 'parcial'])->nullable();
            $table->decimal('partial_percentage', 5, 2)->nullable(); // % para repasse parcial
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
