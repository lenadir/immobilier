<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $agents = User::where('role', User::ROLE_AGENT)->get();

        if ($agents->isEmpty()) {
            $this->command->warn('Aucun agent trouvé. Lancez UserSeeder en premier.');
            return;
        }

        $properties = [
            [
                'type'         => 'villa',
                'rooms'        => 5,
                'surface'      => 320.00,
                'price'        => 45_000_000,
                'city'         => 'Alger',
                'address'      => 'Bab Ezzouar, Alger',
                'description'  => 'Magnifique villa avec jardin et piscine, quartier résidentiel calme.',
                'status'       => 'disponible',
                'is_published' => true,
            ],
            [
                'type'         => 'appartement',
                'rooms'        => 3,
                'surface'      => 90.00,
                'price'        => 12_500_000,
                'city'         => 'Oran',
                'address'      => 'Hai Fellaoucene, Oran',
                'description'  => 'F3 bien exposé, vue sur mer, proche de toutes commodités.',
                'status'       => 'disponible',
                'is_published' => true,
            ],
            [
                'type'         => 'terrain',
                'rooms'        => null,
                'surface'      => 500.00,
                'price'        => 8_000_000,
                'city'         => 'Constantine',
                'address'      => 'Zone industrielle, Constantine',
                'description'  => 'Terrain plat viabilisé, idéal pour construction.',
                'status'       => 'disponible',
                'is_published' => true,
            ],
            [
                'type'         => 'studio',
                'rooms'        => 1,
                'surface'      => 35.00,
                'price'        => 25_000,
                'city'         => 'Annaba',
                'address'      => 'Centre-ville, Annaba',
                'description'  => 'Studio meublé en excellent état, idéal pour étudiant.',
                'status'       => 'location',
                'is_published' => true,
            ],
            [
                'type'         => 'bureau',
                'rooms'        => null,
                'surface'      => 150.00,
                'price'        => 80_000,
                'city'         => 'Alger',
                'address'      => 'Hydra, Alger',
                'description'  => 'Plateau de bureau moderne, climatisé, parking inclus.',
                'status'       => 'location',
                'is_published' => false,  // Non publié (brouillon)
            ],
        ];

        // Colour palette for generated placeholder images
        $colours = [
            [59,  130, 246],  // blue
            [16,  185, 129],  // green
            [245, 158,  11],  // amber
            [239,  68,  68],  // red
            [139,  92, 246],  // violet
        ];

        foreach ($properties as $index => $data) {
            $agent = $agents[$index % $agents->count()];

            $property = Property::firstOrCreate(
                ['address' => $data['address'], 'city' => $data['city']],
                array_merge($data, ['user_id' => $agent->id])
            );

            // Skip image generation if this property already has images
            if ($property->images()->exists()) {
                continue;
            }

            // Generate 3 placeholder images per property using GD
            for ($i = 1; $i <= 3; $i++) {
                [$r, $g, $b] = $colours[($index + $i) % count($colours)];

                $img = imagecreatetruecolor(800, 600);
                $bg  = imagecolorallocate($img, $r, $g, $b);
                $fg  = imagecolorallocate($img, 255, 255, 255);
                imagefilledrectangle($img, 0, 0, 800, 600, $bg);

                // Gradient stripe
                $dark = imagecolorallocatealpha($img, 0, 0, 0, 60);
                imagefilledrectangle($img, 0, 480, 800, 600, $dark);

                $label = strtoupper($data['type']) . '  –  ' . $data['city'];
                imagestring($img, 5, 30, 510, $label, $fg);
                imagestring($img, 3, 30, 540, 'Photo ' . $i . ' / 3', $fg);

                ob_start();
                imagejpeg($img, null, 85);
                $jpeg = ob_get_clean();
                imagedestroy($img);

                $path = 'properties/seed_' . $property->id . '_' . $i . '.jpg';
                Storage::disk('public')->put($path, $jpeg);

                Image::create([
                    'property_id'   => $property->id,
                    'path'          => $path,
                    'disk'          => 'public',
                    'original_name' => 'placeholder_' . $i . '.jpg',
                    'size'          => strlen($jpeg),
                    'mime_type'     => 'image/jpeg',
                    'is_cover'      => $i === 1,
                    'sort_order'    => $i,
                ]);
            }
        }

        $this->command->info('✓ Biens immobiliers créés avec succès.');
    }
}
