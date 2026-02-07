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

        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'slug' => Str::slug($categoryData['name_en']),
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);

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
            [
                'name_en' => 'Croissant Mini Size',
                'name_ar' => 'كرواسون صغير',
                'description_en' => 'Mini croissants in various flavors - 30g each',
                'description_ar' => 'كرواسون صغير بنكهات متنوعة - 30 جرام لكل قطعة',
            ],
            [
                'name_en' => 'Croissant Medium Size',
                'name_ar' => 'كرواسون وسط',
                'description_en' => 'Medium size croissants - 55g each, 100 pieces per box',
                'description_ar' => 'كرواسون حجم وسط - 55 جرام لكل قطعة، 100 قطعة في الصندوق',
            ],
            [
                'name_en' => 'Croissant Jumbo Size',
                'name_ar' => 'كرواسون جامبو',
                'description_en' => 'Jumbo croissants - 120g each, 50 pieces per box',
                'description_ar' => 'كرواسون جامبو - 120 جرام لكل قطعة، 50 قطعة في الصندوق',
            ],
            [
                'name_en' => 'Croissant Rolls & Danish',
                'name_ar' => 'رولات الكرواسون والدانش',
                'description_en' => 'Croissant rolls (70g) and Danish pastries (80g)',
                'description_ar' => 'رولات الكرواسون (70 جرام) ومعجنات الدانش (80 جرام)',
            ],
            [
                'name_en' => 'Cookies Dough',
                'name_ar' => 'عجينة الكوكيز',
                'description_en' => 'Ready to bake cookie dough - 55g each',
                'description_ar' => 'عجينة كوكيز جاهزة للخبز - 55 جرام لكل قطعة',
            ],
            [
                'name_en' => 'Muffins & Babka',
                'name_ar' => 'مافن وبابكا',
                'description_en' => 'Muffins (140g) and Babka bread (300g)',
                'description_ar' => 'مافن (140 جرام) وخبز بابكا (300 جرام)',
            ],
            [
                'name_en' => 'Sourdough Breads',
                'name_ar' => 'خبز ساوردو',
                'description_en' => 'Sourdough breads with natural yeast - 900g each',
                'description_ar' => 'خبز ساوردو بالخميرة الطبيعية - 900 جرام لكل رغيف',
            ],
            [
                'name_en' => 'Ready Baked Breads',
                'name_ar' => 'خبز مخبوز جاهز',
                'description_en' => 'Samoon, Pavé, and Panini breads ready to serve',
                'description_ar' => 'خبز صمون وبافيه وبانيني جاهز للتقديم',
            ],
            [
                'name_en' => 'Half Baked Breads',
                'name_ar' => 'خبز نصف مخبوز',
                'description_en' => 'Half baked breads - finish baking at home',
                'description_ar' => 'خبز نصف مخبوز - أكمل الخبز في المنزل',
            ],
            [
                'name_en' => 'Burger Buns',
                'name_ar' => 'خبز برغر',
                'description_en' => 'Various burger buns for all your needs',
                'description_ar' => 'أنواع مختلفة من خبز البرغر لجميع احتياجاتك',
            ],
            [
                'name_en' => 'Slice Breads',
                'name_ar' => 'خبز توست',
                'description_en' => 'Sliced breads in various flavors - 450g per loaf',
                'description_ar' => 'خبز توست بنكهات متنوعة - 450 جرام لكل رغيف',
            ],
            [
                'name_en' => 'Paton Dough',
                'name_ar' => 'عجينة الباتون',
                'description_en' => 'Raw dough for professional bakers',
                'description_ar' => 'عجينة خام للخبازين المحترفين',
            ],
        ];
    }
}
