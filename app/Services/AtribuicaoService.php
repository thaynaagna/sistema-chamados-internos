<?php

namespace App\Services;

use App\Models\Chamado;
use App\Models\User;
use Illuminate\Support\Collection;

class AtribuicaoService
{
    /**
     * Escolhe quem do suporte deve ficar responsável por um chamado novo,
     * com base na carga de trabalho ATUAL de cada pessoa (chamados em
     * 'aberto' ou 'em_andamento', ponderados pela prioridade).
     *
     * Critério: menor carga total ganha o chamado. Em caso de empate,
     * desempata por quem tem menos chamados (em quantidade bruta) e,
     * por fim, por ordem alfabética do nome (resultado determinístico).
     */
    public function escolherResponsavel(): ?User
    {
        $equipeSuporte = User::where('role', 'suporte')->get();

        if ($equipeSuporte->isEmpty()) {
            return null;
        }

        $cargas = $this->cargasPorPessoa($equipeSuporte);

        return $cargas
            ->sortBy([
                ['carga_ponderada', 'asc'],
                ['quantidade', 'asc'],
                ['nome', 'asc'],
            ])
            ->first()['user'];
    }

    /**
     * Atribui automaticamente um responsável ao chamado e retorna o usuário
     * escolhido. Não salva o chamado — quem chama decide quando persistir.
     */
    public function atribuir(Chamado $chamado): ?User
    {
        $responsavel = $this->escolherResponsavel();

        if ($responsavel) {
            $chamado->responsavel_id = $responsavel->id;
        }

        return $responsavel;
    }

    /**
     * Retorna a distribuição atual de carga de trabalho de toda a equipe
     * de suporte. Usado pelo painel /dashboard/distribuicao.
     *
     * @return Collection<int, array{user: User, quantidade: int, carga_ponderada: int, nome: string}>
     */
    public function cargasPorPessoa(?Collection $equipeSuporte = null): Collection
    {
        $equipeSuporte ??= User::where('role', 'suporte')->get();

        return $equipeSuporte->map(function (User $user) {
            $ativos = $user->chamadosResponsavel()
                ->whereIn('status', Chamado::STATUS_ATIVOS)
                ->get();

            $cargaPonderada = $ativos->sum(
                fn (Chamado $c) => $c->pesoPrioridade()
            );

            return [
                'user' => $user,
                'quantidade' => $ativos->count(),
                'carga_ponderada' => $cargaPonderada,
                'nome' => $user->name,
            ];
        })->values();
    }
}
