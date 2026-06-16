<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chamado extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'categoria_id',
        'solicitante_id',
        'responsavel_id',
        'status',
        'prioridade',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Peso usado pelo AtribuicaoService para calcular a carga de trabalho
     * de cada pessoa do suporte. Prioridades maiores pesam mais.
     */
    public const PESO_PRIORIDADE = [
        'baixa' => 1,
        'media' => 2,
        'alta' => 3,
    ];

    public const STATUS_ATIVOS = ['aberto', 'em_andamento'];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(ChamadoHistorico::class)->latest();
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->whereIn('status', self::STATUS_ATIVOS);
    }

    public function scopeDoUsuario(Builder $query, int $userId): Builder
    {
        return $query->where('solicitante_id', $userId);
    }

    public function pesoPrioridade(): int
    {
        return self::PESO_PRIORIDADE[$this->prioridade] ?? 1;
    }
}
