<?php

namespace Yosmy\Payment\Gateway\Moneris;

use MongoDB\Model\BSONDocument;

class Charge extends BSONDocument
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->offsetGet('id');
    }

    /**
     * @return string
     */
    public function getCustomer(): string
    {
        return $this->offsetGet('customer');
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->offsetGet('order');
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->offsetGet('amount');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        parent::bsonUnserialize($data);
    }
}
