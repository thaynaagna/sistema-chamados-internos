<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChamadoHistorico extends Model
{
    use HasFactory;

    protected $fillable = [
        'chamado_id',
        'user_id',
        'acao',
        'status_anterior',
        'status_novo',
        'comentario',
    ];

    public function chamado(): BelongsTo
    {
        return $this->belongsTo(Chamado::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
