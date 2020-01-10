<?php

namespace Yosmy\Payment\Gateway\Moneris;

/**
 * @di\service()
 */
class PickCharge
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
     *
     * @return Charge
     */
    public function pick(
        string $id
    )  {
        /** @var Charge $charge */
        $charge = $this->manageCollection->findOne([
            '_id' => $id,
        ]);

        return $charge;
    }
}
