<?php

namespace App\Services;

use NumberFormatter;

class MoneyService
{
    protected NumberFormatter $spellFormatter;
    protected NumberFormatter $numberFormatter;

    public function __construct()
    {
        $this->spellFormatter = new NumberFormatter('fr_FR', NumberFormatter::SPELLOUT);
        $this->numberFormatter = new NumberFormatter('fr_FR', NumberFormatter::DECIMAL);
        $this->numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
    }

    public function toWords(float|int|null|string $amount, string $currency = 'CDF'): string
    {
        // Si le montant est null ou vide, on retourne une chaîne par défaut
        if ($amount === null || $amount === '') {
            return "0,00 $currency";
        }

        $amount = round($amount, 2);

        $integerPart = floor($amount);
        $decimalPart = round(($amount - $integerPart) * 100);

        $words = ucfirst($this->spellFormatter->format($integerPart));

        $currencyLabel = $this->currencyLabel($currency, $integerPart);

        $result = "$words $currencyLabel";

        if ($decimalPart > 0) {
            $decimalWords = $this->spellFormatter->format($decimalPart);
            $result .= " et $decimalWords centimes";
        }

        return $result;
    }

    public function format(float|int|null|string $amount, string $currency = 'CDF'): string
    {
        // Si le montant est null ou vide, on retourne une chaîne par défaut
        if ($amount === null || $amount === '') {
            return "0,00 $currency";
        }

        $formatted = $this->numberFormatter->format($amount);
        return "$formatted $currency";
    }

    // app/Services/MoneyService.php
    public function formatWithWords(float|int|null|string $amount, string $currency = 'CDF'): string
    {
        // Si le montant est null ou vide, on retourne une chaîne par défaut
        if ($amount === null || $amount === '') {
            return "0,00 $currency";
        }

        return $this->format($amount, $currency) .
            " (" . $this->toWords($amount, $currency) . ")";
    }

    protected function currencyLabel(string $currency, int $amount): string
    {
        return match ($currency) {
            'CDF' => $amount > 1 ? 'francs congolais' : 'franc congolais',
            'USD' => $amount > 1 ? 'dollars américains' : 'dollar américain',
            default => $currency,
        };
    }
}