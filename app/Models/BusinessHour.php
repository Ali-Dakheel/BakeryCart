<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class BusinessHour extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'day_of_week',
        'opening_time',
        'closing_time',
        'is_closed',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_closed' => 'boolean',
        ];
    }

    public static function isOpenAt(int $dayOfWeek, string $time): bool
    {
        $hours = self::where('day_of_week', $dayOfWeek)->first();
        if (! $hours || $hours->is_closed) {
            return false;
        }

        return $time >= $hours->opening_time && $time <= $hours->closing_time;
    }

    public static function isCurrentlyOpen(): bool
    {
        $now = now('Asia/Bahrain');
        $dayOfWeek = $now->dayOfWeek;
        $time = $now->format('H:i');

        if (SpecialClosure::isClosedToday()) {
            return false;
        }

        return self::isOpenAt($dayOfWeek, $time);
    }
}
