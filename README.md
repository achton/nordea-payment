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
    'e2e-001',
    new Money\DKK(130000), // DKK 1300.00
    'Anders And',
    new StructuredPostalAddress('Andevej', '13', '8000', 'Odense C'),
    new BBAN('1234', '1234567890'),
    new BIC('NDEADKKK')
);

$transaction2 = new NemKontoCreditTransfer(
    'e2e-002',
    new Money\DKK(30000), // DKK 300.00
    'Rasmus Klump',
    new StructuredPostalAddress('Pildskaddevej', '12', '3782', 'Klemensker'),
    new BBAN('0568', '7894561'),
    new BIC('NDEADKKK')
);

$payment = new PaymentInformation(
    'payment-001',
    'René Dif fra Aqua',
    new BIC('NDEADKKK'),
    new BBAN('1234', '1234567890')
);
$payment->addTransaction($transaction1);
$payment->addTransaction($transaction2);

$message = new CustomerCreditTransfer('message-001', 'Acme Test A/S', 'ACMETEST');
$message->addPayment($payment);

echo $message->asXml();
```

## Further Resources

- [Nordea documentation about Transactions in XML ISO20022](https://www.nordea.com/en/our-services/cashmanagement/oursolutions/egateway/#tab=Format-Descriptions_XML-ISO20022-messages)