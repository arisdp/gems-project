<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPackage extends Model
{
    protected $fillable = [
        'project_id',
        'wp_code',
        'wp_name',
        'discipline_code'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function boqs(): HasMany
    {
        return $this->hasMany(Boq::class);
    }

    /**
     * Total BOQ Amount per WP
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->boqs->sum->amount;
    }

    /**
     * WP Progress (%) - Cost Weighted
     */
    public function getProgressAttribute(): float
    {
        $totalAmount = $this->total_amount;

        if ($totalAmount == 0) return 0;

        $weighted = $this->boqs->sum(function ($boq) {
            return $boq->progress * $boq->amount;
        });

        return round($weighted / $totalAmount, 2);
    }
}
