<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{ProgressEntry, Boq};
use Illuminate\Http\Request;

class ProgressEntryController extends Controller
{

    public function index($boqId)
    {
        return response()->json([
            'data' => ProgressEntry::where('boq_id', $boqId)
                ->orderBy('progress_date')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'boq_id'        => 'required|exists:boqs,id',
            'progress_date' => 'required|date',
            'actual_qty'   => 'required|numeric|min:0',
        ]);

        $boq = Boq::with('progressEntries')->findOrFail($validated['boq_id']);

        $totalActual =
            $boq->progressEntries->sum('actual_qty') +
            $validated['actual_qty'];

        // ❗ business rule: progress tidak boleh > 100%
        if ($totalActual > $boq->budget_qty) {
            return response()->json([
                'message' => 'Progress melebihi Budget Qty'
            ], 422);
        }

        $progress = ProgressEntry::create($validated);

        return response()->json([
            'data' => $progress
        ]);
    }

    public function update(Request $request, $id)
    {
        $progress = ProgressEntry::findOrFail($id);
        $boq = $progress->boq->load('progressEntries');

        $validated = $request->validate([
            'progress_date' => 'required|date',
            'actual_qty'   => 'required|numeric|min:0',
        ]);

        $totalActual =
            $boq->progressEntries
            ->where('id', '!=', $progress->id)
            ->sum('actual_qty')
            + $validated['actual_qty'];

        if ($totalActual > $boq->budget_qty) {
            return response()->json([
                'message' => 'Actual qty exceeds budget quantity'
            ], 422);
        }

        $progress->update($validated);

        return response()->json([
            'data' => $progress
        ]);
    }

    public function destroy($id)
    {
        ProgressEntry::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Progress deleted'
        ]);
    }
}
