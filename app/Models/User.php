<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'departamento',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Chamados que este usuário abriu */
    public function chamadosAbertos(): HasMany
    {
        return $this->hasMany(Chamado::class, 'solicitante_id');
    }

    /** Chamados que este usuário (do suporte) é responsável por resolver */
    public function chamadosResponsavel(): HasMany
    {
        return $this->hasMany(Chamado::class, 'responsavel_id');
    }

    public function isSuporte(): bool
    {
        return $this->role === 'suporte';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Suporte e admin podem ver/gerenciar todos os chamados */
    public function podeGerenciarChamados(): bool
    {
        return in_array($this->role, ['suporte', 'admin']);
    }
}
