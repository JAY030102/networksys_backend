<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceManufacturer extends Model
{
    protected $fillable = ['name'];

    public function models()
    {
        return $this->hasMany(DeviceModel::class, 'manufacturer_id');
    }
}
