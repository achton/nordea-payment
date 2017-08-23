<?php

namespace NordeaPayment\PaymentInformation;

use DOMDocument;
use InvalidArgumentException;

/**
 * CategoryPurposeCode contains a category purpose code from the External Code Sets
 *
 * This is the instruction for the payment type. Some codes are linked to the
 * service level. This element can either be used here or at the transaction
 * (credit) level, but not both. SALA or PENS only valid at this level. All
 * credits must be the same.
 */
class CategoryPurposeCode
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Constructor
     *
     * @param string $code
     *
     * @throws InvalidArgumentException When the code is not valid
     */
    public function __construct($code)
    {
        $code = (string) $code;
        if (!preg_match('/^[A-Z]{4}$/', $code)) {
            throw new InvalidArgumentException('The category purpose code is not valid.');
        }

        $this->code = $code;
    }

    /**
     * Returns a XML representation of this purpose
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement The built DOM element
     */
    public function asDom(DOMDocument $doc)
    {
        return $doc->createElement('Cd', $this->code);
    }
}
