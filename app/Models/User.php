<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $primaryKey = 'user_id';
    protected $fillable = [
        // 'name', // Removed
        'first_name', // Added
        'last_name', // Added
        'email',
        'password',
        'account_type',
        'fishpond_name', // Added
        'barangay',
        'municipality',
        'province',
        'total_fishpond_area',
        'species_cultured',
        'water_type',
        'api_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'species_cultured' => 'array',
        ];
    }

    public function isAdmin()
    {
        return $this->account_type === 'admin';
    }
}
