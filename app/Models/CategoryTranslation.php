<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
    ];

    /**  @return BelongsTo<Category, CategoryTranslation> */
    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
