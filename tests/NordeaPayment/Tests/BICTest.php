<?php

namespace NordeaPayment\Tests;

use NordeaPayment\BIC;

class BICTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers \NordeaPayment\BIC::__construct
     */
    public function testValid($bic)
    {
        $this->check($bic, true);
    }

    /**
     * @covers \NordeaPayment\BIC::__construct
     */
    public function testInvalidLength()
    {
        $this->check('AABAFI22F', false);
        $this->check('HANDFIHH00', false);
    }

    /**
     * @covers \NordeaPayment\BIC::__construct
     */
    public function testInvalidChars()
    {
        $this->check('HAND-FIHH', false);
        $this->check('HAND FIHH', false);
    }

    /**
     * @dataProvider validSamples
     * @covers \NordeaPayment\BIC::format
     */
    public function testFormat($bic)
    {
        $instance = new BIC($bic);
        $this->assertEquals($bic, $instance->format());
    }

    public function validSamples()
    {
        return array(
            array('AABAFI22'),
            array('HANDFIHH'),
            array('DEUTDEFF500'),
        );
    }

    protected function check($iban, $valid)
    {
        $exception = false;
        try {
            $temp = new BIC($iban);
        } catch (\InvalidArgumentException $e) {
            $exception = true;
        }
        $this->assertTrue($exception != $valid);
    }
}
