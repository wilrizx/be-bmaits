<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $fillable = [
        'nama',
        'kategori',
    ];
    //

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'unit_kerja_id');
    }
}
