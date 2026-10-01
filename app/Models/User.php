<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

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
    // helper untuk memeriksa role
    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    // helper untuk 5 role skripsi
    public function isQc(): bool
    {
        return $this->isRole('qc');
    }

    public function isGudang(): bool
    {
        return $this->isRole('gudang');
    }

    public function isKeuangan(): bool
    {
        return $this->isRole('keuangan');
    }

    public function isKasir(): bool
    {
        return $this->isRole('kasir');
    }

    public function isDriver(): bool
    {
        return $this->isRole('driver');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
