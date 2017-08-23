<?php

namespace NordeaPayment\Tests\Money;

use NordeaPayment\Money;
use NordeaPayment\Tests\TestCase;

class MixedTest extends TestCase
{
    /**
     * @covers \NordeaPayment\Money\Mixed::plus
     */
    public function testPlus()
    {
        $sum = new Money\Mixed(0);
        $sum = $sum->plus(new Money\DKK(2456));
        $sum = $sum->plus(new Money\DKK(1000));
        $sum = $sum->plus(new Money\JPY(1200));

        $this->assertEquals('1234.56', $sum->format());
    }

    /**
     * @covers \NordeaPayment\Money\Mixed::minus
     */
    public function testMinus()
    {
        $sum = new Money\Mixed(100);
        $sum = $sum->minus(new Money\DKK(5000));
        $sum = $sum->minus(new Money\DKK(99));
        $sum = $sum->minus(new Money\JPY(300));

        $this->assertEquals('-250.99', $sum->format());
    }
}
