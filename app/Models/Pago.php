<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }
}
