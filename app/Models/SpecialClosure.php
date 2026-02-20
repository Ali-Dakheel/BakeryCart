<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SpecialClosure extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'date',
        'reason',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public static function isClosedOn(mixed $date): bool
    {
        return self::whereDate('date', $date)->exists();
    }

    public static function isClosedToday(): bool
    {
        return self::isClosedOn(today('Asia/Bahrain'));
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', today('Asia/Bahrain'))
            ->orderBy('date');
    }
}
