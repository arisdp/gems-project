<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\WorkPackage;
use App\Models\Boq;
use App\Models\ProgressEntry;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ PROJECT
        $project = Project::create([
            'project_code' => 'PRJ-001',
            'project_name' => 'Sample Construction Project'
        ]);

        // 2️⃣ WORK PACKAGE
        $wp = WorkPackage::create([
            'project_id'     => $project->id,
            'wp_code'        => 'WP-CIV-001',
            'wp_name'        => 'Civil Work Package',
            'discipline_code' => 'Civil'
        ]);

        // 3️⃣ BOQ
        $boq1 = Boq::create([
            'work_package_id' => $wp->id,
            'boq_code'        => 'BOQ-001',
            'description'     => 'Excavation',
            'uom'             => 'm3',
            'budget_qty'      => 100,
            'unit_rate'       => 50
        ]);

        $boq2 = Boq::create([
            'work_package_id' => $wp->id,
            'boq_code'        => 'BOQ-002',
            'description'     => 'Concrete',
            'uom'             => 'm3',
            'budget_qty'      => 50,
            'unit_rate'       => 200
        ]);

        // 4️⃣ PROGRESS ENTRY
        ProgressEntry::create([
            'boq_id'        => $boq1->id,
            'progress_date' => '2026-01-10',
            'actual_qty'   => 40
        ]);

        ProgressEntry::create([
            'boq_id'        => $boq2->id,
            'progress_date' => '2026-01-15',
            'actual_qty'   => 10
        ]);
    }
}
