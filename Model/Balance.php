<?php

namespace Mukhin\PrivatbankBundle\Model;

class Balance
{
    /** @var Card */
    protected $card;

    /** @var float */
    protected $balance;

    /** @var \DateTime */
    protected $balanceDate;

    public static function fromResponse(\SimpleXMLElement $cardBalance)
    {
        return (new self)
            ->setCard(Card::fromResponse($cardBalance->card))
            ->setBalance((float)$cardBalance->balance)
            ->setBalanceDate(new \DateTime(
                $cardBalance->balanceDate,
                new \DateTimeZone('Europe/Kiev')
            ))
        ;
    }

    /**
     * @param Card $card
     *
     * @return $this
     */
    public function setCard($card)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
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
     * @param \DateTime $balanceDate
     *
     * @return $this
     */
    public function setBalanceDate(\DateTime $balanceDate)
    {
        $this->balanceDate = $balanceDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBalanceDate()
    {
        return $this->balanceDate;
    }
}