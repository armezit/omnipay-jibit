<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class CreateOrderRequest
 */
class CreateOrderRequest extends AbstractRequest
{

    /**
     * @inheritDoc
     */
    protected function getHttpMethod()
    {
        return 'POST';
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
        $this->validate('amount', 'currency', 'transactionId', 'userId', 'returnUrl');

        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency() === 'IRR' ? 'RIALS' : 'TOMAN',
            'referenceNumber' => $this->getTransactionId(),
            'userIdentifier' => $this->getUserId(),
            'callbackUrl' => $this->getReturnUrl(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function createUri(string $endpoint)
    {
        return $endpoint . '/orders';
    }

    /**
     * @param array $data
     * @return CreateOrderResponse
     */
    protected function createResponse(array $data)
    {
        return new CreateOrderResponse($this, $data);
    }
}
