<?php

namespace Tests\Feature;

use Tests\TestCase;

class SoapServiceTest extends TestCase
{
    public function testSoapServiceConnection()
    {
        $url = config('services_status.services.SOAPService.url');
        $token = config('services_status.services.SOAPService.token');

        try {
            $client = new \SoapClient(null, [
                'location' => $url,
                'uri' => 'urn://service',
                'trace' => true,
                'exceptions' => true,
                'soap_version' => SOAP_1_1,
                'cache_wsdl' => WSDL_CACHE_NONE
            ]);

//            $authVars = new \SoapVar(
//                ['token' => $token],
//                SOAP_ENC_OBJECT
//            );
//
//            $header = new \SoapHeader('urn://service', 'Authentication', $authVars);
//            $client->__setSoapHeaders([$header]);

            $response = $client->__soapCall('getStatus', [
                $token
            ]);

            fwrite(STDOUT, "-> SOAP response:\n" . print_r($response, true) . "\n\n[success] = ". (isset($response['success']) ? ($response['success'] ? 'True' : 'False' ) : '' ));
            $this->assertNotNull($response);
            $this->assertArrayHasKey('success', $response);
            $this->assertArrayHasKey('current_issues', $response);

        } catch (\SoapFault $e) {
            $this->fail("SOAP error: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->fail("General error: " . $e->getMessage());
        }
    }
}
