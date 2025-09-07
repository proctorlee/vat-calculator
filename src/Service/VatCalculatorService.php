<?php

namespace App\Service;

class VatCalculatorService
{
    public function calculate(float $price, float $rate): array
    {
        if ($price < 0 || $rate < 0) {
            throw new \InvalidArgumentException('Price and VAT rate must be non-negative.');
        }

        $vat = round($price * ($rate / 100), 2);
        $total = round($price + $vat, 2);

        return [
            'price' => $price,
            'rate' => $rate,
            'vat' => $vat,
            'total' => $total,
        ];
    }
}
