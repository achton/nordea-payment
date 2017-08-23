<?php

namespace NordeaPayment\Tests\Money;

use NordeaPayment\Money;
use NordeaPayment\Tests\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @covers \NordeaPayment\Money\Money::format
     */
    public function testFormatWithDecimals()
    {
        $zero = new Money\DKK(0);
        $this->assertEquals('0.00', $zero->format());

        $money = new Money\DKK(1234567);
        $this->assertEquals('12345.67', $money->format());

        $money = new Money\DKK(-1234567);
        $this->assertEquals('-12345.67', $money->format());

        $money = new Money\DKK(-2);
        $this->assertEquals('-0.02', $money->format());
    }

    /**
     * @covers \NordeaPayment\Money\Money::format
     */
    public function testFormatWithoutDecimals()
    {
        $zero = new Money\JPY(0);
        $this->assertEquals('0', $zero->format());

        $money = new Money\JPY(123);
        $this->assertEquals('123', $money->format());

        $money = new Money\JPY(-1123);
        $this->assertEquals('-1123', $money->format());
    }

    /**
     * @covers \NordeaPayment\Money\Money::getAmount
     */
    public function testGetAmount()
    {
        $instance = new Money\DKK(345);
        $this->assertEquals(345, $instance->getAmount());

        $instance = new Money\DKK(-345);
        $this->assertEquals(-345, $instance->getAmount());

        $instance = new Money\DKK(0);
        $this->assertEquals(0, $instance->getAmount());
    }

    /**
     * @covers \NordeaPayment\Money\Money::equals
     */
    public function testEquals()
    {
        $instance = new Money\DKK(-451);

        $this->assertTrue($instance->equals($instance));
        $this->assertTrue($instance->equals(new Money\DKK(-451)));

        $this->assertFalse($instance->equals(false));
        $this->assertFalse($instance->equals(null));
        $this->assertFalse($instance->equals(new \stdClass()));
        $this->assertFalse($instance->equals(new Money\EUR(-451)));
        $this->assertFalse($instance->equals(new Money\DKK(-41)));
    }

    /**
     * @dataProvider validSamplePairs
     * @covers \NordeaPayment\Money\Money::plus
     * @covers \NordeaPayment\Money\Money::minus
     * @covers \NordeaPayment\Money\Money::compareTo
     */
    public function testBinaryOperands($a, $b, $expectedSum, $expectedDiff, $expectedComparison)
    {
        $this->assertTrue($expectedSum->equals($a->plus($b)));
        $this->assertTrue($expectedDiff->equals($a->minus($b)));
        $this->assertEquals($expectedComparison, $a->compareTo($b));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \NordeaPayment\Money\Money::plus
     */
    public function testInvalidPlus($a, $b)
    {
        $a->plus($b);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \NordeaPayment\Money\Money::minus
     */
    public function testInvalidMinus($a, $b)
    {
        $a->minus($b);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidSamplePairs
     * @covers \NordeaPayment\Money\Money::minus
     */
    public function testInvalidCompareTo($a, $b)
    {
        $a->compareTo($b);
    }

    public function validSamplePairs()
    {
        return array(
            array(new Money\DKK(17400), new Money\DKK(19635), new Money\DKK(37035), new Money\DKK(-2235), -1),
            array(new Money\DKK(17400), new Money\DKK(4391), new Money\DKK(21791), new Money\DKK(13009), 1),
            array(new Money\DKK(400), new Money\DKK(-400), new Money\DKK(0), new Money\DKK(800), 1),
            array(new Money\DKK(400), new Money\DKK(400), new Money\DKK(800), new Money\DKK(0), 0),
        );
    }

    public function invalidSamplePairs()
    {
        return array(
            array(new Money\DKK(17400), new Money\EUR(19635)),
        );
    }
}
