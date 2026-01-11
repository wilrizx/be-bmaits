<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'nama_kendaraan' => 'BUS MANDIRI',
                'jenis_kendaraan' => 'Bus',
                'nomor_polisi' => 'L 7808 AE',
                'warna_kendaraan' => 'Putih Biru',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 35,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'BUS BNI',
                'jenis_kendaraan' => 'Bus',
                'nomor_polisi' => 'L 7684 AP',
                'warna_kendaraan' => 'Putih Oren',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 28,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'BUS SPS',
                'jenis_kendaraan' => 'Bus',
                'nomor_polisi' => 'L 7151 AH',
                'warna_kendaraan' => 'Putih Biru',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 28,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'BUS IKOMA',
                'jenis_kendaraan' => 'Bus',
                'nomor_polisi' => 'L 7608 AP',
                'warna_kendaraan' => 'Putih Biru',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 27,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'HAICE',
                'jenis_kendaraan' => 'Microbus',
                'nomor_polisi' => 'L 7010 N',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 14,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'HYUNDAI',
                'jenis_kendaraan' => 'Kendaraan Dinas',
                'nomor_polisi' => 'L 1843 OD',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'SEDAN VIOS XSK ITS',
                'jenis_kendaraan' => 'Sedan',
                'nomor_polisi' => 'L 1069 OE',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 3,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'SEDAN ALTIS XWR4',
                'jenis_kendaraan' => 'Sedan',
                'nomor_polisi' => 'L 1081 OE',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 3,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'SEDAN ALTIS XWR3',
                'jenis_kendaraan' => 'Sedan',
                'nomor_polisi' => 'L 1080 OE',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 3,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'INNOVA XDPP',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1511 EP',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Dexlite',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'INNOVA XFTSPK',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1852 AP',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'INNOVA X ELEKTRO',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1502 BP',
                'warna_kendaraan' => 'Abu-abu',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'INNOVA XDRPM',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1059 AP',
                'warna_kendaraan' => 'Hijau',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'AVANZA XDKG',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1031 CP',
                'warna_kendaraan' => 'Hitam',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'AVANZA X INFORMATIKA',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 6001 DP',
                'warna_kendaraan' => 'Putih',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'AVANZA XBURB',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1393 DL',
                'warna_kendaraan' => 'Silver',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'AVANZA XBK',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1068 OD',
                'warna_kendaraan' => 'Silver',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
            [
                'nama_kendaraan' => 'AVANZA XBSP',
                'jenis_kendaraan' => 'MPV',
                'nomor_polisi' => 'L 1171 OD',
                'warna_kendaraan' => 'Silver',
                'bahan_bakar' => 'Pertamax',
                'kapasitas_penumpang' => 5,
                'status_ketersediaan' => 'tersedia',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
        //
    }
}
