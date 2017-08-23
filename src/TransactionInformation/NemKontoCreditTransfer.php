<?php

namespace NordeaPayment\TransactionInformation;

use DOMDocument;
use InvalidArgumentException;
use NordeaPayment\AccountInterface;
use NordeaPayment\BIC;
use NordeaPayment\FinancialInstitutionInterface;
use NordeaPayment\IBAN;
use NordeaPayment\IID;
use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\PaymentInformation;
use NordeaPayment\PostalAddressInterface;
use NordeaPayment\SOSE;

/**
 * NemKontoCreditTransfer contains all the information about a NemKonto transaction.
 */
class NemKontoCreditTransfer extends CreditTransfer
{

    /**
     * @var FinancialInstitutionInterface
     */
    protected $creditorAgent;

    /**
     * {@inheritdoc}
     *
     * @param string $sose The creditors Social security number.
     * @param BIC|IID $creditorAgent BIC or IID of the creditor's financial institution
     *
     * @throws \InvalidArgumentException When the amount is not in EUR or CHF or when the creditor agent is not BIC or IID.
     */
    public function __construct($endToEndId, Money\Money $amount, $creditorName, PostalAddressInterface $creditorAddress, AccountInterface $sose, FinancialInstitutionInterface $creditorAgent)
    {
        if (!$amount instanceof Money\EUR && !$amount instanceof Money\DKK) {
            throw new InvalidArgumentException(sprintf(
              'The amount must be an instance of Money\EUR or Money\DKK (instance of %s given).',
              get_class($amount)
            ));
        }

        if (!$creditorAgent instanceof BIC && !$creditorAgent instanceof IID) {
            throw new InvalidArgumentException('The creditor agent must be an instance of BIC or IID.');
        }

        parent::__construct($endToEndId, $amount, $creditorName, $creditorAddress);

        $this->creditorAgent = $creditorAgent;
        $this->sose = $sose;
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

        $root->appendChild($this->buildNemKontoCreditor($doc, $this->sose));

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

    protected function buildNemKontoCreditor(\DOMDocument $doc, $sose)
    {
        $code = $doc->createElement('Cd', 'SOSE');

        $schemeName = $doc->createElement('SchmeNm');
        $schemeName->appendChild($code);

        $sose = $doc->createElement('Id', $sose);

        $other = $doc->createElement('Othr');
        $other->appendChild($sose);
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

