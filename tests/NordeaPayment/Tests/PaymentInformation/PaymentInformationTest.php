<?php

namespace NordeaPayment\Tests\PaymentInformation;

use DOMDocument;
use DOMXPath;
use NordeaPayment\BIC;
use NordeaPayment\BBAN;
use NordeaPayment\IBAN;
use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\CategoryPurposeCode;
use NordeaPayment\PaymentInformation\PaymentInformation;
use NordeaPayment\StructuredPostalAddress;
use NordeaPayment\Tests\TestCase;

/**
 * @coversDefaultClass \NordeaPayment\PaymentInformation\PaymentInformation
 */
class PaymentInformationTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDebtorAgent()
    {
        $debtorAgent = $this->createMock('\NordeaPayment\FinancialInstitutionInterface');

        $payment = new PaymentInformation(
            'id000',
            'name',
            $debtorAgent,
            new BBAN('1234', '1234567890')
        );
    }

    /**
     * @covers ::hasPaymentTypeInformation
     */
    public function testHasPaymentTypeInformation()
    {
        $payment = new PaymentInformation(
            'id000',
            'name',
            new BIC('NDEADKKK'),
            new BBAN('1234', '1234567890')
        );

        $this->assertFalse($payment->hasPaymentTypeInformation());
    }

    /**
     * @covers ::asDom
     */
    public function testInfersPaymentInformation()
    {
        $doc = new DOMDocument();
        $payment = new PaymentInformation(
            'id000',
            'name',
            new BIC('NDEADKKK'),
            new IBAN('CH31 8123 9000 0012 4568 9')
        );
        $payment->setCategoryPurpose(new CategoryPurposeCode('NURG'));
/*
        $payment->addTransaction(new IS1CreditTransfer(
            'instr-001',
            'e2e-001',
            new Money\DKK(10000), // DKK 100.00
            'Fritz Bischof',
            new StructuredPostalAddress('Dorfstrasse', '17', '9911', 'Musterwald'),
            new PostalAccount('60-9-9')
        ));
        $payment->addTransaction(new IS1CreditTransfer(
            'instr-002',
            'e2e-002',
            new Money\DKK(30000), // DKK 300.00
            'Franziska Meier',
            new StructuredPostalAddress('Altstadt', '1a', '4998', 'Muserhausen'),
            new PostalAccount('80-151-4')
        ));
*/
        $xml = $payment->asDom($doc);

        $xpath = new DOMXPath($doc);
        $this->assertNull($payment->getServiceLevel());
        $this->assertNull($payment->getLocalInstrument());
        $this->assertSame(0.0, $xpath->evaluate('count(./CdtTrfTxInf/PmtTpInf/LclInstrm/Prtry)', $xml));
    }
}
