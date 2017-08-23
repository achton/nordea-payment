# Nordea Payment

[![Build Status](https://travis-ci.org/achton/swiss-payment.png?branch=master)](https://travis-ci.org/achton/swiss-payment)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/achton/swiss-payment/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/achton/swiss-payment/?branch=master)

**Nordea Payment** is a PHP library to generate Danish Nordea pain.001 XML messages (complies with ISO-20022). It is based on [Nordea's Corporate eGateway Message Implementation Guideline for pain.001 messages](resources/MIG_pain.001.001.03_v_2.1_Release-2016-08-19_FINAL.PDF). It is also a fork of the [SwissPayment library](https://github.com/z38/swiss-payment) and altered to fit Nordea's needs.

**NOTE:** It is currently only used for domestic payments, so intl transactions have not been tested as rigorously.

## Installation

Just install [Composer](http://getcomposer.org) and run `composer require trogels/nordea-payment` in your project directory.

## Usage

To get a basic understanding on how the messages are structured, take a look [the resources](#further-resources) mentioned below. The following example shows how to create a message containing two transactions:

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use NordeaPayment\BIC;
use NordeaPayment\IBAN;
use NordeaPayment\Message\CustomerCreditTransfer;
use NordeaPayment\Money;
use NordeaPayment\PaymentInformation\PaymentInformation;
use NordeaPayment\StructuredPostalAddress;
use NordeaPayment\TransactionInformation\BankCreditTransfer;

$transaction1 = new BankCreditTransfer(
    'instr-001',
    'e2e-001',
    new Money\DKK(130000), // DKK 1300.00
    'Muster Transport AG',
    new StructuredPostalAddress('Wiesenweg', '14b', '8058', 'Zürich-Flughafen'),
    new IBAN('CH51 0022 5225 9529 1301 C'),
    new BIC('NDEADKKK')
);

$transaction2 = new NemKontoCreditTransfer(
    'e2e-002',
    new Money\DKK(30000), // DKK 300.00
    'Finanzverwaltung Stadt Musterhausen',
    new StructuredPostalAddress('Altstadt', '1a', '4998', 'Muserhausen'),
    new PostalAccount('80-151-4')
);

$payment = new PaymentInformation(
    'payment-001',
    'InnoMuster AG',
    new BIC('NDEADKKK'),
    new IBAN('CH6600700110000204481')
);
$payment->addTransaction($transaction1);
$payment->addTransaction($transaction2);

$message = new CustomerCreditTransfer('message-001', 'InnoMuster AG');
$message->addPayment($payment);

echo $message->asXml();
```

**Tip:** Take a look at `NordeaPayment\Tests\Message\CustomerCreditTransferTest` to see all payment types in action.

## Further Resources

- [Nordea documentation about Transactions in XML ISO20022](https://www.nordea.com/en/our-services/cashmanagement/oursolutions/egateway/#tab=Format-Descriptions_XML-ISO20022-messages)