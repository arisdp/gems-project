<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::with('workPackages')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects',
            'project_name' => 'required'
        ]);

        return Project::create($data);
    }

    public function show(Project $project)
    {
        return $project->load('workPackages.boqs.progressEntries');
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'project_code' => 'required|unique:projects,project_code,' . $project->id,
            'project_name' => 'required'
        ]);

        $project->update($data);
        return $project;
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
