<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicle';

    protected $fillable = [
        'nama_kendaraan',
        'jenis_kendaraan',
        'nomor_polisi',
        'warna_kendaraan',
        'bahan_bakar',
        'kapasitas_penumpang',
        'status_ketersediaan',
    ];

    protected $casts = [
        'kapasitas_penumpang' => 'integer',
    ];

    /**
     * Relationship dengan Booking
     * Satu kendaraan bisa memiliki banyak booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'vehicle_id');
    }

    /**
     * Scope untuk filter kendaraan yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status_ketersediaan', 'tersedia');
    }

    /**
     * Scope untuk filter kendaraan yang sedang dipinjam
     */
    public function scopeBorrowed($query)
    {
        return $query->where('status_ketersediaan', 'dipinjam');
    }

    /**
     * Scope untuk filter kendaraan yang maintenance
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status_ketersediaan', 'maintenance');
    }
    //
}
