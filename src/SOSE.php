<?php

namespace NordeaPayment;

use DOMDocument;

/**
 * BBAN

 * @see
 * https://www.nordea.com/Images/34-48937/Nordea_Account_Structure_v1_4.pdf
 * for documentation.
 */
class SOSE implements AccountInterface
{
    /**
     * @var string
     */
    protected $cpr;

    /**
     * Constructor
     *
     * @param int $cpr
     *
     */
    public function __construct($cpr)
    {
        if (!self::check($cpr, 10)) {
            throw new \InvalidArgumentException('Registrynumber not valid.');
        }

        $this->cpr = $cpr;
    }

    /**
     * Format the CPR either in a human-readable manner
     *
     * @return string The formatted CPR
     */
    public function format()
    {
        return str_pad($this->cpr, 10, "0", STR_PAD_LEFT);
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
