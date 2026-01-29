<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('shippingAddress.id')
                    ->label('Shipping address')
                    ->placeholder('-'),
                TextEntry::make('customer_name'),
                TextEntry::make('customer_email'),
                TextEntry::make('customer_phone')
                    ->placeholder('-'),
                TextEntry::make('shipping_name'),
                TextEntry::make('shipping_phone')
                    ->placeholder('-'),
                TextEntry::make('shipping_address_line_1'),
                TextEntry::make('shipping_address_line_2')
                    ->placeholder('-'),
                TextEntry::make('shipping_building')
                    ->placeholder('-'),
                TextEntry::make('shipping_floor')
                    ->placeholder('-'),
                TextEntry::make('shipping_apartment')
                    ->placeholder('-'),
                TextEntry::make('shipping_area'),
                TextEntry::make('shipping_city'),
                TextEntry::make('delivery_instructions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('coupon_code')
                    ->placeholder('-'),
                TextEntry::make('coupon_discount')
                    ->numeric(),
                TextEntry::make('tax_percentage')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('shipping_fee')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('status'),
                TextEntry::make('payment_status'),
                TextEntry::make('fulfillment_status'),
                TextEntry::make('delivery_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('delivery_time_slot')
                    ->placeholder('-'),
                TextEntry::make('delivered_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('customer_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('admin_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('source'),
                TextEntry::make('utm_source')
                    ->placeholder('-'),
                TextEntry::make('utm_medium')
                    ->placeholder('-'),
                TextEntry::make('utm_campaign')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Order $record): bool => $record->trashed()),
            ]);
    }
}
