<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'solicitante' = qualquer funcionário que abre chamados
            // 'suporte'     = entra no pool de atribuição automática
            // 'admin'       = gerencia tudo
            $table->enum('role', ['solicitante', 'suporte', 'admin'])
                ->default('solicitante')
                ->after('email');

            $table->string('departamento')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'departamento']);
        });
    }
};
