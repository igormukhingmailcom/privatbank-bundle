<?php

namespace Mukhin\PrivatbankBundle\Model;

class Card
{
    /** @var string */
    protected $account;

    /** @var string */
    protected $currency;

    /** @var string */
    protected $cardNumber;

    public static function fromResponse(\SimpleXMLElement $card)
    {
        return (new self)
            ->setAccount((string)$card->account)
            ->setCurrency((string)$card->currency)
            ->setCardNumber((string)$card->card_number)
            ;
    }

    /**
     * @param string $account
     *
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
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
     * @return \DateTime
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }
}