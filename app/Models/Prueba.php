<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prueba extends Model
{
    use HasFactory;

    public function modulos()
    {
        return $this->belongsToMany(Modulo::class)->withPivot('preguntas');
    }

    public function temas()
    {
        return $this->belongsToMany(Tema::class)->withPivot('preguntas');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function intentos()
    {
        return $this->hasMany(Intento::class);
    }

}
