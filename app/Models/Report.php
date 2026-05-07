<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_id',
        'reason',
        'description',
        'status',
        'analyzed_by',
        'analyzed_at',
        'analysis_notes',
    ];

    protected $casts = [
        'analyzed_at' => 'datetime',
    ];

    /**
     * O usuário que fez a denúncia
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * O usuário que foi denunciado
     */
    public function reported()
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    /**
     * O administrador que analisou a denúncia
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }
}
