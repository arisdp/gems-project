<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{ProgressEntry, Boq};
use Illuminate\Http\Request;

class ProgressEntryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'boq_id' => 'required|exists:boqs,id',
            'progress_date' => 'required|date',
            'actual_qty' => 'required|numeric|min:0'
        ]);

        $boq = Boq::withSum('progressEntries', 'actual_qty')->findOrFail($request->boq_id);
        $total = $boq->progress_entries_sum_actual_qty + $request->actual_qty;

        if ($total > $boq->budget_qty) {
            return response()->json([
                'message' => 'Progress melebihi Budget Qty'
            ], 422);
        }

        return ProgressEntry::create($request->all());
    }
}
