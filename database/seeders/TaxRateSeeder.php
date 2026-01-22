<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

final class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        // Bahrain VAT - 10%
        TaxRate::create([
            'name' => 'VAT',
            'rate' => 10.00,
            'is_inclusive' => false,
            'applies_to' => 'all',
            'applicable_ids' => null,
            'is_active' => true,
            'effective_from' => null,
            'effective_to' => null,
        ]);

        $this->command->info('Tax rates seeded: Bahrain VAT 10%');
    }
}
