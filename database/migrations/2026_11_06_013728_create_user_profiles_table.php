<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('professional_title')->nullable(); // Ex: Desenvolvedor Web Sênior
            $table->string('pix_key')->nullable(); // Chave Pix para recebimento
            $table->string('bank_name')->nullable();
            $table->string('bank_account_type')->nullable(); // corrente, poupança
            $table->string('bank_agency')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_account_digit')->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->string('document_photo')->nullable(); // Foto do documento para verificação
            $table->boolean('identity_verified')->default(false); // Selo Identidade Verificada
            $table->decimal('rating_average', 3, 2)->default(0.00); // Avaliação média 1-5
            $table->integer('total_reviews')->default(0); // Total de avaliações
            $table->decimal('total_earned', 12, 2)->default(0.00); // Total ganho na plataforma
            $table->integer('projects_completed')->default(0); // Projetos concluídos
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
