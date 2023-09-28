<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Intento extends Model
{
    use HasFactory;

    protected $casts = [ 'preguntas' => 'array' ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function prueba()
    {
        return $this->belongsTo(Prueba::class, 'prueba_id', 'id');
    }

    public function preguntasIncluidas()
    {
        return $this->hasMany(Intento::class, 'id')->join('preguntas', 'preguntas.id', '=', 'preguntas.id') ->whereIn('preguntas.id', $this->preguntas);
    }
}
