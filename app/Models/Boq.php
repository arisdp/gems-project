<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boq extends Model
{
    protected $fillable = [
        'work_package_id',
        'boq_code',
        'description',
        'uom',
        'budget_qty',
        'unit_rate'
    ];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class);
    }

    public function progressEntries(): HasMany
    {
        return $this->hasMany(ProgressEntry::class);
    }

    /**
     * BOQ Amount = budget_qty × unit_rate
     */
    public function getAmountAttribute(): float
    {
        return $this->budget_qty * $this->unit_rate;
    }

    /**
     * Total Actual Qty
     */
    public function getActualQtyAttribute(): float
    {
        return $this->progressEntries->sum('actual_qty');
    }

    /**
     * BOQ Progress (%) (max 100%)
     */
    public function getProgressAttribute(): float
    {
        if ($this->budget_qty == 0) return 0;

        $progress = ($this->actual_qty / $this->budget_qty) * 100;

        return round(min($progress, 100), 2);
    }
}
