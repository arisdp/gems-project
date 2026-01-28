<?php

namespace App\Http\Controllers;

use App\Models\WorkPackage;
use Illuminate\Http\Request;

class WorkPackageController extends Controller
{
    public function index()
    {
        return WorkPackage::with('boqs.progressEntries')->get()
            ->map(function ($wp) {
                return [
                    'id' => $wp->id,
                    'wp_code' => $wp->wp_code,
                    'discipline' => $wp->discipline_code,
                    'total_boq_amount' => $wp->total_amount,
                    'progress_percent' => $wp->progress
                ];
            });
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wp_code' => 'required',
            'wp_name' => 'required',
            'discipline_code' => 'required'
        ]);

        return WorkPackage::create($data);
    }

    public function show(WorkPackage $workPackage)
    {
        return $workPackage->load('boqs.progressEntries');
    }

    public function update(Request $request, WorkPackage $workPackage)
    {
        $data = $request->validate([
            'wp_code' => 'required',
            'wp_name' => 'required',
            'discipline_code' => 'required'
        ]);

        $workPackage->update($data);
        return $workPackage;
    }

    public function destroy(WorkPackage $workPackage)
    {
        $workPackage->delete();
        return response()->json(['message' => 'Work Package deleted']);
    }
}
