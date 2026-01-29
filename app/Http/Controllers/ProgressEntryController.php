<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{ProgressEntry, Boq};
use App\Services\BoqProgressService;
use Illuminate\Http\Request;

class ProgressEntryController extends Controller
{

    public function index()
    {
        return response()->json([
            'data' => ProgressEntry::with('boq')
                ->orderBy('progress_date')
                ->get()
        ]);
    }


    public function store(Request $request, BoqProgressService $service)
    {
        $data = $request->validate([
            'boq_id' => 'required|exists:boqs,id',
            'progress_date' => 'required|date',
            'actual_qty' => 'required|numeric|min:0'
        ]);

        $entry = $service->store($data);

        return response()->json($entry, 201);
    }

    public function update(
        Request $request,
        ProgressEntry $progressEntry,
        BoqProgressService $service
    ) {
        $data = $request->validate([
            'actual_qty' => 'required|numeric|min:0'
        ]);

        $service->update($progressEntry, $data['actual_qty']);

        return response()->json($progressEntry);
    }


    public function destroy($id)
    {
        ProgressEntry::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Progress deleted'
        ]);
    }
}
