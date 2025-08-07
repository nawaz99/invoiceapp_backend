<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
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
    ];

    // Add required methods for JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey(); // usually 'id'
    }

    public function getJWTCustomClaims()
    {
        return []; // you can add custom claims if needed
    }

    /**
     * A user can have many clients.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * A user can have many invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function companySetting()
    {
        return $this->hasOne(CompanySetting::class);
    }

    
}
