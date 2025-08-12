<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'password',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatar()
    {
        if (!$this->avatar) {
            return asset('storage/avatar/default.png');
        }
        return asset('storage/avatar/' . auth()->user()->username . '/' . $this->avatar);
    }

    // Relasi dengan cheesecake sebagai baker
    public function cheesecakes()
    {
        return $this->hasMany(Cheesecake::class, 'baker_id');
    }

    // Relasi dengan transaksi sebagai kasir
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'kasir_id');
    }
}
