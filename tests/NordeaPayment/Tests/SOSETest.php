<?php

namespace NordeaPayment\Tests;

use NordeaPayment\SOSE;

class SOSETest extends TestCase
{
    /**
     * @dataProvider samplesValid
     * @covers \NordeaPayment\SOSE::__construct
     */
    public function testValid($sose)
    {
        $this->check($sose, true);
    }

    /**
     * @dataProvider invalidSamples
     * @covers \NordeaPayment\SOSE::__construct
     */
    public function testInvalid($sose)
    {
        $this->check($sose, false);
    }

    /**
     * @dataProvider samplesValid
     * @covers \NordeaPayment\SOSE::__toString
     */
    public function testToString($sose)
    {
        $instance = new SOSE($sose);
        $this->assertEquals($instance->format(), (string) $instance);
    }

    public function samplesValid()
    {
        return [
            ['0101701234'],
            ['1010804567'],
            ['2412561569'],
        ];
    }

    public function invalidSamples()
    {
        return [
            ['2413001234'], // invalid date
            ['123456789'], // 9 digits
            ['3201794241'], // invalid date
            ['ABC12345678'], // letters
            ['121086-1274'], // dash included (11 chars)
            ['121086-127'], // 10 chars, but dash included
        ];
    }

    protected function check($sose, $valid)
    {
        $exception = false;
        try {
            $temp = new SOSE($sose);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
