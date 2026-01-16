<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bread = Category::factory()->active()->withTranslations()->create(['sort_order' => 1]);

        $pastries = Category::factory()->active()->withTranslations()->create(['sort_order' => 2]);

        Category::factory()->count(3)->child($bread)->withTranslations()->create();

        Category::factory()->count(3)->child($pastries)->withTranslations()->create();

        Category::factory()->count(3)->withTranslations()->create();

        $this->command->info('Categories seeded with translations!');
    }
}
