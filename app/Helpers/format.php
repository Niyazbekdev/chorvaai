<?php

if (! function_exists('fmt_sum')) {
    function fmt_sum(int|float|null $amount): string
    {
        if (!$amount) {
            return "0 so'm";
        }
        if ($amount >= 1_000_000_000) {
            return number_format($amount / 1_000_000_000, 1) . " mlrd so'm";
        }
        if ($amount >= 1_000_000) {
            return number_format($amount / 1_000_000, 1) . " mln so'm";
        }
        return number_format($amount, 0, '.', ' ') . " so'm";
    }
}
