<?php

namespace App\Enums;

enum ServiceStatusEnum: string
{
    case FUNCTIONAL = 'functional';
    case NON_FUNCTIONAL = 'non-functional';
    case PROBLEM_EXIST = 'problem-exist';

    public static function allValue(): array
    {
        return [
            self::FUNCTIONAL->value,
            self::NON_FUNCTIONAL->value,
            self::PROBLEM_EXIST->value,
        ];
    }
}
