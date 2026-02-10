<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = $this->getCategories();
        $createdCategories = [];

        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'parent_id' => isset($categoryData['parent_slug']) && isset($createdCategories[$categoryData['parent_slug']])
                    ? $createdCategories[$categoryData['parent_slug']]->id
                    : null,
                'slug' => Str::slug($categoryData['name_en']),
                'icon' => $categoryData['icon'] ?? null,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);

            // Store reference for child categories
            $createdCategories[$category->slug] = $category;

            // Add English translation
            $category->translations()->create([
                'locale' => 'en',
                'name' => $categoryData['name_en'],
                'description' => $categoryData['description_en'],
            ]);

            // Add Arabic translation
            $category->translations()->create([
                'locale' => 'ar',
                'name' => $categoryData['name_ar'],
                'description' => $categoryData['description_ar'],
            ]);
        }

        $this->command->info('Categories seeded: ' . count($categories) . ' categories with translations');
    }

    private function getCategories(): array
    {
        return [
            // Parent category for all croissants
            [
                'name_en' => 'Croissants',
                'name_ar' => 'كرواسون',
                'description_en' => 'Fresh buttery croissants in various sizes and flavors',
                'description_ar' => 'كرواسون طازج بالزبدة بأحجام ونكهات متنوعة',
                'icon' => '🥐',
            ],
            // Croissant subcategories
            [
                'name_en' => 'Mini Size (30g)',
                'name_ar' => 'حجم صغير (30 جرام)',
                'description_en' => 'Mini croissants in various flavors - 30g each',
                'description_ar' => 'كرواسون صغير بنكهات متنوعة - 30 جرام لكل قطعة',
                'parent_slug' => 'croissants',
            ],
            [
                'name_en' => 'Medium Size (55g)',
                'name_ar' => 'حجم وسط (55 جرام)',
                'description_en' => 'Medium size croissants - 55g each, 100 pieces per box',
                'description_ar' => 'كرواسون حجم وسط - 55 جرام لكل قطعة، 100 قطعة في الصندوق',
                'parent_slug' => 'croissants',
            ],
            [
                'name_en' => 'Jumbo Size (120g)',
                'name_ar' => 'حجم جامبو (120 جرام)',
                'description_en' => 'Jumbo croissants - 120g each, 50 pieces per box',
                'description_ar' => 'كرواسون جامبو - 120 جرام لكل قطعة، 50 قطعة في الصندوق',
                'parent_slug' => 'croissants',
            ],
            [
                'name_en' => 'Rolls & Danish',
                'name_ar' => 'رولات ودانش',
                'description_en' => 'Croissant rolls (70g) and Danish pastries (80g)',
                'description_ar' => 'رولات الكرواسون (70 جرام) ومعجنات الدانش (80 جرام)',
                'parent_slug' => 'croissants',
            ],
            [
                'name_en' => 'Cookies Dough',
                'name_ar' => 'عجينة الكوكيز',
                'description_en' => 'Ready to bake cookie dough - 55g each',
                'description_ar' => 'عجينة كوكيز جاهزة للخبز - 55 جرام لكل قطعة',
                'icon' => '🍪',
            ],
            [
                'name_en' => 'Muffins & Babka',
                'name_ar' => 'مافن وبابكا',
                'description_en' => 'Muffins (140g) and Babka bread (300g)',
                'description_ar' => 'مافن (140 جرام) وخبز بابكا (300 جرام)',
                'icon' => '🧁',
            ],
            [
                'name_en' => 'Sourdough Breads',
                'name_ar' => 'خبز ساوردو',
                'description_en' => 'Sourdough breads with natural yeast - 900g each',
                'description_ar' => 'خبز ساوردو بالخميرة الطبيعية - 900 جرام لكل رغيف',
                'icon' => '🍞',
            ],
            [
                'name_en' => 'Ready Baked Breads',
                'name_ar' => 'خبز مخبوز جاهز',
                'description_en' => 'Samoon, Pavé, and Panini breads ready to serve',
                'description_ar' => 'خبز صمون وبافيه وبانيني جاهز للتقديم',
                'icon' => '🥖',
            ],
            [
                'name_en' => 'Half Baked Breads',
                'name_ar' => 'خبز نصف مخبوز',
                'description_en' => 'Half baked breads - finish baking at home',
                'description_ar' => 'خبز نصف مخبوز - أكمل الخبز في المنزل',
                'icon' => '🥖',
            ],
            [
                'name_en' => 'Burger Buns',
                'name_ar' => 'خبز برغر',
                'description_en' => 'Various burger buns for all your needs',
                'description_ar' => 'أنواع مختلفة من خبز البرغر لجميع احتياجاتك',
                'icon' => '🍔',
            ],
            [
                'name_en' => 'Slice Breads',
                'name_ar' => 'خبز توست',
                'description_en' => 'Sliced breads in various flavors - 450g per loaf',
                'description_ar' => 'خبز توست بنكهات متنوعة - 450 جرام لكل رغيف',
                'icon' => '🍞',
            ],
            [
                'name_en' => 'Paton Dough',
                'name_ar' => 'عجينة الباتون',
                'description_en' => 'Raw dough for professional bakers',
                'description_ar' => 'عجينة خام للخبازين المحترفين',
                'icon' => '🌾',
            ],
        ];
    }
}
