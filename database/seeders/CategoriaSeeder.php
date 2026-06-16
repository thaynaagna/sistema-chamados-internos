<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Hardware', 'descricao' => 'Computadores, periféricos, equipamentos'],
            ['nome' => 'Software', 'descricao' => 'Sistemas, instalação de programas, erros'],
            ['nome' => 'Rede e Acesso', 'descricao' => 'Internet, VPN, login, permissões'],
            ['nome' => 'Mobiliário e Infraestrutura', 'descricao' => 'Cadeiras, mesas, ar-condicionado, impressoras'],
            ['nome' => 'Outros', 'descricao' => 'Demais solicitações'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(['nome' => $categoria['nome']], $categoria);
        }
    }
}
