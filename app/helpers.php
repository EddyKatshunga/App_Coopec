<?php

use App\Services\MoneyService;

if (! function_exists('money_to_words')) {
    function money_to_words($amount, $currency = 'CDF')
    {
        return app(MoneyService::class)->toWords($amount, $currency);
    }
}

if (! function_exists('money_format_bank')) {
    function money_format_bank($amount, $currency = 'CDF')
    {
        return app(MoneyService::class)->formatWithWords($amount, $currency);
    }
}