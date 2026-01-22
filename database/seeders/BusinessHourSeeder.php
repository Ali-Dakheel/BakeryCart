<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BusinessHour;
use Illuminate\Database\Seeder;

final class BusinessHourSeeder extends Seeder
{
    public function run(): void
    {
        $hours = [
            // Sunday (0) - Standard hours
            ['day_of_week' => 0, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Monday (1)
            ['day_of_week' => 1, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Tuesday (2)
            ['day_of_week' => 2, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Wednesday (3)
            ['day_of_week' => 3, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Thursday (4)
            ['day_of_week' => 4, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Friday (5) - Opens after Jumu'ah prayer
            ['day_of_week' => 5, 'opening_time' => '14:00', 'closing_time' => '22:00', 'is_closed' => false],
            // Saturday (6)
            ['day_of_week' => 6, 'opening_time' => '07:00', 'closing_time' => '22:00', 'is_closed' => false],
        ];

        foreach ($hours as $hour) {
            BusinessHour::create($hour);
        }

        $this->command->info('Business hours seeded: 7 days (Friday opens 14:00 after Jumu\'ah)');
    }
}
