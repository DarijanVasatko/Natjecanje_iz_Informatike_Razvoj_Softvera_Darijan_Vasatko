<?php

namespace Database\Seeders;

use App\Models\PcComponentType;
use Illuminate\Database\Seeder;

class PcComponentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'naziv' => 'Procesor',
                'slug' => 'cpu',
                'redoslijed' => 1,
                'ikona' => 'bi-cpu',
                'obavezan' => true,
            ],
            [
                'naziv' => 'Matična ploča',
                'slug' => 'maticna-ploca',
                'redoslijed' => 2,
                'ikona' => 'bi-motherboard',
                'obavezan' => true,
            ],
            [
                'naziv' => 'RAM memorija',
                'slug' => 'ram',
                'redoslijed' => 3,
                'ikona' => 'bi-memory',
                'obavezan' => true,
            ],
            [
                'naziv' => 'Grafička kartica',
                'slug' => 'gpu',
                'redoslijed' => 4,
                'ikona' => 'bi-gpu-card',
                'obavezan' => false,
            ],
            [
                'naziv' => 'SSD/HDD',
                'slug' => 'storage',
                'redoslijed' => 5,
                'ikona' => 'bi-device-hdd',
                'obavezan' => true,
            ],
            [
                'naziv' => 'Napajanje',
                'slug' => 'napajanje',
                'redoslijed' => 6,
                'ikona' => 'bi-lightning',
                'obavezan' => true,
            ],
            [
                'naziv' => 'Kućište',
                'slug' => 'kuciste',
                'redoslijed' => 7,
                'ikona' => 'bi-pc-display',
                'obavezan' => true,
            ],
        ];

        foreach ($types as $type) {
            PcComponentType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
