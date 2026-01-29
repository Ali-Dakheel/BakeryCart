<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('category.id')
                    ->label('Category'),
                TextEntry::make('slug'),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('short_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('compare_at_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('cost')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('current_stock')
                    ->numeric(),
                TextEntry::make('low_stock_threshold')
                    ->numeric(),
                IconEntry::make('track_inventory')
                    ->boolean(),
                TextEntry::make('daily_production_capacity')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('lead_time_hours')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('preparation_time_minutes')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('available_from_time')
                    ->time()
                    ->placeholder('-'),
                TextEntry::make('available_to_time')
                    ->time()
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('attributes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_available')
                    ->boolean(),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_taxable')
                    ->boolean(),
                IconEntry::make('requires_shipping')
                    ->boolean(),
                TextEntry::make('meta_title')
                    ->placeholder('-'),
                TextEntry::make('meta_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('og_image_url')
                    ->placeholder('-'),
                TextEntry::make('available_from')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('available_until')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('views_count')
                    ->numeric(),
                TextEntry::make('sales_count')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),
            ]);
    }
}
