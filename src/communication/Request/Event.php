<?php

namespace Yosmy\Payment\Gateway\Moneris\Request;

use JsonSerializable;
use MongoDB\BSON\Persistable;
use MongoDB\BSON\UTCDateTime;
use Yosmy\Log;

class Event implements Persistable, JsonSerializable, Log\Event
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $request;

    /**
     * @var array
     */
    private $response;

    /**
     * @var int
     */
    private $date;

    /**
     * @param string $id
     * @param array $request
     * @param array $response
     * @param int $date
     */
    public function __construct(
        string $id,
        array $request,
        array $response,
        int $date
    ) {
        $this->id = $id;
        $this->request = $request;
        $this->response = $response;
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * {@inheritDoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'request' => $this->request,
            'response' => $this->response,
            'date' => new UTCDateTime($this->date * 1000),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];

        $this->request = $data['request'];

        $this->response = $data['response'];

        /** @var UTCDateTime $date */
        $date = $data['date'];
        $this->date = $date->toDateTime()->getTimestamp();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'request' => $this->request,
            'response' => $this->response,
            'date' => $this->date
        ];
    }
}