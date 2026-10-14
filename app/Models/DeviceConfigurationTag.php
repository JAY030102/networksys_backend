<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceConfigurationTag extends Model
{
    protected $table = 'device_configuration';

    protected $fillable = ['name', 'color'];
}
