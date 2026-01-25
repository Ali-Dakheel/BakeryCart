<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,confirmed,delivered,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
