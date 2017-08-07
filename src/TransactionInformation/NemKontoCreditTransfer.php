<?php

namespace NordeaPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use NordeaPayment\BIC;
use NordeaPayment\FinancialInstitutionInterface;
use NordeaPayment\IBAN;
use NordeaPayment\IID;
use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\PaymentInformation;
use NordeaPayment\PostalAddressInterface;
use NordeaPayment\SOSE;

/**
 * BankCreditTransfer contains all the information about a type 3 transaction.
 */
class NemKontoCreditTransfer extends CreditTransfer
{
    /**
     * @var IBAN
     */
    protected $creditorIBAN;

    /**
     * @var FinancialInstitutionInterface
     */
    protected $creditorAgent;

    /**
     * {@inheritdoc}
     *
     * @param IBAN    $creditorIBAN  IBAN of the creditor
     * @param BIC|IID $creditorAgent BIC or IID of the creditor's financial institution
     * @param string $cpr The creditors CPR number.
     *
     * @throws \InvalidArgumentException When the amount is not in EUR or CHF or when the creditor agent is not BIC or IID.
     */
    public function __construct($endToEndId, Money\Money $amount, $creditorName, PostalAddressInterface $creditorAddress, FinancialInstitutionInterface $creditorAgent, SOSE $cpr)
    {
        if (!$amount instanceof Money\EUR && !$amount instanceof Money\DKK) {
            throw new InvalidArgumentException(sprintf(
              'The amount must be an instance of Money\EUR or Money\CHF (instance of %s given).',
              get_class($amount)
            ));
        }

        if (!$creditorAgent instanceof BIC && !$creditorAgent instanceof IID) {
            throw new InvalidArgumentException('The creditor agent must be an instance of BIC or IID.');
        }

        parent::__construct($endToEndId, $amount, $creditorName, $creditorAddress);

        $this->creditorIBAN =  new IBAN('CH51 0022 5225 9529 1301 C');
        $this->creditorAgent = $creditorAgent;
        $this->serviceLevel = 'NURG';
        $this->cpr = $cpr;
    }

    /**
     * {@inheritdoc}
     */
    public function asDom(DOMDocument $doc, PaymentInformation $paymentInformation)
    {
        $root = $this->buildHeader($doc, $paymentInformation);

        $creditorAgent = $doc->createElement('CdtrAgt');
        $creditorAgent->appendChild($this->creditorAgent->asDom($doc));
        $root->appendChild($creditorAgent);

        $root->appendChild($this->buildNemKontoCreditor($doc, $this->cpr));

        $creditorAccount = $doc->createElement('CdtrAcct');

        $id = $doc->createElement('Id', 'NOTPROVIDED');

        $other = $doc->createElement('Othr');
        $other->appendChild($id);

        $outer_id = $doc->createElement('Id');
        $outer_id->appendChild($other);

        $creditorAccount->appendChild($outer_id);
        $root->appendChild($creditorAccount);

        $this->appendPurpose($doc, $root);

        $this->appendRemittanceInformation($doc, $root);

        return $root;
    }

    protected function buildNemKontoCreditor(\DOMDocument $doc, $cpr)
    {
        $code = $doc->createElement('Cd', 'SOSE');

        $schemeName = $doc->createElement('SchmeNm');
        $schemeName->appendChild($code);

        $cpr = $doc->createElement('Id', $cpr);

        $other = $doc->createElement('Othr');
        $other->appendChild($cpr);
        $other->appendChild($schemeName);

        $privateId = $doc->createElement('PrvtId');
        $privateId->appendChild($other);

        $id = $doc->createElement('Id');
        $id->appendChild($privateId);

        $creditor = parent::buildCreditor($doc);
        $creditor->appendChild($id);

        return $creditor;
    }
}

