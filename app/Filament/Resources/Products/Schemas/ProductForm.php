<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->translations->first()?->name ?? $record->slug)
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    ]),

                Section::make('Pricing')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001),
                        TextInput::make('compare_at_price')
                            ->label('Compare at Price')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->helperText('Original price for showing discount'),
                        TextInput::make('cost')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->helperText('Cost price (not shown to customers)'),
                    ]),

                Section::make('Inventory')
                    ->columns(3)
                    ->schema([
                        TextInput::make('current_stock')
                            ->label('Current Stock')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('low_stock_threshold')
                            ->label('Low Stock Alert')
                            ->required()
                            ->numeric()
                            ->default(10),
                        Toggle::make('track_inventory')
                            ->label('Track Inventory')
                            ->default(true),
                    ]),

                Section::make('Production & Availability')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        TextInput::make('daily_production_capacity')
                            ->label('Daily Capacity')
                            ->numeric(),
                        TextInput::make('lead_time_hours')
                            ->label('Lead Time (hours)')
                            ->numeric(),
                        TextInput::make('preparation_time_minutes')
                            ->label('Prep Time (minutes)')
                            ->numeric(),
                        TimePicker::make('available_from_time')
                            ->label('Available From'),
                        TimePicker::make('available_to_time')
                            ->label('Available Until'),
                        TextInput::make('weight')
                            ->numeric()
                            ->suffix('g'),
                    ]),

                Section::make('Status')
                    ->columns(4)
                    ->schema([
                        Toggle::make('is_available')
                            ->label('Available')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false),
                        Toggle::make('is_taxable')
                            ->label('Taxable')
                            ->default(true),
                        Toggle::make('requires_shipping')
                            ->label('Requires Shipping')
                            ->default(false),
                    ]),

                Section::make('Scheduling')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('available_from')
                            ->label('Available From Date'),
                        DateTimePicker::make('available_until')
                            ->label('Available Until Date'),
                    ]),

                Section::make('SEO')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(2),
                    ]),
            ]);
    }
}
