<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

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
        'tanggal_peminjaman' => 'date',
        'tanggal_kembali' => 'date',
    ];
    //
}
