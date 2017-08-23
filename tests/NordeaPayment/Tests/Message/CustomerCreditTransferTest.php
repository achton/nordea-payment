<?php

namespace NordeaPayment\Tests\Message;

use NordeaPayment\BBAN;
use NordeaPayment\BIC;
use NordeaPayment\FinancialInstitutionAddress;
use NordeaPayment\GeneralAccount;
use NordeaPayment\IBAN;
use NordeaPayment\IID;
use NordeaPayment\Message\CustomerCreditTransfer;
use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\CategoryPurposeCode;
use NordeaPayment\PaymentInformation\PaymentInformation;
use NordeaPayment\SOSE;
use NordeaPayment\StructuredPostalAddress;
use NordeaPayment\Tests\TestCase;
use NordeaPayment\TransactionInformation\BankCreditTransfer;
use NordeaPayment\TransactionInformation\NemKontoCreditTransfer;
use NordeaPayment\TransactionInformation\PurposeCode;
use NordeaPayment\UnstructuredPostalAddress;

class CustomerCreditTransferTest extends TestCase
{
    const SCHEMA = 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03';
    const SCHEMA_FILE = 'pain.001.001.03.ch.02.xsd';

    protected function buildMessage()
    {
        // IBAN
        $transaction = new BankCreditTransfer(
            'e2e-001',
            new Money\DKK(130000), // DKK 1300.00
            'Anders And',
            new StructuredPostalAddress('Andevej', '14b', '5300', 'Kerteminde'),
            new IBAN('CH51 0022 5225 9529 1301 C'),
            new BIC('NDEADKKK')
        );

        // BBAN
        $transaction2 = new BankCreditTransfer(
            'e2e-001',
            new Money\DKK(130000), // DKK 1300.00
            'Birger Birgersen',
            new StructuredPostalAddress('Birgervej', '1a', '5000', 'Odense C'),
            new BBAN('1234', '1234567890'),
            new BIC('NDEADKKK')
        );

        // SOSE
        $transaction3 = new NemKontoCreditTransfer(
            'e2e-002',
            new Money\DKK(130000), // DKK 1300.00
            'Charles Charleston',
            new StructuredPostalAddress('Cirkelvej', '54', '1150', 'København K'),
            new SOSE('1710791111'),
            new BIC('NDEADKKK')
        );

        $iban4 = new IBAN('CH51 0022 5225 9529 1301 C');
        $transaction4 = new BankCreditTransfer(
            'e2e-004',
            new Money\DKK(30000), // DKK 300.00
            'Muster Transport AG',
            new StructuredPostalAddress('Wiesenweg', '14b', '8058', 'Zürich-Flughafen'),
            $iban4,
            IID::fromIBAN($iban4)
        );
        $transaction4->setPurpose(new PurposeCode('AIRB'));

        $payment = new PaymentInformation('payment-001', 'InnoMuster AG', new BIC('NDEADKKK'), new IBAN('CH6600700110000204481'));
        $payment->addTransaction($transaction);
        $payment->addTransaction($transaction2);
        $payment->addTransaction($transaction3);
        $payment->addTransaction($transaction4);


        $message = new CustomerCreditTransfer('message-001', 'Acme Test A/S', 'ACMETEST');
        $message->addPayment($payment);

        return $message;
    }

    public function testGroupHeader()
    {
        $xml = $this->buildMessage()->asXml();

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('pain001', self::SCHEMA);

        $nbOfTxs = $xpath->evaluate('string(//pain001:GrpHdr/pain001:NbOfTxs)');
        $this->assertEquals('4', $nbOfTxs);

        $ctrlSum = $xpath->evaluate('string(//pain001:GrpHdr/pain001:CtrlSum)');
        $this->assertEquals('4200.00', $ctrlSum);
    }
/*
    public function testSchemaValidation()
    {
        $xml = $this->buildMessage()->asXml();
        $schemaPath = __DIR__.'/../../../../'.self::SCHEMA_FILE;

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        libxml_use_internal_errors(true);
        $valid = $doc->schemaValidate($schemaPath);
        foreach (libxml_get_errors() as $error) {
            $this->fail($error->message);
        }
        $this->assertTrue($valid);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
    }*/
}
