<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
