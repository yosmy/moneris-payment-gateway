<?php

namespace Yosmy\Payment\Gateway\Moneris\Test;

use Yosmy\Payment\Gateway;
use Yosmy\Payment\Gateway\Moneris;
use LogicException;

/**
 * @di\service()
 */
class ExecuteCharge
{
    /**
     * @var Moneris\ExecuteCharge
     */
    private $executeCharge;

    /**
     * @param Moneris\ExecuteCharge $executeCharge
     */
    public function __construct(Moneris\ExecuteCharge $executeCharge)
    {
        $this->executeCharge = $executeCharge;
    }

    /**
     * @cli\resolution({command: "/payment/gateway/moneris/execute-charge"})
     *
     * @param string $customer
     * @param string $card
     * @param int    $amount
     * @param string $description
     * @param string $statement
     */
    public function execute(
        string $customer,
        string $card,
        int $amount,
        string $description,
        string $statement
    ) {
        try {
            return $this->executeCharge->execute(
                $customer,
                $card,
                $amount,
                $description,
                $statement
            );
        } catch (
            Gateway\FraudException
            | Gateway\FundsException
            | Gateway\IssuerException
            | Gateway\RiskException $e
        ) {
            throw new LogicException(null, null, $e);
        }
    }
}