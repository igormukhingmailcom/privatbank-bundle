# MukhinPrivatbankBundle

+[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/igormukhingmailcom/privatbank-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/igormukhingmailcom/privatbank-bundle/?branch=master)
+[![Code Coverage](https://scrutinizer-ci.com/g/igormukhingmailcom/privatbank-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/igormukhingmailcom/privatbank-bundle/?branch=master)

This bundle provides ability to interact with Privatbank/Privat24 API (https://api.privatbank.ua/p24api).

Not all available methods implemented (only informational ones for personal accounts). 
Fill free to contribute.

## Installation

```
composer require igormukhingmailcom/privatbank-bundle
```

```
# app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new Mukhin\PrivatbankBundle\MukhinPrivatbankBundle(),
        ];
    }
}
```

## Configuration

```
# app/config/parameters.yml
parameters:
    ...
    privatbank_merchant_id: 12345
    privatbank_merchant_secret: Xn3U9xm01DF4888LY1W2Zz5eDXwkMrBZ
    privatbank_card_number: 4149437864339229
```

```
# app/config/parameters.yml.dist
parameters:
    ...
    # Privatbank merchant
    privatbank_merchant_id: ~
    privatbank_merchant_secret: ~
    privatbank_card_number: ~
```

```
# app/config/config.yml
mukhin_privatbank:
    merchants:
        my_merchant_name:
            merchant_id: "%privatbank_merchant_id%"
            merchant_secret: "%privatbank_merchant_secret%"
            card_number: "%privatbank_card_number%"

```

Regarding this configuration, merchant service `mukhin_privatbank.merchant.my_merchant_name`
will be created.

# Usage

```
$merchant = $this->get('mukhin_privatbank.merchant.my_merchant_name');

# Balance
$balance = $merchant->getBalance();
echo sprintf(
    '%s: Balance at card %s is %s %s',
    $balance->getBalanceDate()->format('Y-m-d H:i:s'),
    $balance->getCard()->getCardNumber(),
    $balance->getBalance(),
    $balance->getCard()->getCurrency()
);

# History
$sinceDate = new \DateTime('1970-01-01');
$toDate = new \DateTime();
$history = $merchant->getHistory($sinceDate, $toDate);

echo sprintf(
    'Debit is %s, credit is %s for period %s-%s',
    $history->getCredit(),
    $history->getDebit(),
    $sinceDate->format('Y-m-d H:i:s'),
    $toDate->format('Y-m-d H:i:s')
);
foreach ($history->getStatements() as $statement)
    echo sprintf(
        '%s %s, balance is %s %s at %s (%s)',
        $statement->getSignedAmount(),
        $statement->getCurrency(),
        $statement->getBalance(),
        $statement->getCurrency(),
        $statement->getTransactionDate()->format('Y-m-d H:i:s'),
        $statement->getDescription()
    );
}
```

# To read

* https://api.privatbank.ua/#p24/balance
* https://api.privatbank.ua/#p24/orders