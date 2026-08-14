<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'company_status',
        'company_reject_reason',
        'company_reject_at',
        'company_accept_at',
        'company_accept_by',
        'company_reject_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'company_reject_at' => 'datetime',
            'company_accept_at' => 'datetime',
        ];
    }

    public function getIsAdminAttribute()
    {
        return strtolower($this->role) === 'admin';
    }

    public function getIsUserAttribute()
    {
        return strtolower($this->role) === 'user';
    }

    public function getIsDosenAttribute()
    {
        return strtolower($this->role) === 'dosen';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
