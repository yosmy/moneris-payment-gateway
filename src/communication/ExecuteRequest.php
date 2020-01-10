<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;
use Yosmy\Http\Exception;
use Yosmy\Http;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * @di\service({
 *     private: true
 * })
 */
class ExecuteRequest
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $storeId;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var Http\ExecuteRequest
     */
    private $executeRequest;

    /**
     * @var Request\LogEvent
     */
    private $logEvent;

    /**
     * @di\arguments({
     *     host:     "%moneris_host%",
     *     storeId:  "%moneris_store_id%",
     *     apiToken: "%moneris_api_token%"
     * })
     *
     * @param string              $host
     * @param string              $storeId
     * @param string              $apiToken
     * @param Http\ExecuteRequest $executeRequest
     * @param Request\LogEvent    $logEvent
     */
    public function __construct(
        string $host,
        string $storeId,
        string $apiToken,
        Http\ExecuteRequest $executeRequest,
        Request\LogEvent $logEvent
    ) {
        $this->host = $host;
        $this->storeId = $storeId;
        $this->apiToken = $apiToken;
        $this->executeRequest = $executeRequest;
        $this->logEvent = $logEvent;
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws Gateway\ApiException
     */
    public function execute(
        array $params
    ) {
        $serializer = new Serializer([], [new XmlEncoder()]);

        $xml = $serializer->serialize(
            array_merge(
                // Auth
                [
                    'store_id' => $this->storeId,
                    'api_token' => $this->apiToken
                ],
                $params
            ),
            'xml',
            [
                XmlEncoder::ROOT_NODE_NAME => 'request'
            ]
        );

        $request = [
            'params' => $params
        ];

        try {
            $response = $this->executeRequest->execute(
                'post',
                sprintf('https://%s:443/gateway2/servlet/MpgRequest', $this->host),
                [
                    'headers' => [
                        'Content-Type' => 'text/xml'
                    ],
                    'body' => $xml
                ]
            );

            $response = $response->getRawBody();

            $response = simplexml_load_string($response);

            $response = json_decode(json_encode((array) $response), TRUE);

            $response = $response['receipt'];

            // https://developer.moneris.com/More/Testing/Financial%20Response%20Codes
            if (
                $response['ResponseCode'] == "null"
                || (int) $response['ResponseCode'] >= 50
            ) {
                $this->logEvent->log(
                    $request,
                    $response
                );

                throw new Gateway\ApiException(
                    $response
                );
            }

            $this->logEvent->log(
                $request,
                $response
            );

            return $response;
        } catch (Exception $e) {
            $response = $e->getResponse();

            $this->logEvent->log(
                $request,
                $response
            );

            throw new Gateway\ApiException($response);
        }
    }
}
