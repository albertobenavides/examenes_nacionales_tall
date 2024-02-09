<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Tema extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    protected $casts =  [
        'contenido' => 'array'
    ];

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

    public function buildSortQuery()
    {
        return static::query()->where('modulo_id', $this->modulo_id);
    }
}
