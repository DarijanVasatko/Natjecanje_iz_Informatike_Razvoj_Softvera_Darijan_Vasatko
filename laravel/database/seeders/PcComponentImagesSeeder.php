<?php

namespace Database\Seeders;

use App\Models\Proizvod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PcComponentImagesSeeder extends Seeder
{
    /**
     * Update PC component products with image paths.
     * Run this after placing images in the correct folders.
     */
    public function run(): void
    {
        $imageMap = [
            'CPU' => 'uploads/pc-components/cpu/',
            'MB' => 'uploads/pc-components/motherboard/',
            'RAM' => 'uploads/pc-components/ram/',
            'GPU' => 'uploads/pc-components/gpu/',
            'SSD' => 'uploads/pc-components/storage/',
            'PSU' => 'uploads/pc-components/psu/',
            'CASE' => 'uploads/pc-components/case/',
        ];

        $products = Proizvod::where(function ($query) {
            $query->where('sifra', 'LIKE', 'CPU%')
                  ->orWhere('sifra', 'LIKE', 'MB%')
                  ->orWhere('sifra', 'LIKE', 'RAM%')
                  ->orWhere('sifra', 'LIKE', 'GPU%')
                  ->orWhere('sifra', 'LIKE', 'SSD%')
                  ->orWhere('sifra', 'LIKE', 'PSU%')
                  ->orWhere('sifra', 'LIKE', 'CASE%');
        })->get();

        $updated = 0;
        $missing = [];

        foreach ($products as $product) {
            // Determine folder based on sifra prefix
            $prefix = explode('-', $product->sifra)[0];
            $folder = $imageMap[$prefix] ?? null;

            if (!$folder) continue;

            // Generate filename from product name (remove special chars)
            $cleanName = str_replace(['!', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $product->Naziv);
            $filename = Str::slug($cleanName) . '.jpg';
            $fullPath = $folder . $filename;
            $storagePath = storage_path('app/public/' . $fullPath);

            // Check if image exists
            if (file_exists($storagePath)) {
                $product->Slika = $fullPath;
                $product->save();
                $updated++;
                $this->command->info("✓ {$product->Naziv} -> {$fullPath}");
            } else {
                $missing[] = [
                    'naziv' => $product->Naziv,
                    'path' => $fullPath,
                    'expected' => $storagePath
                ];
            }
        }

        $this->command->newLine();
        $this->command->info("Ažurirano: {$updated} proizvoda");

        if (count($missing) > 0) {
            $this->command->warn("Nedostaje " . count($missing) . " slika:");
            foreach ($missing as $m) {
                $this->command->line("  - {$m['naziv']}");
                $this->command->line("    Očekivano: {$m['path']}");
            }
        }
    }
}
