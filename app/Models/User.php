<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // remove if not using Sanctum

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

   protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'username',
    'name',
    'avatar',
    'email',
    'mobile_number',
    'birthdate',
    'gender',
    'address',
    'password',
    'role',
    'status',
    'account_status',
    'suspended_at',
    'suspended_by',
    'suspension_reason',
    'approved_at',
    'approved_by',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'terminated_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function terminatedBy()
    {
        return $this->belongsTo(User::class, 'terminated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (! $this->avatar) {
                    return null;
                }

                return rtrim(config('app.url'), '/').'/storage/'.$this->avatar;
            },
        );
    }
}
