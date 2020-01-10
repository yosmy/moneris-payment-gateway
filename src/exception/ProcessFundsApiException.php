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
class ProcessFundsApiException implements Gateway\ProcessApiException
{
    /**
     * {@inheritDoc}
     */
    public function process(Gateway\ApiException $e)
    {
        // https://developer.moneris.com/More/Testing/Financial%20Response%20Codes

        // Low funds/Insufficient Balance
        if (
            $e->getResponse()['ResponseCode'] == '481'
            && in_array(
                $e->getResponse()['ISO'],
                ['58', '62', '65', '80']
            )
        ) {
            throw new Gateway\FundsException();
        }
    }
}