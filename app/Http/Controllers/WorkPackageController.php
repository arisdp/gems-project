<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkPackage;
use Illuminate\Http\Request;

class WorkPackageController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => WorkPackage::with('project')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wp_code' => 'required',
            'wp_name' => 'required',
            'discipline_code' => 'required',
        ]);

        $wp = WorkPackage::create($data);

        return response()->json([
            'message' => 'Created',
            'data' => $wp->load('project')
        ]);
    }

    public function update(Request $request, WorkPackage $workPackage)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wp_code' => 'required',
            'wp_name' => 'required',
            'discipline_code' => 'required',
        ]);

        $workPackage->update($data);

        return response()->json([
            'message' => 'Updated',
            'data' => $workPackage->load('project')
        ]);
    }

    public function destroy(WorkPackage $workPackage)
    {
        $workPackage->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
