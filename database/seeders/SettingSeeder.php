<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

final class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'store_name',
                'value' => 'Easy Bake',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Store display name',
            ],
            [
                'key' => 'store_email',
                'value' => 'hello@easybake.bh',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Primary contact email',
            ],
            [
                'key' => 'store_phone',
                'value' => '+973-1700-0000',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Customer service phone',
            ],
            [
                'key' => 'store_address',
                'value' => 'Building 123, Road 456, Block 789, Manama, Bahrain',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Physical store address',
            ],
            [
                'key' => 'store_instagram',
                'value' => '@easybake.bh',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'description' => 'Instagram handle',
            ],

            // Shop Settings
            [
                'key' => 'currency',
                'value' => 'BHD',
                'type' => 'string',
                'group' => 'shop',
                'is_public' => true,
                'description' => 'Store currency code',
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'BD',
                'type' => 'string',
                'group' => 'shop',
                'is_public' => true,
                'description' => 'Currency display symbol',
            ],
            [
                'key' => 'tax_rate',
                'value' => '10',
                'type' => 'integer',
                'group' => 'shop',
                'is_public' => true,
                'description' => 'Default tax rate percentage',
            ],
            [
                'key' => 'min_order_amount',
                'value' => '5.000',
                'type' => 'string',
                'group' => 'shop',
                'is_public' => true,
                'description' => 'Minimum order amount in BHD',
            ],
            [
                'key' => 'max_order_amount',
                'value' => '500.000',
                'type' => 'string',
                'group' => 'shop',
                'is_public' => false,
                'description' => 'Maximum order amount in BHD',
            ],

            // Delivery Settings
            [
                'key' => 'delivery_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'delivery',
                'is_public' => true,
                'description' => 'Enable/disable delivery',
            ],
            [
                'key' => 'pickup_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'delivery',
                'is_public' => true,
                'description' => 'Enable/disable store pickup',
            ],
            [
                'key' => 'same_day_delivery_cutoff',
                'value' => '14:00',
                'type' => 'string',
                'group' => 'delivery',
                'is_public' => true,
                'description' => 'Cutoff time for same-day delivery',
            ],
            [
                'key' => 'advance_order_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'delivery',
                'is_public' => true,
                'description' => 'How many days in advance customers can order',
            ],

            // Notification Settings
            [
                'key' => 'order_notification_email',
                'value' => 'orders@easybake.bh',
                'type' => 'string',
                'group' => 'notification',
                'is_public' => false,
                'description' => 'Email for order notifications',
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'integer',
                'group' => 'notification',
                'is_public' => false,
                'description' => 'Notify when stock falls below this',
            ],

            // System Settings
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => false,
                'description' => 'Enable maintenance mode',
            ],
            [
                'key' => 'guest_checkout',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => true,
                'description' => 'Allow guest checkout',
            ],
            [
                'key' => 'reviews_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => true,
                'description' => 'Enable product reviews',
            ],
            [
                'key' => 'reviews_require_approval',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'system',
                'is_public' => false,
                'description' => 'Require admin approval for reviews',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings seeded: ' . count($settings) . ' configuration values');
    }
}
