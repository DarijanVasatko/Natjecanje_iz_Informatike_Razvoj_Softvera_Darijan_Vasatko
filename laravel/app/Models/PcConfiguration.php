<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcConfiguration extends Model
{
    protected $table = 'pc_configurations';

    protected $fillable = [
        'user_id',
        'session_id',
        'naziv',
        'ukupna_cijena',
    ];

    protected $casts = [
        'ukupna_cijena' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PcConfigurationItem::class, 'configuration_id');
    }

    public function calculateTotalPrice(): float
    {
        $total = $this->items()->sum('cijena_u_trenutku');
        $this->ukupna_cijena = $total;
        $this->save();
        return $total;
    }

    public function addComponent(int $componentTypeId, int $proizvodId, float $cijena): PcConfigurationItem
    {
        $this->items()->where('component_type_id', $componentTypeId)->delete();

        $item = $this->items()->create([
            'component_type_id' => $componentTypeId,
            'proizvod_id' => $proizvodId,
            'cijena_u_trenutku' => $cijena,
        ]);

        $this->calculateTotalPrice();

        return $item;
    }

    public function removeComponent(int $componentTypeId): bool
    {
        $deleted = $this->items()->where('component_type_id', $componentTypeId)->delete();
        $this->calculateTotalPrice();
        return $deleted > 0;
    }

    public function getComponentByType(int $componentTypeId): ?PcConfigurationItem
    {
        return $this->items()->where('component_type_id', $componentTypeId)->first();
    }

    public function isComplete(): bool
    {
        $requiredTypes = PcComponentType::where('obavezan', true)->pluck('id');
        $selectedTypes = $this->items()->pluck('component_type_id');

        return $requiredTypes->diff($selectedTypes)->isEmpty();
    }

    public function getTotalTdp(): int
    {
        $total = 0;
        foreach ($this->items()->with('proizvod.pcSpec')->get() as $item) {
            if ($item->proizvod && $item->proizvod->pcSpec) {
                $total += $item->proizvod->pcSpec->tdp ?? 0;
            }
        }
        return $total;
    }

    public function getRecommendedWattage(): int
    {
        $tdp = $this->getTotalTdp();
        return (int) ceil(($tdp + 50) * 1.2);
    }
}
