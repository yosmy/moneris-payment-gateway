<?php

namespace Yosmy\Payment\Gateway\Moneris\Request;

use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service()
 */
class LogEvent
{
    /**
     * @var ManageEventCollection
     */
    private $manageCollection;

    /**
     * @param ManageEventCollection $manageCollection
     */
    public function __construct(
        ManageEventCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param array  $request
     * @param array  $response
     */
    public function log(
        array $request,
        array $response
    )  {
        try {
            $this->manageCollection->insertOne(new Event(
                uniqid(),
                $request,
                $response,
                time()
            ));
        } catch (DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}
