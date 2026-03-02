<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    use HasFactory;

    // Nome da tabela (opcional se seguir o padrão plural)
    protected $table = 'mensagens';

    protected $fillable = [
        'user_match_id',
        'sender_id',
        'conteudo',
        'lida'
    ];

    /**
     * Relacionamento: A mensagem pertence a um Match
     */
    public function match()
    {
        return $this->belongsTo(UserMatch::class, 'user_match_id');
    }

    /**
     * Relacionamento: A mensagem foi enviada por um Usuário
     */
    public function autor()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}