<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceRunningConfiguration extends Model
{
    protected $fillable = ['device_id', 'file_name', 'content'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
