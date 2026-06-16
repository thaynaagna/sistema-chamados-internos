<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Services\AtribuicaoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private AtribuicaoService $atribuicaoService)
    {
    }

    /**
     * Painel de distribuição: mostra quantos chamados ativos (e a carga
     * ponderada por prioridade) cada pessoa do suporte está carregando.
     * Visível apenas para suporte/admin.
     */
    public function distribuicao(Request $request): Response
    {
        abort_unless($request->user()->podeGerenciarChamados(), 403);

        $cargas = $this->atribuicaoService->cargasPorPessoa()
            ->map(fn ($c) => [
                'id' => $c['user']->id,
                'nome' => $c['user']->name,
                'quantidade' => $c['quantidade'],
                'carga_ponderada' => $c['carga_ponderada'],
            ])
            ->sortByDesc('carga_ponderada')
            ->values();

        $resumo = [
            'total_abertos' => Chamado::where('status', 'aberto')->count(),
            'total_em_andamento' => Chamado::where('status', 'em_andamento')->count(),
            'total_resolvidos_mes' => Chamado::where('status', 'resolvido')
                ->whereMonth('resolved_at', now()->month)
                ->count(),
        ];

        return Inertia::render('Dashboard/Distribuicao', [
            'cargas' => $cargas,
            'resumo' => $resumo,
        ]);
    }
}
