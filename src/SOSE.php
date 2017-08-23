<?php

namespace NordeaPayment;

use DOMDocument;
use InvalidArgumentException;

/**
 * Social Security (SOSE) aka CPR.
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

        if (!self::check($cpr)) {
            throw new InvalidArgumentException('SOSE/CPR number not valid.');
        }

        $this->cpr = $cpr;
    }

    /**
     * Translate a 2-digit year in the CPR number to a 4-digit year, based on
     * CPR rules for the 7th digit.
     *
     * @param string $cpr 10-digit cpr number.
     *
     * @return string 4 digit representation of year of birth
     */
    protected static function getYear($cpr) {
        $year = substr($cpr, 4, 2);
        $seventh = $cpr[6];
        if ($seventh < 4) {
            $century = 19;
        } elseif ($seventh == 4 || $seventh == 9) {
            if ($year <= 36) {
                $century = 20;
            } else {
                $century = 19;
            }
        } elseif ($seventh <= 8) {
            $century = 20;
        }

        return (string) $century . $year;
    }

    /**
     * Format the CPR either in a human-readable manner
     *
     * @return string The formatted CPR
     */
    public function format()
    {
        return $this->cpr;
    }

    protected static function check($number)
    {
        if (!is_numeric($number)) {
            return false;
        }

        // Check for valid date in CPR number.
        list($d, $m) = str_split(substr($number, 0, 4), 2);
        if (checkdate($m, $d, self::getYear($number)) === false) {
            return false;
        }

        return strlen($number) <= 10 ? true : false;
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
