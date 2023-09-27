<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'examen_id');
    }
}
