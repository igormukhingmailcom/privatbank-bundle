<?php

namespace Mukhin\PrivatbankBundle\Model;

class Statements
{
    /** @var string */
    protected $status;

    /** @var float */
    protected $credit;

    /** @var float */
    protected $debet;

    /** @var Statement[] */
    protected $statements = [];

    public static function fromResponse(\SimpleXMLElement $statements)
    {
        return (new self)
            ->setStatus((string)$statements['status'])
            ->setCredit(floatval((string)$statements['credit']))
            ->setDebet(floatval((string)$statements['debet']))
            ->setStatements(Statement::arrayFromResponse($statements))
        ;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param float $debet
     *
     * @return $this
     */
    public function setDebet($debet)
    {
        $this->debet = $debet;

        return $this;
    }

    /**
     * @return float
     */
    public function getDebet()
    {
        return $this->debet;
    }

    /**
     * @param float $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param Statement[] $statements
     *
     * @return $this
     */
    public function setStatements($statements)
    {
        $this->statements = $statements;

        return $this;
    }

    /**
     * @return Statement[]
     */
    public function getStatements()
    {
        return $this->statements;
    }

}