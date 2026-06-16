<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Cria os usuários necessários para testar o sistema:
     * - 1 admin (gerencia tudo)
     * - 3 pessoas de suporte (atendem os chamados, requisito 3.4 da spec)
     * - 2 solicitantes (funcionários que abrem chamados)
     *
     * Todas as senhas são "password" — apenas para ambiente de teste.
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Admin', 'email' => 'admin@empresa.com', 'role' => 'admin', 'departamento' => 'TI'],
            ['name' => 'Carlos Souza', 'email' => 'carlos@empresa.com', 'role' => 'suporte', 'departamento' => 'TI'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda@empresa.com', 'role' => 'suporte', 'departamento' => 'TI'],
            ['name' => 'Rafael Tavares', 'email' => 'rafael@empresa.com', 'role' => 'suporte', 'departamento' => 'TI'],
            ['name' => 'Juliana Pires', 'email' => 'juliana@empresa.com', 'role' => 'solicitante', 'departamento' => 'Administrativo'],
            ['name' => 'Marcos Andrade', 'email' => 'marcos@empresa.com', 'role' => 'solicitante', 'departamento' => 'Financeiro'],
        ];

        foreach ($usuarios as $dados) {
            User::firstOrCreate(
                ['email' => $dados['email']],
                [
                    'name' => $dados['name'],
                    'role' => $dados['role'],
                    'departamento' => $dados['departamento'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
