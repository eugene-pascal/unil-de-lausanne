<?php

return [
    'services' => [
        'WordPress' => [
            'url' => 'https://sepia2.unil.ch/wp/',
            'type' => 'html',
        ],

        'BookStack' => [
            'url' => 'https://wiki.unil.ch/',
            'type' => 'html',
        ],

        'Compilatio' => [
            'url' => 'https://app.compilatio.net/api/public/alerts',
            'type' => 'json',
            'check' => [
                'param' =>'status.message',
                'value' => 'OK',
                'error' => 'data.alerts'
            ]
        ],

        'MediaServer' => [
            'url' => 'https://rec.unil.ch/api/v2/info/',
            'type' => 'json',
            'check' => [
                'param' =>'success',
                'value' => true
            ]
        ],

        'Recorder' => [
            'url' => 'https://cse.unil.ch/miris/?q=POL-A',
            'type' => 'html',
        ],

        'SOAPService' => [
            'url' => 'https://cse.unil.ch/soapws/soap_server.php',
            'type' => 'soap',
            'token' => 'hJ89sdf83hf02j1MsdKf02JhQp91xZ',
            'check' => [
                'param' =>'success',
                'value' => true,
                'error' => 'current_issues'
            ]
        ],
    ],
];

