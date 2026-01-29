<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Boq;
use Illuminate\Http\Request;

class BoqController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Boq::with('workPackage')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_package_id' => 'required|exists:work_packages,id',
            'boq_code' => 'required|string',
            'description' => 'nullable|string',
            'uom' => 'required|string',
            'budget_qty' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0',
        ]);

        return Boq::create($validated);
    }


    public function update(Request $request, $id)
    {
        $boq = Boq::findOrFail($id);

        $validated = $request->validate([
            'work_package_id' => 'required|exists:work_packages,id',
            'boq_code'        => 'required|string|max:50',
            'description'     => 'required|string',
            'uom'             => 'required|string|max:20',
            'budget_qty'      => 'required|numeric|min:0',
            'unit_rate'       => 'required|numeric|min:0',
        ]);

        $boq->update($validated);

        return response()->json([
            'data' => $boq->load('workPackage')
        ]);
    }

    /* =========================================================
     * DELETE: BOQ
     * ========================================================= */
    public function destroy($id)
    {
        $boq = Boq::findOrFail($id);
        $boq->delete();

        return response()->json([
            'message' => 'BOQ deleted successfully'
        ]);
    }
}
