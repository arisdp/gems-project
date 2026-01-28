<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkPackage;
use Illuminate\Http\Request;

class WorkPackageController extends Controller
{
    public function index()
    {
        return WorkPackage::with('project')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wp_code' => 'required',
            'wp_name' => 'required',
            'discipline_code' => 'required'
        ]);

        return WorkPackage::create($request->all());
    }
}
