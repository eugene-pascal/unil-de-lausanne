<?php

namespace App\Console\Commands;

use App\Enums\ServiceStatusEnum;
use App\Models\ServiceFailure;
use App\Services\Checkers\ServiceCheckerFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ServiceStatus;

class CheckServices extends Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected $signature = 'services:check';

    /**
     * Description command
     *
     * @var string
     */
    protected $description = 'Check all services on work';

    /**
     * Exec command
     */
    public function handle()
    {
        $services = config('services_status.services');
        foreach ($services as $key => $service) {
            $checker = ServiceCheckerFactory::make($service);
            $status = $checker->check($service);

            $this->info("✔ {$key}::{$service['type']}: {$status['status']->value}");

            ServiceStatus::create([
                'service_name'  => $key,
                'type'          => $service['type'] ?? ''   ,
                'status'        => $status['status']->value,
                'full_response' => $status['full_response'] ?? null,
                'issues'        => $status['error'] ?? null,
                'extra'         => null,
                'checked_at'    => Carbon::now(),
            ]);

            if ($status['status'] !== ServiceStatusEnum::FUNCTIONAL) {
                // if we have already created record then pass it
                $existingFailure = ServiceFailure::where('service_name', $key)
                    ->whereNull('ended_at')
                    ->first();

                if (!$existingFailure) {
                    ServiceFailure::create([
                        'service_name'     => $key,
                        'type'             => $service['type'],
                        'started_at'       => now(),
                        'ended_at'         => null,
                        'duration_seconds' => null,
                    ]);
                    $this->warn("Issue is fixed for {$key}");
                }

            } else {
                // if we have open record then close it
                $openFailure = ServiceFailure::where('service_name', $key)
                    ->whereNull('ended_at')
                    ->first();

                if ($openFailure) {
                    $end = now();
                    $duration = $end->diffInSeconds($openFailure->started_at);

                    $openFailure->update([
                        'ended_at'         => $end,
                        'duration_seconds' => $duration,
                    ]);

                    $this->info("Issue was closed for {$key} (duration: " . $openFailure->getDurationFormattedAttr() . ")");
                }
            }
        }
    }
}
