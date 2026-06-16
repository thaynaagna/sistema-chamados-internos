<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamados', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');

            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
                ->nullOnDelete();

            $table->foreignId('solicitante_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // nullable: pode não haver ninguém no suporte ainda quando o chamado é aberto
            $table->foreignId('responsavel_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['aberto', 'em_andamento', 'resolvido', 'fechado'])
                ->default('aberto');

            $table->enum('prioridade', ['baixa', 'media', 'alta'])
                ->default('media');

            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'responsavel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamados');
    }
};
