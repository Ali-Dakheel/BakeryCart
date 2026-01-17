<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialClosure extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'date',
        'reason'
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public static function isClosedOn($date): bool
    {
        return static::whereDate('date', $date)->exists();
    }

    public static function isClosedToday(): bool
    {
        return static::isClosedOn(today('Asia/Bahrain'));
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today('Asia/Bahrain'))
            ->orderBy('date');
    }
}
