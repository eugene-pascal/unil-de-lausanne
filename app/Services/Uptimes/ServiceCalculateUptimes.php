<?php

namespace App\Services\Uptimes;

use App\Models\ServiceStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ServiceCalculateUptimes
{
    /**
     * Retrieves the current status of each distinct service.
     */
    public function getCurrentServiceStatuses(): Collection
    {
        return ServiceStatus::select('service_name')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($service) {
                $latest = ServiceStatus::where('service_name', $service->service_name)
                    ->orderByDesc('checked_at')
                    ->first();

                return [$service->service_name => $latest->status ?? 'unknown'];
            });
    }

    /**
     * Calculates the uptime percentage for each service over a specified period.
     */
    public function calculateServiceUptimePercentage(int $hours = 24): array
    {
        $since = Carbon::now()->subHours($hours);

        $services = ServiceStatus::select('service_name')
            ->distinct()
            ->pluck('service_name');

        $result = [];

        foreach ($services as $service) {
            $total = ServiceStatus::where('service_name', $service)
                ->where('checked_at', '>=', $since)
                ->count();

            $functional = ServiceStatus::where('service_name', $service)
                ->where('checked_at', '>=', $since)
                ->where('status', 'functional')
                ->count();

            $result[$service] = $total > 0 ? round(($functional / $total) * 100, 2) : 0;
        }

        return $result;
    }

    /**
     * Computes the overall uptime percentage across all services over a specified period.
     */
    public function calculateGlobalUptimePercentage(int $hours = 24): float
    {
        $since = Carbon::now()->subHours($hours);

        $total = ServiceStatus::where('checked_at', '>=', $since)->count();

        $functional = ServiceStatus::where('checked_at', '>=', $since)
            ->where('status', 'functional')
            ->count();

        return $total > 0 ? round(($functional / $total) * 100, 2) : 0;
    }
}
