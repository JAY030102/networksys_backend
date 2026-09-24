<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    protected $fillable = ['manufacturer_id', 'name', 'color'];

    public function manufacturer()
    {
        return $this->belongsTo(DeviceManufacturer::class, 'manufacturer_id');
    }
}
