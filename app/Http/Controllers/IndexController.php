<?php

namespace App\Http\Controllers;

use App\Models\ServiceFailure;
use App\Services\Uptimes\ServiceCalculateUptimes;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function home(ServiceCalculateUptimes $uptimeService)
    {
        $latestFailures = ServiceFailure::orderByDesc('started_at')
            ->limit(5)
            ->get();

        return view('home', [
            'currentStatuses' => $uptimeService->getCurrentServiceStatuses(),
            'uptimes24h'      => $uptimeService->calculateServiceUptimePercentage(24),
            'global24h'       => $uptimeService->calculateGlobalUptimePercentage(24),
            'global7d'        => $uptimeService->calculateGlobalUptimePercentage(24 * 7),
            'latestFailures'  => $latestFailures
        ]);
    }
}

