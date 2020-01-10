<?php

namespace Yosmy\Payment\Gateway\Moneris\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Moneris;
use LogicException;

/**
 * @di\service()
 */
class AddCard
{
    /**
     * @var Moneris\AddCard
     */
    private $addCard;

    /**
     * @param Moneris\AddCard $addCard
     */
    public function __construct(Moneris\AddCard $addCard)
    {
        $this->addCard = $addCard;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/moneris/add-card"})
     *
     * @param string $customer
     * @param string $number
     * @param string $month
     * @param string $year
     * @param string $cvc
     *
     * @return Gateway\Card
     */
    public function add(
        string $customer,
        string $number,
        string $month,
        string $year,
        string $cvc
    ) {
        try {
            return $this->addCard->add(
                $customer,
                $number,
                $month,
                $year,
                $cvc
            );
        } catch (
            Gateway\FieldException
            | Gateway\FraudException
            | Gateway\FundsException
            | Gateway\IssuerException
            | Gateway\RiskException $e
        ) {
            throw new LogicException(null, null, $e);
        }
    }
}