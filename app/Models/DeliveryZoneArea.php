<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryZoneArea extends Model
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
