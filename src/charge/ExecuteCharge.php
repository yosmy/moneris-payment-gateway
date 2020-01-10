<?php

namespace Yosmy\Payment\Gateway\Moneris;

use LogicException;
use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.execute_charge']
 * })
 */
class ExecuteCharge implements Gateway\ExecuteCharge
{
    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @var AddCharge
     */
    private $addCharge;

    /**
     * @var Gateway\ProcessApiException[]
     */
    private $processExceptionServices;

    /**
     * @di\arguments({
     *     processExceptionServices: '#yosmy.payment.gateway.moneris.execute_charge.exception_throwed',
     * })
     *
     * @param ExecuteRequest                $executeRequest
     * @param AddCharge                     $addCharge
     * @param Gateway\ProcessApiException[] $processExceptionServices
     */
    public function __construct(
        ExecuteRequest $executeRequest,
        AddCharge $addCharge,
        ?array $processExceptionServices
    ) {
        $this->executeRequest = $executeRequest;
        $this->addCharge = $addCharge;
        $this->processExceptionServices = $processExceptionServices;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(
        string $customer,
        string $card,
        int $amount,
        string $description,
        string $statement
    ) {
        unset($description);

        // https://developer.moneris.com/Documentation/NA/E-Commerce%20Solutions/API/Purchase
        $amount = (string) number_format($amount / 100, 2, '.', '');

        $order = uniqid();

        $params = [
            'res_purchase_cc' => [
                'order_id' => $order,
                'cust_id' => $customer,
                'data_key' => $card,
                'amount' => $amount,
                'dynamic_descriptor' => $statement,
                'crypt_type' => 7,
            ]
        ];

        try {
            $response = $this->executeRequest->execute($params);

            $created = strtotime(sprintf(
                '%s %s',
                $response['TransDate'],
                $response['TransTime']
            ));
        } catch (Gateway\ApiException $e) {
            foreach ($this->processExceptionServices as $service) {
                try {
                    $service->process($e);
                } catch (Gateway\FundsException|Gateway\IssuerException|Gateway\RiskException|Gateway\FraudException $e) {
                    throw $e;
                } catch (Gateway\FieldException $e) {
                    throw new LogicException(null, null, $e);
                }
            }

            throw new LogicException(null, null, $e);
        }

        $id = $response['TransID'];

        // Charge saved, for future refund
        $this->addCharge->add(
            $id,
            $customer,
            $order,
            $amount
        );

        return new Gateway\Charge(
            $id,
            $created
        );
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'moneris';
    }
}
