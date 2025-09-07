<?php

namespace App\Tests\Service;

use App\Service\VatCalculatorService;
use PHPUnit\Framework\TestCase;

class VatCalculatorServiceTest extends TestCase
{
    public function testCalculateWithValidInputs(): void
    {
        $calculator = new VatCalculatorService();
        $result = $calculator->calculate(100, 20);

        $this->assertEquals(100, $result['price']);
        $this->assertEquals(20, $result['rate']);
        $this->assertEquals(20, $result['vat']);
        $this->assertEquals(120, $result['total']);
    }

    public function testCalculateWithZeroRate(): void
    {
        $calculator = new VatCalculatorService();
        $result = $calculator->calculate(50, 0);

        $this->assertEquals(0, $result['vat']);
        $this->assertEquals(50, $result['total']);
    }

    public function testCalculateWithNegativeInputsThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $calculator = new VatCalculatorService();
        $calculator->calculate(-10, 20);
    }
}
