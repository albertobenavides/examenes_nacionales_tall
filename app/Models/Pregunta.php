<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pregunta extends Model
{
    use HasFactory;

    public function tema()
    {
        return $this->belongsTo(Tema::class);
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }

    /**
     * Get the curso that owns the Pregunta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }
}
