<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            // 'name' => 'Administrator',
            'email' => 'adminsatu@its.ac.id',
            'password' => Hash::make('admin-bmaits'),
        ]);
    }
}
