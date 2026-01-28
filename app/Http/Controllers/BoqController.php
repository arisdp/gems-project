<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boq;
use Illuminate\Http\Request;

class BoqController extends Controller
{
    public function index()
    {
        return Boq::with('workPackage')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_package_id' => 'required|exists:work_packages,id',
            'boq_code' => 'required',
            'description' => 'required',
            'uom' => 'required',
            'budget_qty' => 'required|numeric|min:0',
            'unit_rate' => 'required|numeric|min:0'
        ]);

        return Boq::create($request->all());
    }
}
