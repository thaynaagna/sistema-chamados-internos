<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamado_historicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chamado_id')
                ->constrained('chamados')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 'abertura' | 'mudanca_status' | 'reatribuicao' | 'comentario'
            $table->string('acao');

            $table->string('status_anterior')->nullable();
            $table->string('status_novo')->nullable();
            $table->text('comentario')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamado_historicos');
    }
};
