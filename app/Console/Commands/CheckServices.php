<?php

namespace App\Console\Commands;

use App\Enums\ServiceStatusEnum;
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
        }
    }
}
