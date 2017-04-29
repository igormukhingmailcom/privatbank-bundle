<?php

namespace Mukhin\PrivatbankBundle\Model;

class Statement
{
    /** @var string */
    protected $cardNumber;

    /** @var \DateTime */
    protected $transactionDate;

    /** @var string */
    protected $transactionCode;

    /** @var float */
    protected $sourceAmount;

    /** @var float */
    protected $sourceFee;

    /** @var  string */
    protected $sourceCurrency;

    /** @var float */
    protected $exchangeRate;

    /** @var float */
    protected $amount;

    /** @var string */
    protected $currency;

    /** @var float */
    protected $balance;

    /** @var string */
    protected $description;

    /**
     * @param \SimpleXMLElement $statements
     * @return $this[]
     */
    public static function arrayFromResponse(\SimpleXMLElement $statements)
    {
        $result = [];
        foreach ($statements->statement as $statement) {
            $result[] = self::fromResponse($statement);
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $statement
     * @return $this
     */
    public static function fromResponse(\SimpleXMLElement $statement)
    {
        list($sourceAmount, $sourceCurrency) = explode(' ', (string)$statement['amount']);
        list($amount, $currency) = explode(' ', (string)$statement['cardamount']);
        list($balance, $balanceCurrency) = explode(' ', (string)$statement['rest']);

        return (new self)
            ->setCardNumber((string)$statement['card'])
            ->setTransactionDate(\DateTime::createFromFormat(
                'Y-m-d H:i:s',
                sprintf('%s %s', (string)$statement['trandate'], (string)$statement['trantime']),
                new \DateTimeZone('Europe/Kiev')
            ))
            ->setTransactionCode((string)$statement['appcode'])
            ->setAmount(floatval($amount))
            ->setBalance(floatval($balance))
            ->setCurrency($currency)
            ->setSourceAmount(floatval($sourceAmount))
            ->setSourceCurrency($sourceCurrency)
            ->setDescription((string)$statement['terminal'])
            ->setExchangeRate($currency != $sourceCurrency ? abs(floatval($amount)/floatval($sourceAmount)) : null)
            ->setSourceFee($currency == $sourceCurrency ? abs(abs(floatval($amount)) - abs(floatval($sourceAmount))) : null)
        ;
    }

    /**
     * @param string $cardNumber
     *
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param \DateTime $transactionDate
     *
     * @return $this
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * @param string $transactionCode
     *
     * @return $this
     */
    public function setTransactionCode($transactionCode)
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionCode()
    {
        return $this->transactionCode;
    }

    /**
     * @param float $sourceAmount
     *
     * @return $this
     */
    public function setSourceAmount($sourceAmount)
    {
        $this->sourceAmount = $sourceAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getSourceAmount()
    {
        return $this->sourceAmount;
    }

    /**
     * @param float $sourceFee
     *
     * @return $this
     */
    public function setSourceFee($sourceFee)
    {
        $this->sourceFee = $sourceFee;

        return $this;
    }

    /**
     * @return float
     */
    public function getSourceFee()
    {
        return $this->sourceFee;
    }

    /**
     * @param string $sourceCurrency
     *
     * @return $this
     */
    public function setSourceCurrency($sourceCurrency)
    {
        $this->sourceCurrency = $sourceCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceCurrency()
    {
        return $this->sourceCurrency;
    }

    /**
     * @param float $exchangeRate
     *
     * @return $this
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getSignedAmount()
    {
        return $this->amount > 0 ? sprintf('+%s', $this->amount) : $this->amount;
    }

    /**
     * @param float $balance
     *
     * @return $this
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}