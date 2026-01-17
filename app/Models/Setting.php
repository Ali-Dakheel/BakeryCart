<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\isArray;

class Setting extends Model
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

    public static function get(string $key, $default = null)
    {
        $setting = static::find($key);
        if (!$setting) {
            return $default;
        }
        return static::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, $value, string $type = 'string'): bool
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
            ]
        )->exists();
    }

    protected static function castValue($value, string $type)
    {
        return match($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
