<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $primaryKey = 'UserID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Username',
        'Password',
        'NamaLengkap',
        'Email',
        'Role',
        'EmailVerifiedAt',
        'EmailVerificationToken',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'Password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'TanggalDaftar' => 'datetime',
            'EmailVerifiedAt' => 'datetime',
        ];
    }

    // Role helpers
    public function isOwner(): bool
    {
        return $this->Role === 'Owner';
    }

    public function isAdmin(): bool
    {
        return $this->Role === 'Admin';
    }

    public function isProduksi(): bool
    {
        return $this->Role === 'Produksi';
    }

    public function isKeuangan(): bool
    {
        return $this->Role === 'Keuangan';
    }
}
