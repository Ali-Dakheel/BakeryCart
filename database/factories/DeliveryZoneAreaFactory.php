<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DeliveryZone;
use App\Models\DeliveryZoneArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DeliveryZoneArea> */
final class DeliveryZoneAreaFactory extends Factory
{
    protected $model = DeliveryZoneArea::class;

    public function definition(): array
    {
        return [
            'delivery_zone_id' => DeliveryZone::factory(),
            'area_name' => fake()->randomElement([
                'Manama', 'Muharraq', 'Riffa', 'Hamad Town',
                'Isa Town', 'Saar', 'Budaiya', 'Juffair',
                'Seef', 'Adliya', 'Zinj', 'Hoora',
                'Amwaj Islands', 'Tubli', 'Sanabis',
                'Sitra', 'Arad', 'Hidd', 'Busaiteen',
            ]),
        ];
    }
}
