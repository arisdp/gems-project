<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use App\Models\ProgressEntry;
use Illuminate\Http\Request;

class ProgressEntryController extends Controller
{
    public function index()
    {
        return ProgressEntry::with('boq')->orderBy('progress_date')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'boq_id' => 'required|exists:boqs,id',
            'progress_date' => 'required|date',
            'actual_qty' => 'required|numeric|min:0'
        ]);

        $boq = Boq::with('progressEntries')->findOrFail($data['boq_id']);

        $totalActual = $boq->actual_qty + $data['actual_qty'];

        // ❗ VALIDASI WAJIB
        if ($totalActual > $boq->budget_qty) {
            return response()->json([
                'message' => 'Actual quantity exceeds BOQ budget'
            ], 422);
        }

        return ProgressEntry::create($data);
    }

    public function destroy(ProgressEntry $progressEntry)
    {
        $progressEntry->delete();
        return response()->json(['message' => 'Progress deleted']);
    }
}
