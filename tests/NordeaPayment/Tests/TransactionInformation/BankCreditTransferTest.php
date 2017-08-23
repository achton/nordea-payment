<?php

namespace NordeaPayment\Tests\TransactionInformation;

use NordeaPayment\BIC;
use NordeaPayment\BBAN;
use NordeaPayment\Money;
use NordeaPayment\StructuredPostalAddress;
use NordeaPayment\Tests\TestCase;
use NordeaPayment\TransactionInformation\BankCreditTransfer;

/**
 * @coversDefaultClass \NordeaPayment\TransactionInformation\BankCreditTransfer
 */
class BankCreditTransferTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCreditorAgent()
    {
        $creditorAgent = $this->createMock('\NordeaPayment\FinancialInstitutionInterface');

        $transfer = new BankCreditTransfer(
            'name',
            new Money\DKK(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new BBAN('1234', '1234567890'),
            $creditorAgent
        );
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAmount()
    {
        $transfer = new BankCreditTransfer(
            'name',
            new Money\USD(100),
            'name',
            new StructuredPostalAddress('foo', '99', '9999', 'bar'),
            new BBAN('1234', '1234567890'),
            new BIC('NDEADKKK')
        );
    }
}
