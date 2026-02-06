<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcConfigurationItem extends Model
{
    protected $table = 'pc_configuration_items';

    protected $fillable = [
        'configuration_id',
        'component_type_id',
        'proizvod_id',
        'cijena_u_trenutku',
    ];

    protected $casts = [
        'cijena_u_trenutku' => 'decimal:2',
    ];

    /**
     * Get the configuration
     */
    public function configuration(): BelongsTo
    {
        return $this->belongsTo(PcConfiguration::class, 'configuration_id');
    }

    /**
     * Get the component type
     */
    public function componentType(): BelongsTo
    {
        return $this->belongsTo(PcComponentType::class, 'component_type_id');
    }

    /**
     * Get the product
     */
    public function proizvod(): BelongsTo
    {
        return $this->belongsTo(Proizvod::class, 'proizvod_id', 'Proizvod_ID');
    }
}
