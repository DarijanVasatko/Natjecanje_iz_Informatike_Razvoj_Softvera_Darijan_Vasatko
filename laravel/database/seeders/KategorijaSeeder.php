<?php

namespace Database\Seeders;

use App\Models\Kategorija;
use Illuminate\Database\Seeder;

class KategorijaSeeder extends Seeder
{
    /**
     * Seed the kategorija table with default categories.
     */
    public function run(): void
    {
        $kategorije = [
            'Laptopi',
            'Monitori',
            'Periferija',
            'Komponente',
            'Mrezna oprema',
            'Gaming',
            'Uredska oprema',
        ];

        foreach ($kategorije as $naziv) {
            Kategorija::firstOrCreate(
                ['ImeKategorija' => $naziv]
            );
        }

        $this->command->info('Kategorije seeded successfully!');
    }
}
