<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = [
        'nama',
        'nrp',
        'unit_kerja',
        'vehicle_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'keperluan',
        'status_booking'
    ];
    
    protected $casts = [
        'nrp' => 'integer',
        'vehicle_id' => 'integer',
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    /**
     * Relationship dengan Vehicle
     * Satu booking dimiliki oleh satu kendaraan
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
    //
}
