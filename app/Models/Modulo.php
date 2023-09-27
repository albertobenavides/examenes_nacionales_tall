<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function pruebas()
    {
        return $this->belongsToMany(Prueba::class)->withPivot('preguntas');
    }
}
