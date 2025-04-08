<?php

namespace App\Services\Checkers;

use App\Enums\ServiceStatusEnum;

class SoapServiceChecker implements ServiceCheckerInterface
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
            $client = new \SoapClient(null, [
                'location'   => $serviceConfig['url'],
                'uri'        => 'urn://service-status',
                'trace'      => true,
                'exceptions' => true,
                'soap_version' => SOAP_1_1,
                'cache_wsdl' => WSDL_CACHE_NONE
            ]);

            $response = $client->__soapCall('getStatus', [
                $serviceConfig['token'] ?? ''
            ]);

            $returnData['status'] = (isset($response['success']) && $response['success']) ?
                ServiceStatusEnum::FUNCTIONAL : ServiceStatusEnum::NON_FUNCTIONAL;

            $returnData['full_response'] = $response;
            $resultOnSuccess = data_get($returnData['full_response'], $serviceConfig['check']['param'], '');
            if ($resultOnSuccess != $serviceConfig['check']['value']) {
                $returnData['status'] = ServiceStatusEnum::NON_FUNCTIONAL;
            }
            if (!empty($serviceConfig['check']['error'])) {
                $errorArr = data_get($returnData['full_response'], $serviceConfig['check']['error'], []);
                if (!empty($errorArr)) {
                    $returnData['error'] = $errorArr;
                }
            }

        } catch (\Throwable $e) {
            $returnData['status'] = ServiceStatusEnum::NON_FUNCTIONAL;
            $returnData['error'][] = 'Exception: ' . $e->getMessage();
        }
        return $returnData;
    }
}
