<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.delete_card']
 * })
 */
class DeleteCard implements Gateway\DeleteCard
{
    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @param ExecuteRequest $executeRequest
     */
    public function __construct(
        ExecuteRequest $executeRequest
    ) {
        $this->executeRequest = $executeRequest;
    }

    /**
     * @param string $customer
     * @param string $card
     *
     * @throws Gateway\ApiException
     */
    public function delete(
        string $customer,
        string $card
    ) {
        unset($customer);

        $params = [
            'res_delete' => [
                'data_key' => $card,
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