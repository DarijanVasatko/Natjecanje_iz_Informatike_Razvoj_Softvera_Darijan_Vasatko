<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcComponentSpec extends Model
{
    protected $table = 'pc_component_specs';

    protected $fillable = [
        'proizvod_id',
        'component_type_id',
        'socket_type',
        'ram_type',
        'form_factor',
        'wattage',
        'tdp',
    ];

    /**
     * Get the product
     */
    public function proizvod(): BelongsTo
    {
        return $this->belongsTo(Proizvod::class, 'proizvod_id', 'Proizvod_ID');
    }

    /**
     * Get the component type
     */
    public function componentType(): BelongsTo
    {
        return $this->belongsTo(PcComponentType::class, 'component_type_id');
    }

    /**
     * Check if this component is compatible with another
     */
    public function isCompatibleWith(PcComponentSpec $other): bool
    {
        // CPU ↔ Matična: provjeri socket
        if ($this->socket_type && $other->socket_type) {
            if ($this->socket_type !== $other->socket_type) {
                return false;
            }
        }

        // RAM ↔ Matična: provjeri DDR tip
        if ($this->ram_type && $other->ram_type) {
            if ($this->ram_type !== $other->ram_type) {
                return false;
            }
        }

        // Matična ↔ Kućište: provjeri form factor
        if ($this->form_factor && $other->form_factor) {
            return $this->isFormFactorCompatible($this->form_factor, $other->form_factor);
        }

        return true;
    }

    /**
     * Check form factor compatibility
     * ATX kućište prima ATX, mATX, ITX
     * mATX kućište prima mATX, ITX
     * ITX kućište prima samo ITX
     */
    protected function isFormFactorCompatible(string $motherboardFF, string $caseFF): bool
    {
        $compatibility = [
            'ATX' => ['ATX', 'mATX', 'ITX'],
            'mATX' => ['mATX', 'ITX'],
            'ITX' => ['ITX'],
        ];

        // If checking case compatibility with motherboard
        // Case form factor determines what motherboards it can hold
        if (isset($compatibility[$caseFF])) {
            return in_array($motherboardFF, $compatibility[$caseFF]);
        }

        return true;
    }
}
