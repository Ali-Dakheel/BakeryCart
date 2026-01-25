<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true ;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => ['nullable', 'exists:addresses,id'],
            'shipping_name' => ['required_without:shipping_address_id', 'string', 'max:255'],
            'shipping_phone' => ['required_without:shipping_address_id', 'string', 'max:20'],
            'shipping_address_line_1' => ['required_without:shipping_address_id', 'string', 'max:255'],
            'shipping_address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping_building' => ['nullable', 'string', 'max:50'],
            'shipping_floor' => ['nullable', 'string', 'max:10'],
            'shipping_apartment' => ['nullable', 'string', 'max:10'],
            'shipping_area' => ['required_without:shipping_address_id', 'string', 'max:100'],
            'shipping_city' => ['required_without:shipping_address_id', 'string', 'max:100'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'delivery_date' => ['nullable', 'date', 'after:today'],
            'delivery_time_slot' => ['nullable', 'string', 'in:09:00-12:00,12:00-15:00,15:00-18:00'],
            'customer_notes' => ['nullable', 'string', 'max:500'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            'payment_method' => ['required', 'string', 'in:card,benefit_pay,cod'],
        ];
    }
}
