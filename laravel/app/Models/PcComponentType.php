<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcComponentType extends Model
{
    protected $table = 'pc_component_types';

    protected $fillable = [
        'naziv',
        'slug',
        'redoslijed',
        'ikona',
        'obavezan',
    ];

    protected $casts = [
        'obavezan' => 'boolean',
    ];

    /**
     * Get all component specs of this type
     */
    public function specs(): HasMany
    {
        return $this->hasMany(PcComponentSpec::class, 'component_type_id');
    }

    /**
     * Get all configuration items of this type
     */
    public function configurationItems(): HasMany
    {
        return $this->hasMany(PcConfigurationItem::class, 'component_type_id');
    }

    /**
     * Get products of this component type
     */
    public function products()
    {
        return $this->hasManyThrough(
            Proizvod::class,
            PcComponentSpec::class,
            'component_type_id', // FK on pc_component_specs
            'Proizvod_ID',       // FK on proizvod
            'id',                // Local key on pc_component_types
            'proizvod_id'        // Local key on pc_component_specs
        );
    }
}
