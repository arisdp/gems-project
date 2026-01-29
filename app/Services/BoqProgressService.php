<?php

namespace App\Services;

use App\Models\Boq;
use App\Models\ProgressEntry;
use Illuminate\Validation\ValidationException;

class BoqProgressService
{
    public function store(array $data): ProgressEntry
    {
        $boq = Boq::with('progressEntries')->findOrFail($data['boq_id']);

        $currentTotal = $boq->progressEntries->sum('actual_qty');
        $newTotal = $currentTotal + $data['actual_qty'];

        if ($newTotal > $boq->budget_qty) {
            throw ValidationException::withMessages([
                'actual_qty' => 'Total progress BOQ melebihi budget quantity'
            ]);
        }

        return ProgressEntry::create($data);
    }

    public function update(ProgressEntry $progressEntry, float $actualQty): ProgressEntry
    {
        $boq = $progressEntry->boq()->with('progressEntries')->first();

        $currentTotal = $boq->progressEntries
            ->where('id', '!=', $progressEntry->id)
            ->sum('actual_qty');

        $newTotal = $currentTotal + $actualQty;

        if ($newTotal > $boq->budget_qty) {
            throw ValidationException::withMessages([
                'actual_qty' => 'Total progress BOQ melebihi budget quantity'
            ]);
        }

        $progressEntry->update([
            'actual_qty' => $actualQty
        ]);

        return $progressEntry;
    }
}
