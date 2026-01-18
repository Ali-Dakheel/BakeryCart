<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DeliveryZoneArea extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'delivery_zone_id',
        'area_name',
    ];

    /** @return BelongsTo<DeliveryZone, DeliveryZoneArea> */
    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZoneArea::class);
    }
}
