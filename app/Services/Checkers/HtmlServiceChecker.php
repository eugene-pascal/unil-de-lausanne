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
                    if (!empty($serviceConfig['check']['function']) && method_exists($this, $serviceConfig['check']['function'])) {
                        $result = $this->{$serviceConfig['check']['function']}($body);
                        $returnData['status'] = $result['status'];
                        $returnData['error'][] = $result['message'] ?? '';
                    } else {
                        $contains = str_contains($body, $serviceConfig['check']['search']);
                        $expected = $serviceConfig['check']['success'];
                        $returnData['status'] = ($contains === $expected)
                            ? ServiceStatusEnum::FUNCTIONAL
                            : ServiceStatusEnum::NON_FUNCTIONAL;

                        if ($returnData['status'] === ServiceStatusEnum::NON_FUNCTIONAL) {
                            $returnData['error'][] = $serviceConfig['check']['errorMessage'] ?? '';
                        }
                    }
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

    /**
     * Use to initialize only one html service
     */
    private function detectServiceRecorder($body): array
    {
        preg_match_all('/<th>(.*?)<\/th>/', $body, $matchesTh);
        if (!empty($matchesTh[1]) && count($matchesTh[1]) === 4) {
            array_shift($matchesTh[1]);
        }
        preg_match_all('/<td\s+class="([^"]*)">.*?<\/td>/', $body, $matches);

        $allServicesOnline = true;
        $offlineServiceName = [];
        foreach ($matches[1] as $iter=>$class) {
            if (trim($class) !== 'online') {
                $allServicesOnline = false;
                $offlineServiceName[] = $matchesTh[1][$iter] ?? '';
            }
        }

        $messageOnError = '';
        if (!empty($offlineServiceName)) {
            $messageOnError = sprintf("les éléments suivants ne sont pas actifs: %s", implode(', ', $offlineServiceName));
        }

        return [
            'status' => empty($offlineServiceName) ? ServiceStatusEnum::FUNCTIONAL : ServiceStatusEnum::PROBLEM_EXIST,
            'message' => $messageOnError
        ];
    }
}
