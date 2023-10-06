<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Modulo extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function pruebas()
    {
        return $this->belongsToMany(Prueba::class)->withPivot('preguntas');
    }

    public function temas()
    {
        return $this->hasMany('App\Tema');
    }

    public function buildSortQuery()
    {
        return static::query()->where('curso_id', $this->curso_id);
    }
}
