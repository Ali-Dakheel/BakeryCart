<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('order_id')
                    ->relationship('order', 'id'),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('title'),
                Textarea::make('comment')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_verified_purchase')
                    ->required(),
                Toggle::make('is_approved')
                    ->required(),
                TextInput::make('helpful_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('admin_response')
                    ->columnSpanFull(),
                DateTimePicker::make('responded_at'),
            ]);
    }
}
