<?php

namespace App\Services\Checkers;

class ServiceCheckerFactory
{
    public static function make(array $serviceConfig): ServiceCheckerInterface
    {
        return match ($serviceConfig['type']) {
            'json' => new JsonServiceChecker(),
            'html' => new HtmlServiceChecker(),
            'soap' => new SoapServiceChecker(),
            default => throw new \InvalidArgumentException("Unknown type: {$serviceConfig['type']}")
        };
    }
}
