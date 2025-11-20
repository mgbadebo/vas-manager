<?php

namespace App\Support;

class Money
{
    public static function format(mixed $value, int $decimals = 2): string
    {
        $numeric = is_numeric($value) ? (float) $value : 0.0;

        return '₦' . number_format($numeric, $decimals);
    }
}


