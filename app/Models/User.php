<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use VerifieClotureComptable;
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $rememberTokenName = null; // Désactive le "remember me", Laravel n'utilisera plus jamais remember_token

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Laravel va deviner que la table membres contient une colonne user_id, c'est magique
    public function membre() : HasOne
    {
        return $this->hasOne(Membre::class);
    }

    public function agent(): HasOneThrough
    {
        return $this->hasOneThrough(
            Agent::class, //Le modèle cible
            Membre::class, // Le modèle intermédiaire
            'user_id', // Clé étrangère sur la table membres
            'membre_id', // Clé étrangère sur la table agents
            'id', // Clé locale sur la table users
            'id'  // Clé locale sur la table membres
        );
    }

    /**
     * Relation avec toutes les photos
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Relation avec la photo de profil
     */
    public function profilePhoto(): HasOne
    {
        return $this->hasOne(Photo::class)->where('is_profile', true);
    }

    public function historiqueRole(): HasMany
    {
        return $this->hasMany(HistoriqueRole::class);
    }

}
