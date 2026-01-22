<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing categories
        $categories = Category::whereNull('parent_id')->get();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        $products = $this->getBakeryProducts();

        foreach ($products as $productData) {
            // Assign to a random category
            $category = $categories->random();

            $product = Product::create([
                'category_id' => $category->id,
                'slug' => Str::slug($productData['name_en']) . '-' . fake()->unique()->numberBetween(1, 1000),
                'sku' => 'EB-' . strtoupper(Str::substr(Str::slug($productData['name_en']), 0, 4)) . '-' . fake()->unique()->numberBetween(100, 999),
                'price' => $productData['price'],
                'compare_at_price' => $productData['compare_at_price'] ?? null,
                'cost' => round($productData['price'] * 0.6, 3),
                'current_stock' => fake()->numberBetween(20, 100),
                'low_stock_threshold' => 10,
                'track_inventory' => true,
                'daily_production_capacity' => fake()->numberBetween(50, 200),
                'lead_time_hours' => fake()->randomElement([0, 2, 4]),
                'is_available' => true,
                'is_featured' => $productData['is_featured'] ?? false,
                'is_taxable' => true,
                'requires_shipping' => true,
                'views_count' => fake()->numberBetween(50, 500),
                'sales_count' => fake()->numberBetween(10, 200),
            ]);

            // Add English translation
            $product->translations()->create([
                'locale' => 'en',
                'name' => $productData['name_en'],
                'short_description' => $productData['description_en'],
                'description' => $productData['description_en'] . ' ' . fake()->paragraph(),
            ]);

            // Add Arabic translation
            $product->translations()->create([
                'locale' => 'ar',
                'name' => $productData['name_ar'],
                'short_description' => $productData['description_ar'],
                'description' => $productData['description_ar'],
            ]);

            // Add images
            for ($i = 0; $i < 2; $i++) {
                $product->images()->create([
                    'image_url' => fake()->imageUrl(800, 600, 'food'),
                    'alt_text' => $productData['name_en'],
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }

            // Add variants for some products
            if (fake()->boolean(60)) {
                $basePrice = (float) $product->price;

                $product->variants()->createMany([
                    [
                        'name' => 'Single',
                        'sku' => $product->sku . '-1PC',
                        'price' => $basePrice,
                        'stock' => fake()->numberBetween(20, 50),
                        'pack_quantity' => 1,
                        'is_available' => true,
                        'sort_order' => 0,
                    ],
                    [
                        'name' => '6-Pack',
                        'sku' => $product->sku . '-6PK',
                        'price' => round($basePrice * 5.5, 3),
                        'stock' => fake()->numberBetween(10, 30),
                        'pack_quantity' => 6,
                        'is_available' => true,
                        'sort_order' => 1,
                    ],
                ]);
            }
        }

        $this->command->info('Products seeded: ' . count($products) . ' bakery items with translations, images, and variants');
    }

    private function getBakeryProducts(): array
    {
        return [
            // Croissants
            [
                'name_en' => 'Butter Croissant',
                'name_ar' => 'كرواسون بالزبدة',
                'description_en' => 'Flaky, buttery French croissant made with premium butter.',
                'description_ar' => 'كرواسون فرنسي مقرمش بالزبدة الفاخرة.',
                'price' => 0.750,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Chocolate Croissant',
                'name_ar' => 'كرواسون بالشوكولاتة',
                'description_en' => 'Classic pain au chocolat with rich Belgian chocolate.',
                'description_ar' => 'باين أو شوكولا كلاسيكي بشوكولاتة بلجيكية غنية.',
                'price' => 0.950,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Almond Croissant',
                'name_ar' => 'كرواسون باللوز',
                'description_en' => 'Filled with almond cream and topped with sliced almonds.',
                'description_ar' => 'محشو بكريمة اللوز ومغطى بشرائح اللوز.',
                'price' => 1.200,
            ],
            [
                'name_en' => 'Zaatar Croissant',
                'name_ar' => 'كرواسون بالزعتر',
                'description_en' => 'Savory croissant with traditional Middle Eastern zaatar.',
                'description_ar' => 'كرواسون مالح بالزعتر الشرق أوسطي التقليدي.',
                'price' => 0.850,
            ],

            // Breads
            [
                'name_en' => 'French Baguette',
                'name_ar' => 'باجيت فرنسي',
                'description_en' => 'Traditional French baguette with crispy crust.',
                'description_ar' => 'باجيت فرنسي تقليدي بقشرة مقرمشة.',
                'price' => 1.000,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Sourdough Loaf',
                'name_ar' => 'خبز العجين المخمر',
                'description_en' => 'Naturally leavened sourdough with perfect tang.',
                'description_ar' => 'خبز مخمر طبيعياً بنكهة مميزة.',
                'price' => 2.500,
            ],
            [
                'name_en' => 'Focaccia',
                'name_ar' => 'فوكاتشا',
                'description_en' => 'Italian flatbread with olive oil and rosemary.',
                'description_ar' => 'خبز إيطالي مسطح بزيت الزيتون وإكليل الجبل.',
                'price' => 1.500,
            ],
            [
                'name_en' => 'Brioche Loaf',
                'name_ar' => 'خبز البريوش',
                'description_en' => 'Rich, buttery French brioche bread.',
                'description_ar' => 'خبز بريوش فرنسي غني بالزبدة.',
                'price' => 2.000,
            ],
            [
                'name_en' => 'Whole Wheat Bread',
                'name_ar' => 'خبز القمح الكامل',
                'description_en' => 'Healthy whole wheat bread, perfect for sandwiches.',
                'description_ar' => 'خبز القمح الكامل الصحي، مثالي للسندويشات.',
                'price' => 1.250,
            ],

            // Pastries
            [
                'name_en' => 'Cinnamon Danish',
                'name_ar' => 'دانش بالقرفة',
                'description_en' => 'Swirled danish pastry with cinnamon and cream cheese.',
                'description_ar' => 'معجنات دانش ملفوفة بالقرفة والجبن الكريمي.',
                'price' => 1.100,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Apple Turnover',
                'name_ar' => 'فطيرة التفاح',
                'description_en' => 'Flaky pastry filled with spiced apple filling.',
                'description_ar' => 'فطيرة مقرمشة محشوة بحشوة التفاح المتبلة.',
                'price' => 1.250,
            ],
            [
                'name_en' => 'Cheese Danish',
                'name_ar' => 'دانش بالجبن',
                'description_en' => 'Cream cheese filled danish with fruit topping.',
                'description_ar' => 'دانش محشو بالجبن الكريمي مع فواكه.',
                'price' => 1.100,
            ],
            [
                'name_en' => 'Pain aux Raisins',
                'name_ar' => 'باين أو ريزان',
                'description_en' => 'Spiral pastry with custard cream and raisins.',
                'description_ar' => 'معجنات حلزونية بكريمة الكاسترد والزبيب.',
                'price' => 0.950,
            ],

            // Cakes & Treats
            [
                'name_en' => 'Chocolate Éclair',
                'name_ar' => 'إكلير بالشوكولاتة',
                'description_en' => 'Choux pastry filled with vanilla cream, topped with chocolate.',
                'description_ar' => 'معجنات شو محشوة بكريمة الفانيليا ومغطاة بالشوكولاتة.',
                'price' => 1.500,
            ],
            [
                'name_en' => 'Fruit Tart',
                'name_ar' => 'تارت الفواكه',
                'description_en' => 'Buttery tart shell with pastry cream and fresh fruits.',
                'description_ar' => 'قشرة تارت بالزبدة مع كريمة المعجنات وفواكه طازجة.',
                'price' => 2.500,
                'compare_at_price' => 3.000,
            ],
            [
                'name_en' => 'Lemon Tart',
                'name_ar' => 'تارت الليمون',
                'description_en' => 'Tangy lemon curd in a crisp pastry shell.',
                'description_ar' => 'كريمة الليمون الحامضة في قشرة معجنات مقرمشة.',
                'price' => 2.250,
            ],
            [
                'name_en' => 'Carrot Cake Slice',
                'name_ar' => 'قطعة كيك الجزر',
                'description_en' => 'Moist carrot cake with cream cheese frosting.',
                'description_ar' => 'كيك الجزر الطري مع كريمة الجبن.',
                'price' => 1.750,
            ],
            [
                'name_en' => 'Tiramisu Cup',
                'name_ar' => 'كوب تيراميسو',
                'description_en' => 'Classic Italian tiramisu in an individual serving.',
                'description_ar' => 'تيراميسو إيطالي كلاسيكي بحصة فردية.',
                'price' => 2.000,
            ],

            // Savory
            [
                'name_en' => 'Cheese Manakish',
                'name_ar' => 'مناقيش بالجبن',
                'description_en' => 'Traditional Middle Eastern flatbread with melted cheese.',
                'description_ar' => 'خبز شرق أوسطي تقليدي بالجبن الذائب.',
                'price' => 1.000,
            ],
            [
                'name_en' => 'Spinach Fatayer',
                'name_ar' => 'فطاير السبانخ',
                'description_en' => 'Triangular pastry filled with seasoned spinach.',
                'description_ar' => 'فطيرة مثلثة محشوة بالسبانخ المتبل.',
                'price' => 0.500,
            ],
            [
                'name_en' => 'Chicken Puff',
                'name_ar' => 'باف الدجاج',
                'description_en' => 'Puff pastry filled with seasoned chicken.',
                'description_ar' => 'عجينة منفوخة محشوة بالدجاج المتبل.',
                'price' => 0.750,
            ],
            [
                'name_en' => 'Meat Sambousek',
                'name_ar' => 'سمبوسك باللحم',
                'description_en' => 'Crispy pastry filled with spiced minced meat.',
                'description_ar' => 'معجنات مقرمشة محشوة باللحم المفروم المتبل.',
                'price' => 0.600,
            ],

            // Cookies
            [
                'name_en' => 'Chocolate Chip Cookie',
                'name_ar' => 'كوكيز برقائق الشوكولاتة',
                'description_en' => 'Classic chewy cookie loaded with chocolate chips.',
                'description_ar' => 'كوكيز طري كلاسيكي محمل برقائق الشوكولاتة.',
                'price' => 0.500,
            ],
            [
                'name_en' => 'Double Chocolate Cookie',
                'name_ar' => 'كوكيز شوكولاتة مزدوجة',
                'description_en' => 'Rich chocolate cookie with white chocolate chunks.',
                'description_ar' => 'كوكيز شوكولاتة غني بقطع الشوكولاتة البيضاء.',
                'price' => 0.600,
            ],
            [
                'name_en' => 'Oatmeal Raisin Cookie',
                'name_ar' => 'كوكيز الشوفان والزبيب',
                'description_en' => 'Wholesome oatmeal cookie with plump raisins.',
                'description_ar' => 'كوكيز الشوفان الصحي مع الزبيب.',
                'price' => 0.450,
            ],
            [
                'name_en' => 'Maamoul',
                'name_ar' => 'معمول',
                'description_en' => 'Traditional Middle Eastern date-filled cookies.',
                'description_ar' => 'معمول شرق أوسطي تقليدي محشو بالتمر.',
                'price' => 0.400,
            ],

            // Seasonal/Special
            [
                'name_en' => 'King Cake',
                'name_ar' => 'كيكة الملك',
                'description_en' => 'Festive brioche cake with colorful decorations.',
                'description_ar' => 'كيكة بريوش احتفالية بزينة ملونة.',
                'price' => 8.000,
                'compare_at_price' => 10.000,
                'is_featured' => true,
            ],
            [
                'name_en' => 'Birthday Cake (6-inch)',
                'name_ar' => 'كيكة عيد ميلاد (6 بوصة)',
                'description_en' => 'Customizable birthday cake, serves 8-10.',
                'description_ar' => 'كيكة عيد ميلاد قابلة للتخصيص، تكفي 8-10 أشخاص.',
                'price' => 15.000,
            ],
        ];
    }
}
