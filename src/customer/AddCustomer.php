<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.add_customer']
 * })
 */
class AddCustomer implements Gateway\AddCustomer
{
    /**
     * @return Gateway\Customer
     */
    public function add()
    {
        // Moneris doesn't have api to create customer

        $id = uniqid();

        return new Gateway\Customer(
            $id
        );
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'moneris';
    }
}