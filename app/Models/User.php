<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notes' => 'array'
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url == '' ? $this->avatar_url : '/storage/' . $this->avatar_url;
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function intentos()
    {
        return $this->hasMany(Intento::class);
    }

    /**
     * The modulos that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modulos(): BelongsToMany
    {
        return $this->belongsToMany(Modulo::class, 'modulo_user')->withPivot('tema_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin'){
            return $this->hasRole(['super-admin', 'consulta']);
        } else {
            return true;
        }
    }

    public function canImpersonate()
    {
        return $this->hasRole(['super_admin']);
    }
}
