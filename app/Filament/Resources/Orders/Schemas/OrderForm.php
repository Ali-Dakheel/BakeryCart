<?php

declare(strict_types=1);

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('order_number')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('shipping_address_id')
                            ->relationship('shippingAddress', 'address_line_1')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Customer Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('customer_name')
                            ->required(),
                        TextInput::make('customer_email')
                            ->email()
                            ->required(),
                        TextInput::make('customer_phone')
                            ->tel(),
                    ]),

                Section::make('Shipping Address')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        TextInput::make('shipping_name')
                            ->required(),
                        TextInput::make('shipping_phone')
                            ->tel(),
                        TextInput::make('shipping_address_line_1')
                            ->label('Address Line 1')
                            ->required(),
                        TextInput::make('shipping_address_line_2')
                            ->label('Address Line 2'),
                        TextInput::make('shipping_building')
                            ->label('Building'),
                        TextInput::make('shipping_floor')
                            ->label('Floor'),
                        TextInput::make('shipping_apartment')
                            ->label('Apartment'),
                        TextInput::make('shipping_area')
                            ->label('Area')
                            ->required(),
                        TextInput::make('shipping_city')
                            ->label('City')
                            ->required(),
                        Textarea::make('delivery_instructions')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),

                Section::make('Order Totals')
                    ->columns(4)
                    ->schema([
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001),
                        TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->default(0),
                        TextInput::make('coupon_code')
                            ->disabled(),
                        TextInput::make('coupon_discount')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->default(0),
                        TextInput::make('tax_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->default(10),
                        TextInput::make('tax_amount')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->default(0),
                        TextInput::make('shipping_fee')
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001)
                            ->default(0),
                        TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('BHD')
                            ->step(0.001),
                    ]),

                Section::make('Status')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready',
                                'out_for_delivery' => 'Out for Delivery',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),
                        Select::make('fulfillment_status')
                            ->options([
                                'unfulfilled' => 'Unfulfilled',
                                'partially_fulfilled' => 'Partially Fulfilled',
                                'fulfilled' => 'Fulfilled',
                            ])
                            ->required()
                            ->default('unfulfilled'),
                    ]),

                Section::make('Delivery')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('delivery_date'),
                        TextInput::make('delivery_time_slot'),
                        DateTimePicker::make('delivered_at'),
                    ]),

                Section::make('Notes')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('customer_notes')
                            ->label('Customer Notes')
                            ->rows(2),
                        Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(2),
                        Textarea::make('cancellation_reason')
                            ->label('Cancellation Reason')
                            ->rows(2)
                            ->visible(fn ($get) => $get('status') === 'cancelled'),
                    ]),

                Section::make('Tracking')
                    ->collapsible()
                    ->collapsed()
                    ->columns(3)
                    ->schema([
                        TextInput::make('source')
                            ->default('web'),
                        TextInput::make('utm_source'),
                        TextInput::make('utm_medium'),
                        TextInput::make('utm_campaign'),
                        TextInput::make('ip_address')
                            ->disabled(),
                    ]),
            ]);
    }
}
