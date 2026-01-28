<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use Illuminate\Http\Request;

class BoqController extends Controller
{
    public function index()
    {
        return Boq::with('workPackage', 'progressEntries')->get()
            ->map(function ($boq) {
                return [
                    'id' => $boq->id,
                    'boq_code' => $boq->boq_code,
                    'description' => $boq->description,
                    'uom' => $boq->uom,
                    'budget_qty' => (float) $boq->budget_qty,
                    'actual_qty' => $boq->actual_qty,
                    'progress_percent' => $boq->progress,
                    'unit_rate' => (float) $boq->unit_rate,
                    'amount' => $boq->amount,
                    'work_package' => $boq->workPackage->wp_code
                ];
            });
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'work_package_id' => 'required|exists:work_packages,id',
            'boq_code' => 'required',
            'description' => 'required',
            'uom' => 'required',
            'budget_qty' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0'
        ]);

        return Boq::create($data);
    }

    public function show(Boq $boq)
    {
        return $boq->load('progressEntries');
    }

    public function update(Request $request, Boq $boq)
    {
        $data = $request->validate([
            'boq_code' => 'required',
            'description' => 'required',
            'uom' => 'required',
            'budget_qty' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0'
        ]);

        $boq->update($data);
        return $boq;
    }

    public function destroy(Boq $boq)
    {
        $boq->delete();
        return response()->json(['message' => 'BOQ deleted']);
    }
}
