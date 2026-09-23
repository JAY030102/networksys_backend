<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedUser extends Model
{
    protected $fillable = [
        'original_user_id',
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
        'role',
        'archive_type',
        'reason',
        'actioned_by',
        'actioned_at',
    ];

    protected function casts(): array
    {
        return [
            'actioned_at' => 'datetime',
        ];
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
