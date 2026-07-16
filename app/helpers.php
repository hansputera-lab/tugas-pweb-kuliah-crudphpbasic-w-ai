<?php

if (!function_exists('currency')) {
    function currency(float|string|int|null $amount, int $decimals = 0): string
    {
        if (is_null($amount)) {
            $amount = 0;
        }
        return 'Rp ' . number_format((float) $amount, $decimals, ',', '.');
    }
}
