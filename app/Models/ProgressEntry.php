<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressEntry extends Model
{
    protected $fillable = [
        'boq_id',
        'progress_date',
        'actual_qty'
    ];

    public function boq(): BelongsTo
    {
        return $this->belongsTo(Boq::class);
    }
}
