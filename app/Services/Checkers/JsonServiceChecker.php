<?php

namespace App\Services\Checkers;

use App\Enums\ServiceStatusEnum;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class JsonServiceChecker implements ServiceCheckerInterface
{
    public function check(array $serviceConfig): array
    {
        $returnData = [
            'status' => ServiceStatusEnum::NON_FUNCTIONAL,
            'code' => 0,
            'full_response' => null,
            'error' => [],
        ];

        try {
            $response = Http::timeout(5)->get($serviceConfig['url']);

            $returnData['status'] = $response->ok() ? ServiceStatusEnum::FUNCTIONAL : ServiceStatusEnum::NON_FUNCTIONAL;
            $returnData['code'] = $response->status();
            $returnData['full_response'] = $response->json();

            $resultOnSuccess = data_get($returnData['full_response'], $serviceConfig['check']['param'], 'NONE');
            if ($resultOnSuccess != $serviceConfig['check']['value']) {
                $returnData['status'] = ServiceStatusEnum::NON_FUNCTIONAL;
                if (!empty($serviceConfig['check']['error'])) {
                    $returnData['error'] = data_get($returnData['full_response'], $serviceConfig['check']['error'], []);
                }
            }
        }  catch (RequestException $e) {
            $returnData['error'][] = $e->getMessage();
        } catch (\Throwable $e) {
            $returnData['error'][] = 'Unexpected error: ' . $e->getMessage();
        }
        return $returnData;
    }
}
