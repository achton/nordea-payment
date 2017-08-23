<?php

namespace NordeaPayment\Tests;

use InvalidArgumentException;
use NordeaPayment\GeneralAccount;

class GeneralAccountTest extends TestCase
{
    /**
     * @covers \NordeaPayment\GeneralAccount::__construct
     */
    public function testValid()
    {
        $instance = new GeneralAccount('A-123-4567890-78');
    }

    /**
     * @covers \NordeaPayment\GeneralAccount::__construct
     * @expectedException InvalidArgumentException
     */
    public function testInvalid()
    {
        $instance = new GeneralAccount('0123456789012345678901234567890123456789');
    }

    /**
     * @covers \NordeaPayment\GeneralAccount::format
     */
    public function testFormat()
    {
        $instance = new GeneralAccount('  123-4567890-78 AA ');
        $this->assertSame('  123-4567890-78 AA ', $instance->format());
    }
}
