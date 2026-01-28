<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Project::orderBy('id', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects',
            'project_name' => 'required'
        ]);

        $project = Project::create($data);

        return response()->json([
            'message' => 'Project created',
            'data' => $project
        ], 201);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects,project_code,' . $project->id,
            'project_name' => 'required'
        ]);

        $project->update($data);

        return response()->json([
            'message' => 'Project updated',
            'data' => $project
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted',
            'id' => $project->id
        ]);
    }
}
