<?php

namespace NordeaPayment\Message;

use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\PaymentInformation;

/**
 * CustomerCreditTransfer represents a Customer Credit Transfer Initiation (pain.001) message
 */
class CustomerCreditTransfer extends AbstractMessage
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $initiatingParty;

    /**
     * @var array
     */
    protected $payments;

    /**
     * @var \DateTime
     */
    protected $creationTime;

    /**
     * Constructor
     *
     * @param string $id Identifier of the message (should usually be unique over a period of at least 90 days)
     * @param string $initiatingParty Name of the initiating party
     */
    public function __construct($id, $initiatingParty)
    {
        $this->id = (string) $id;
        $this->initiatingParty = (string) $initiatingParty;
        $this->payments = array();
        $this->creationTime = new \DateTime();
    }

    /**
     * Manually sets the creation time
     *
     * @param \DateTime $creationTime The desired creation time
     *
     * @return CustomerCreditTransfer This message
     */
    public function setCreationTime(\DateTime $creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Adds a payment instruction
     *
     * @param PaymentInformation $payment The payment to be added
     *
     * @return CustomerCreditTransfer This message
     */
    public function addPayment(PaymentInformation $payment)
    {
        $this->payments[] = $payment;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaName()
    {
        return 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildDom(\DOMDocument $doc)
    {
        $transactionCount = 0;
        $transactionSum = new Money\Mixed(0);
        foreach ($this->payments as $payment) {
            $transactionCount += $payment->getTransactionCount();
            $transactionSum = $transactionSum->plus($payment->getTransactionSum());
        }

        $root = $doc->createElement('CstmrCdtTrfInitn');
        $header = $doc->createElement('GrpHdr');
        $header->appendChild($doc->createElement('MsgId', $this->id));
        $header->appendChild($doc->createElement('CreDtTm', $this->creationTime->format('Y-m-d\TH:i:sP')));
        $header->appendChild($doc->createElement('NbOfTxs', $transactionCount));
        $header->appendChild($doc->createElement('CtrlSum', $transactionSum->format()));
        $header->appendChild($this->buildInitiatingParty($doc, 'Dong A/S', 'DONGNEMOTEST'));
        $root->appendChild($header);

        foreach ($this->payments as $payment) {
            $root->appendChild($payment->asDom($doc));
        }

        return $root;
    }
}
