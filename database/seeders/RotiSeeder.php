<?php

namespace Database\Seeders;

use App\Models\Roti;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RotiSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $rotis = [
            [
                'nama' => 'Roti Tawar',
                'harga' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Roti Manis',
                'harga' => 3000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Roti Coklat',
                'harga' => 7000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Roti Keju',
                'harga' => 8000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Roti Pisang',
                'harga' => 6000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rotis as $roti) {
            Roti::create($roti);
        }
    }
}
