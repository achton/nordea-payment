<?php

namespace NordeaPayment\Tests\PaymentInformation;

use DOMDocument;
use NordeaPayment\PaymentInformation\CategoryPurposeCode;
use NordeaPayment\Tests\TestCase;

/**
 * @coversDefaultClass \NordeaPayment\PaymentInformation\CategoryPurposeCode
 */
class CategoryPurposeCodeTest extends TestCase
{
    /**
     * @dataProvider validSamples
     * @covers ::__construct
     */
    public function testValid($code)
    {
        $this->assertInstanceOf('NordeaPayment\PaymentInformation\CategoryPurposeCode', new CategoryPurposeCode($code));
    }

    public function validSamples()
    {
        return [
            ['SALA'], // Salary payment
            ['PENS'], // Pension payment
        ];
    }

    /**
     * @dataProvider invalidSamples
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalid($code)
    {
        new CategoryPurposeCode($code);
    }

    public function invalidSamples()
    {
        return [
            [''],
            ['sala'],
            ['SAL'],
            [' SALA'],
            ['B112'],
        ];
    }

    /**
     * @covers ::asDom
     */
    public function testAsDom()
    {
        $doc = new DOMDocument();
        $iid = new CategoryPurposeCode('SALA');

        $xml = $iid->asDom($doc);

        $this->assertSame('Cd', $xml->nodeName);
        $this->assertSame('SALA', $xml->textContent);
    }
}
