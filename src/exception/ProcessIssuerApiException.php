<?php

namespace Yosmy\Payment\Gateway\Moneris;

use Yosmy\Payment\Gateway;

/**
 * @di\service({
 *     tags: [
 *         'yosmy.payment.gateway.moneris.execute_charge.exception_throwed'
 *     ]
 * })
 */
class ProcessIssuerApiException implements Gateway\ProcessApiException
{
    /**
     * {@inheritDoc}
     */
    public function process(Gateway\ApiException $e)
    {
        // https://developer.moneris.com/More/Testing/Financial%20Response%20Codes
        // Do NOT validate the combination of RBC and ISO response codes. These are liable to change without notice

        if (
            $e->getResponse()['ResponseCode'] == '050'
            && in_array(
                $e->getResponse()['ISO'],
                ['01', '05', '06']
            )
        ) {
            throw new Gateway\IssuerException();
        }

        // Not supported
        if (
            $e->getResponse()['ResponseCode'] == '056'
        ) {
            throw new Gateway\IssuerException();
        }

        // Invalid account
        if (
            $e->getResponse()['ResponseCode'] == '200'
        ) {
            throw new Gateway\IssuerException();
        }

        // Invalid transaction, rejected
        if (
            $e->getResponse()['ResponseCode'] == '476'
        ) {
            throw new Gateway\IssuerException();
        }

        // Refer Call/Invalid Card Number
        if (
            $e->getResponse()['ResponseCode'] == '477'
        ) {
            throw new Gateway\IssuerException();
        }

        // Declined
        if (
            $e->getResponse()['ResponseCode'] == '478'
        ) {
            throw new Gateway\IssuerException();
        }

        // Transaction not allowed to be processed by cardholder
        if (
            $e->getResponse()['ResponseCode'] == '481'
            && in_array(
                $e->getResponse()['ISO'],
                ['05', '50', '51', '57']
            )
        ) {
            throw new Gateway\IssuerException();
        }

        // Expired Card
        if (
            $e->getResponse()['ResponseCode'] == '482'
        ) {
            throw new Gateway\IssuerException();
        }

        // Refer to Issuer
        if (
            $e->getResponse()['ResponseCode'] == '483'
        ) {
            throw new Gateway\IssuerException();
        }

        // System timeout
        if (
            $e->getResponse()['ResponseCode'] == '810'
        ) {
            throw new Gateway\IssuerException();
        }
    }
}