<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    use HasFactory;

    /** @var string */
    protected $primaryKey = 'key';

    /** @var string */
    protected $keyType = 'string';

    /** @var bool */
    public $incrementing = false;

    /** @var bool */
    public const UPDATED_AT = 'updated_at';

    /** @var bool */
    public const CREATED_AT = null;

    /** @var array<int, string> */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'is_public',
        'description',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::find($key);
        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, mixed $value, string $type = 'string'): bool
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
            ]
        )->exists();
    }

    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
