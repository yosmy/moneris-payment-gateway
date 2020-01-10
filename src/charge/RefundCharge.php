<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.refund_charge']
 * })
 */
class RefundCharge implements Gateway\RefundCharge
{
    /**
     * @var PickCharge
     */
    private $pickCharge;

    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @param PickCharge     $pickCharge
     * @param ExecuteRequest $executeRequest
     */
    public function __construct(
        PickCharge $pickCharge,
        ExecuteRequest $executeRequest
    ) {
        $this->pickCharge = $pickCharge;
        $this->executeRequest = $executeRequest;
    }

    /**
     * {@inheritDoc}
     */
    public function refund(
        string $id
    ) {
        // The charge was saved, to get amount and customer
        $charge = $this->pickCharge->pick($id);

        $params = [
            'refund' => [
                'order_id' => $charge->getOrder(), // Same as charge
                'txn_number' => $id,
                'amount' => $charge->getAmount(),
                'cust_id' => $charge->getCustomer(),
                'dynamic_descriptor' => 'Reembolso',
                'crypt_type' => 7,
            ]
        ];

        try {
            $this->executeRequest->execute($params);
        } catch (Gateway\ApiException $e) {
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'moneris';
    }
}
