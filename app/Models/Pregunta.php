<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
