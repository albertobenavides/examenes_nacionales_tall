<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'examen_id');
    }

    public function modulos()
    {
        return $this->hasMany(Modulo::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function pruebas()
    {
        return $this->hasMany(Prueba::class);
    }
}
