<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Chamado;
use App\Models\ChamadoHistorico;
use App\Models\User;
use App\Services\AtribuicaoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChamadoController extends Controller
{
    public function __construct(private AtribuicaoService $atribuicaoService)
    {
    }

    /**
     * Lista de chamados.
     * - Solicitante: vê apenas os próprios chamados.
     * - Suporte/Admin: vê todos, com filtros.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = Chamado::query()
            ->with(['categoria', 'solicitante', 'responsavel'])
            ->latest();

        if (! $user->podeGerenciarChamados()) {
            $query->doUsuario($user->id);
        } else {
            if ($request->filled('status')) {
                $query->where('status', $request->string('status'));
            }
            if ($request->filled('prioridade')) {
                $query->where('prioridade', $request->string('prioridade'));
            }
            if ($request->filled('responsavel_id')) {
                $query->where('responsavel_id', $request->integer('responsavel_id'));
            }
            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->integer('categoria_id'));
            }
        }

        return Inertia::render('Chamados/Index', [
            'chamados' => $query->paginate(15)->withQueryString(),
            'filtros' => $request->only(['status', 'prioridade', 'responsavel_id', 'categoria_id']),
            'categorias' => Categoria::orderBy('nome')->get(['id', 'nome']),
            'equipeSuporte' => $user->podeGerenciarChamados()
                ? User::where('role', 'suporte')->orderBy('name')->get(['id', 'name'])
                : [],
            'podeGerenciar' => $user->podeGerenciarChamados(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Chamados/Create', [
            'categorias' => Categoria::orderBy('nome')->get(['id', 'nome']),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descricao' => ['required', 'string'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'prioridade' => ['required', 'in:baixa,media,alta'],
        ]);

        $chamado = new Chamado($dados);
        $chamado->solicitante_id = $request->user()->id;
        $chamado->status = 'aberto';

        // Atribuição automática balanceada por carga de trabalho.
        $responsavel = $this->atribuicaoService->atribuir($chamado);

        $chamado->save();

        ChamadoHistorico::create([
            'chamado_id' => $chamado->id,
            'user_id' => $request->user()->id,
            'acao' => 'abertura',
            'status_novo' => 'aberto',
            'comentario' => $responsavel
                ? "Chamado aberto e atribuído automaticamente a {$responsavel->name}."
                : 'Chamado aberto. Nenhum responsável disponível no momento.',
        ]);

        return redirect()
            ->route('chamados.show', $chamado)
            ->with('success', 'Chamado aberto com sucesso!');
    }

    public function show(Chamado $chamado, Request $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user->podeGerenciarChamados() || $chamado->solicitante_id === $user->id,
            403
        );

        $chamado->load(['categoria', 'solicitante', 'responsavel', 'historico.user']);

        return Inertia::render('Chamados/Show', [
            'chamado' => $chamado,
            'podeGerenciar' => $user->podeGerenciarChamados(),
            'equipeSuporte' => $user->podeGerenciarChamados()
                ? User::where('role', 'suporte')->orderBy('name')->get(['id', 'name'])
                : [],
        ]);
    }

    /**
     * Atualiza o status do chamado (fluxo: aberto -> em_andamento -> resolvido -> fechado).
     */
    public function atualizarStatus(Request $request, Chamado $chamado)
    {
        abort_unless($request->user()->podeGerenciarChamados(), 403);

        $dados = $request->validate([
            'status' => ['required', 'in:aberto,em_andamento,resolvido,fechado'],
            'comentario' => ['nullable', 'string'],
        ]);

        $statusAnterior = $chamado->status;
        $chamado->status = $dados['status'];

        if ($dados['status'] === 'resolvido' && ! $chamado->resolved_at) {
            $chamado->resolved_at = now();
        }

        $chamado->save();

        ChamadoHistorico::create([
            'chamado_id' => $chamado->id,
            'user_id' => $request->user()->id,
            'acao' => 'mudanca_status',
            'status_anterior' => $statusAnterior,
            'status_novo' => $dados['status'],
            'comentario' => $dados['comentario'] ?? null,
        ]);

        return back()->with('success', 'Status atualizado.');
    }

    /**
     * Reatribuição manual — usada quando o suporte/admin precisa
     * corrigir a distribuição automática.
     */
    public function reatribuir(Request $request, Chamado $chamado)
    {
        abort_unless($request->user()->podeGerenciarChamados(), 403);

        $dados = $request->validate([
            'responsavel_id' => ['required', 'exists:users,id'],
            'comentario' => ['nullable', 'string'],
        ]);

        $novoResponsavel = User::findOrFail($dados['responsavel_id']);
        abort_unless($novoResponsavel->isSuporte(), 422, 'Usuário escolhido não faz parte da equipe de suporte.');

        $responsavelAnterior = $chamado->responsavel?->name ?? 'ninguém';
        $chamado->responsavel_id = $novoResponsavel->id;
        $chamado->save();

        ChamadoHistorico::create([
            'chamado_id' => $chamado->id,
            'user_id' => $request->user()->id,
            'acao' => 'reatribuicao',
            'comentario' => $dados['comentario']
                ?? "Reatribuído de {$responsavelAnterior} para {$novoResponsavel->name}.",
        ]);

        return back()->with('success', 'Chamado reatribuído.');
    }
}
