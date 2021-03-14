<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class VerifyOrderRequest
 */
class VerifyOrderRequest extends AbstractRequest
{

    /**
     * @inheritDoc
     */
    protected function getHttpMethod()
    {
        return 'GET';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // Validate required parameters before return data
        $this->validate('transactionReference');

        return [];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function createUri(string $endpoint)
    {
        return $endpoint . '/orders/' . $this->getTransactionReference() . '/verify';
    }

    /**
     * @param array $data
     * @return VerifyOrderResponse
     */
    protected function createResponse(array $data)
    {
        return new VerifyOrderResponse($this, $data);
    }
}
