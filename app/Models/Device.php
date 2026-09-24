<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_name',
        'category',
        'status',
        'ip_address',
        'mac_address',
        'vlan',
        'manufacturer',
        'model',
        'serial_number',
        'location',
        'rack',
        'port',
        'firmware',
        'assigned_to',
        'purchase_date',
        'warranty_expiry',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
