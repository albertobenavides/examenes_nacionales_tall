<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    use HasFactory;

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }

    public function pruebas()
    {
        return $this->belongsToMany(Prueba::class)->withPivot('preguntas');
    }
}
