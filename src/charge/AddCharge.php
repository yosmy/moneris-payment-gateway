<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service()
 */
class AddCharge
{
    /**
     * @var ManageChargeCollection
     */
    private $manageCollection;

    /**
     * @param ManageChargeCollection $manageCollection
     */
    public function __construct(
        ManageChargeCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $id
     * @param string $customer
     * @param string $order
     * @param string $amount
     */
    public function add(
        string $id,
        string $customer,
        string $order,
        string $amount
    )  {
        try {
            $this->manageCollection->insertOne([
                '_id' => $id,
                'customer' => $customer,
                'order' => $order,
                'amount' => $amount
            ]);
        } catch (DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}
