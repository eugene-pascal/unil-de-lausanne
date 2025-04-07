<?php

namespace App\Services\Checkers;

use App\Enums\ServiceStatusEnum;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class HtmlServiceChecker implements ServiceCheckerInterface
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

            $body = $response->body();
            $returnData['code'] = $response->status();

            if ($response->ok()) {
                $returnData['status'] = ServiceStatusEnum::FUNCTIONAL;
                if (!empty($serviceConfig['check'])&&is_array($serviceConfig['check'])) {
                    $contains = str_contains($body, $serviceConfig['check']['search']);
                    $expected = $serviceConfig['check']['success'];
                    $returnData['status'] = ($contains === $expected)
                        ? ServiceStatusEnum::FUNCTIONAL
                        : ServiceStatusEnum::NON_FUNCTIONAL;
                }
            } else {
                $returnData['status'] = ServiceStatusEnum::NON_FUNCTIONAL;
            }

        } catch (RequestException $e) {
            $returnData['error'][] = $e->getMessage();
        } catch (\Throwable $e) {
            $returnData['error'][] = 'Unexpected error: ' . $e->getMessage();
        }
        return $returnData;
    }
}
