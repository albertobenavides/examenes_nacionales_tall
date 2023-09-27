<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intento extends Model
{
    use HasFactory;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function prueba()
    {
        return $this->belongsTo(Prueba::class, 'prueba_id', 'id');
    }
}
