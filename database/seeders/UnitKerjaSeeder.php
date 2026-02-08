<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direktorat = [
            "Direktorat Pendidikan Sarjana dan Pascasarjana",
            "Direktorat Kemahasiswaan",
            "Direktorat Pengembangan Akademik dan Inovasi Pembelajaran",
            "Direktorat Perencanaan dan Pengembangan Strategis",
            "Direktorat Sumber Daya Manusia dan Organisasi",
            "Direktorat Teknologi dan Pengembangan Sistem Informasi",
            "Direktorat Riset dan Pengabdian kepada Masyarakat",
            "Direktorat Inovasi dan Science Techno Park",
            "Direktorat Kerja Sama dan Pengelolaan Usaha",
            "Direktorat Kemitraan Global",
            "Biro Manajemen Aset",
            "Biro Keuangan",
        ];

        foreach ($direktorat as $nama) {
            UnitKerja::create([
                'nama' => $nama,
                'kategori' => 'Direktorat'
            ]);
        }

        $fakultas = [
            "Fakultas Sains dan Analitika Data",
            "Fakultas Teknologi Industri dan Sistem Rekayasa",
            "Fakultas Teknik Sipil, Perencanaan, dan Kebumian",
            "Fakultas Teknologi Kelautan",
            "Fakultas Teknologi Elektro dan Informatika Cerdas",
            "Fakultas Desain Kreatif dan Bisnis Digital",
            "Fakultas Vokasi",
            "Fakultas Kedokteran dan Kesehatan",
        ];

        foreach ($fakultas as $nama) {
            UnitKerja::create([
                'nama' => $nama,
                'kategori' => 'Fakultas'
            ]);
        }
        //
    }
}
