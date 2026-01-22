<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

final class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Central Bahrain',
                'base_fee' => 1.000,
                'free_delivery_threshold' => 20.000,
                'estimated_delivery_time' => '30-45 min',
                'is_active' => true,
                'sort_order' => 1,
                'areas' => [
                    'Manama', 'Juffair', 'Seef', 'Adliya', 'Hoora',
                    'Gudaibiya', 'Sanabis', 'Zinj', 'Salmaniya', 'Tubli',
                ],
            ],
            [
                'name' => 'North Bahrain',
                'base_fee' => 1.500,
                'free_delivery_threshold' => 25.000,
                'estimated_delivery_time' => '45-60 min',
                'is_active' => true,
                'sort_order' => 2,
                'areas' => [
                    'Muharraq', 'Hidd', 'Arad', 'Busaiteen', 'Galali',
                    'Amwaj Islands', 'Diyar Al Muharraq',
                ],
            ],
            [
                'name' => 'South Bahrain',
                'base_fee' => 1.500,
                'free_delivery_threshold' => 25.000,
                'estimated_delivery_time' => '45-60 min',
                'is_active' => true,
                'sort_order' => 3,
                'areas' => [
                    'Riffa', 'Hamad Town', 'Isa Town', 'Sitra',
                    'Alba', 'Awali', 'Zallaq',
                ],
            ],
            [
                'name' => 'West Bahrain',
                'base_fee' => 2.000,
                'free_delivery_threshold' => 30.000,
                'estimated_delivery_time' => '60-90 min',
                'is_active' => true,
                'sort_order' => 4,
                'areas' => [
                    'Budaiya', 'Saar', 'Jasra', 'Barbar',
                    'Janabiya', 'Hamala', 'Karzakan',
                ],
            ],
        ];

        foreach ($zones as $zoneData) {
            $areas = $zoneData['areas'];
            unset($zoneData['areas']);

            $zone = DeliveryZone::create($zoneData);

            foreach ($areas as $areaName) {
                $zone->areas()->create(['area_name' => $areaName]);
            }
        }

        $this->command->info('Delivery zones seeded: 4 zones with ' . collect($zones)->sum(fn($z) => count($z['areas'] ?? [])) . ' areas');
    }
}
