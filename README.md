# Omnipay: Jibit

**Jibit driver for the Omnipay PHP payment processing library**

![Packagist Version](https://img.shields.io/packagist/v/armezit/omnipay-jibit.svg)
![PHP from Packagist](https://img.shields.io/packagist/php-v/armezit/omnipay-jibit.svg)
![Packagist](https://img.shields.io/packagist/l/armezit/omnipay-jibit.svg)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Jibit support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require 
`league/omnipay` and `armezit/omnipay-jibit` with Composer:

```
composer require league/omnipay armezit/omnipay-jibit
```

## Basic Usage

The following gateways are provided by this package:

* Jibit

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Example

### Purchase

The result will be a redirect to the gateway or bank.

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Jibit');
$gateway->setApiKey('API_KEY');
$gateway->setSecretKey('SECRET_KEY');
$gateway->setReturnUrl('https://www.example.com/return');

// Send purchase request
$response = $gateway->purchase([
    'amount' => $amount,
    'currency' => $currency,
    'transactionId' => $transactionId, // referenceNumber in Jibit api doc
    'userId' => $userId, // userIdentifier in Jibit api doc
])->send();

// Process response
if ($response->isSuccessful() && $response->isRedirect()) {
    // store the order identifier to use in completePurchase()
    $orderIdentifier = $response->getTransactionReference();
    // Redirect to offsite payment gateway
    $response->redirect();
} else {
    // Payment failed: display message to customer
    echo $response->getMessage();
}
```

### Complete Purchase (Verify)

On return, the usual completePurchase will provide the result of the transaction attempt.

The final result includes the following methods to inspect additional details:

```php
// Send purchase complete request
$response = $gateway->completePurchase([
    'transactionReference' => $orderIdentifier, 
])->send();

// Process response
if ($response->isPending()) {
    // In case of pending, you must inquiry the order later
    return;
}

if (!$response->isSuccessful() || $response->isCancelled()) {
    // Payment failed: display message to customer
    echo $response->getMessage();
} else {
    // Payment was successful
    print_r($response);
}
```

### Inquiry Order

Inquiry an order by the orderIdentifier:

```php
$response = $gateway->fetchTransaction([
    'transactionReference' => $orderIdentifier,
])->send();

if ($response->isPending()) {
    // In case of pending, you must inquiry the order later
    return;
}

if ($response->isCancelled()) {
    // Payment failed: display message to customer
    echo $response->getMessage();
} else if ($response->isSuccessful()) {
    // Payment was successful
    print_r($response);
}
```

### Testing

```sh
composer test
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/armezit/omnipay-jibit/issues),
or better yet, fork the library and submit a pull request.
