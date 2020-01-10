<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;
use LogicException;

/**
 * @di\service({
 *     tags: ['yosmy.payment.gateway.add_card']
 * })
 */
class AddCard implements Gateway\AddCard
{
    /**
     * @var ExecuteRequest
     */
    private $executeRequest;

    /**
     * @var Gateway\ProcessApiException[]
     */
    private $processExceptionServices;

    /**
     * @di\arguments({
     *     processExceptionServices: '#yosmy.payment.gateway.moneris.add_card.exception_throwed',
     * })
     *
     * @param ExecuteRequest                $executeRequest
     * @param Gateway\ProcessApiException[] $processExceptionServices
     */
    public function __construct(
        ExecuteRequest $executeRequest,
        array $processExceptionServices
    ) {
        $this->executeRequest = $executeRequest;
        $this->processExceptionServices = $processExceptionServices;
    }

    /**
     * {@inheritDoc}
     */
    public function add(
        string $customer,
        string $number,
        string $month,
        string $year,
        string $cvc
    ) {
        try {
            $this->verifyCard(
                $customer,
                $number,
                $year,
                $month,
                $cvc
            );
        } catch (Gateway\FieldException $e) {
            throw $e;
        }

        $params = [
            'res_add_cc' => [
                'order_id' => uniqid(),
                'cust_id' => $customer,
                'pan' => $number,
                'expdate' => sprintf('%s%s', $year, $month),
                'cvd_info' => [
                    'cvd_indicator' => 1,
                    'cvd_value' => $cvc
                ],
                'crypt_type' => 7, // SSL-enabled merchant
            ]
        ];

        try {
            $response = $this->executeRequest->execute($params);
        } catch (Gateway\ApiException $e) {
            foreach ($this->processExceptionServices as $service) {
                try {
                    $service->process($e);
                } catch (Gateway\FieldException $e) {
                    throw $e;
                } catch (Gateway\FundsException $e) {
                    throw $e;
                } catch (Gateway\IssuerException $e) {
                    throw $e;
                } catch (Gateway\RiskException $e) {
                    throw $e;
                } catch (Gateway\FraudException $e) {
                    throw $e;
                }
            }

            throw new LogicException(null, null, $e);
        }

        $last4 = substr($number, -4);

        return new Gateway\Card(
            $response['DataKey'],
            $last4
        );
    }

    /**
     * {@inheritDoc}
     */
    public function identify() {
        return 'moneris';
    }

    /**
     * @param string $customer
     * @param string $number
     * @param string $year
     * @param string $month
     * @param string $cvc
     *
     * @throws Gateway\FieldException
     */
    private function verifyCard(
        string $customer,
        string $number,
        string $year,
        string $month,
        string $cvc
    ) {
        $params = [
            'card_verification' => [
                'order_id' => uniqid(),
                'cust_id' => $customer,
                'pan' => $number,
                'expdate' => sprintf('%s%s', $year, $month),
                'cvd_info' => [
                    'cvd_indicator' => 1,
                    'cvd_value' => $cvc
                ],
                'crypt_type' => 7, // SSL-enabled merchant
            ]
        ];

        try {
            $response = $this->executeRequest->execute($params);

            // https://developer.moneris.com/More/Testing/CVD%20Result%20Codes
            if (
                // Doesn't match?
                $response['CvdResultCode'] != '1M'
                // Doesn't match for AmEx/JCB
                && $response['CvdResultCode'] != '1Y'
            ) {
                throw new Gateway\FieldException('cvc');
            }
        } catch (Gateway\ApiException $e) {
            foreach ($this->processExceptionServices as $service) {
                try {
                    $service->process($e);
                } catch (Gateway\FieldException $e) {
                    throw $e;
                } catch (
                    Gateway\FundsException
                    | Gateway\IssuerException
                    | Gateway\RiskException
                    | Gateway\FraudException $e
                ) {
                    throw new LogicException(null, null, $e);
                }
            }

            throw new LogicException(null, null, $e);
        }
    }
}