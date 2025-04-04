<?php

namespace App\Services\Checkers;

interface ServiceCheckerInterface
{
    public function check(array $serviceConfig): array;
}
