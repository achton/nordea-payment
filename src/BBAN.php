<?php

namespace NordeaPayment;

use DOMDocument;

/**
 * BBAN

 * @see
 * https://www.nordea.com/Images/34-48937/Nordea_Account_Structure_v1_4.pdf
 * for documentation.
 */
class BBAN implements AccountInterface
{
    /**
     * @var string
     */
    protected $registryNumber;
    protected $accountNumber;

    /**
     * Constructor
     *
     * @param int $registryNumber
     * @param int $registryNumber
     *
     */
    public function __construct($registryNumber, $accountNumber)
    {
        if (!self::check($registryNumber, 4)) {
            throw new \InvalidArgumentException('Registrynumber not valid.');
        }
        if (!self::check($accountNumber, 10)) {
            throw new \InvalidArgumentException('Accountnumber not valid.');
        }
        $this->accountNumber = $accountNumber;
        $this->registryNumber = $registryNumber;
    }

    /**
     * Format the BBAN either in a human-readable manner
     *
     * @return string The formatted BBAN
     */
    public function format()
    {
        $registryNumber = str_pad($this->registryNumber, 4, "0", STR_PAD_LEFT);
        $accountNumber = str_pad($this->accountNumber, 10, "0", STR_PAD_LEFT);
        return $registryNumber . $accountNumber;
    }

    protected static function check($number, $length)
    {
        if (!is_numeric($number)) {
            return false;
        }
        return strlen($number) <= $length ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc)
    {

        $code = $doc->createElement('Cd', 'BBAN');
        $schem = $doc->createElement('SchmeNm');
        $schem->appendChild($code);
        $id = $doc->createElement('Id', $this->format());
        $other = $doc->createElement('Othr');
        $other->appendChild($id);
        $other->appendChild($schem);
        $xml = $doc->createElement('Id');
        $xml->appendChild($other);

        return $xml;
    }

    /**
     * Returns a string representation.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return $this->format();
    }
}
