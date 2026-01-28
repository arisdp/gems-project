<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_code',
        'project_name'
    ];

    public function workPackages(): HasMany
    {
        return $this->hasMany(WorkPackage::class);
    }

    /**
     * Total Contract Value (Σ WP Amount)
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->workPackages->sum->total_amount;
    }

    /**
     * Project Progress (%) - Cost Weighted
     */
    public function getProgressAttribute(): float
    {
        $totalAmount = $this->total_amount;

        if ($totalAmount == 0) return 0;

        $weightedProgress = $this->workPackages->sum(function ($wp) {
            return $wp->progress * $wp->total_amount;
        });

        return round($weightedProgress / $totalAmount, 2);
    }
}
