<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WorkPackage;
use App\Models\Boq;

class DashboardController extends Controller
{
    public function index()
    {
        $project = Project::with('workPackages.boqs.progressEntries')->first();

        $projectTotalAmount = 0;
        $projectWeightedProgress = 0;

        $wpData = [];
        $boqData = [];

        foreach ($project->workPackages as $wp) {

            $wpTotalAmount = 0;
            $wpWeightedProgress = 0;

            foreach ($wp->boqs as $boq) {

                $amount = $boq->budget_qty * $boq->unit_rate;
                $actualQty = $boq->progressEntries->sum('actual_qty');
                $progress = min(($actualQty / $boq->budget_qty) * 100, 100);

                $wpTotalAmount += $amount;
                $wpWeightedProgress += ($progress / 100) * $amount;

                $boqData[] = [
                    'boq_code'   => $boq->boq_code,
                    'budget_qty' => $boq->budget_qty,
                    'actual_qty' => $actualQty,
                    'progress'  => round($progress, 2),
                    'amount'    => $amount
                ];
            }

            $wpProgress = $wpTotalAmount > 0
                ? ($wpWeightedProgress / $wpTotalAmount) * 100
                : 0;

            $projectTotalAmount += $wpTotalAmount;
            $projectWeightedProgress += ($wpProgress / 100) * $wpTotalAmount;

            $wpData[] = [
                'wp_code'      => $wp->wp_code,
                'total_amount' => $wpTotalAmount,
                'progress'    => round($wpProgress, 2)
            ];
        }

        $projectProgress = $projectTotalAmount > 0
            ? ($projectWeightedProgress / $projectTotalAmount) * 100
            : 0;

        return response()->json([
            'data' => [
                'project' => [
                    'project_code' => $project->project_code,
                    'total_amount' => $projectTotalAmount,
                    'progress'     => round($projectProgress, 2)
                ],
                'work_packages' => $wpData,
                'boqs' => $boqData
            ]
        ]);
    }
}