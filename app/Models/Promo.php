<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    use HasFactory;

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Get the curso that owns the Promo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }
}
