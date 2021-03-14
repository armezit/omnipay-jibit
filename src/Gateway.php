<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Jibit\Message\VerifyOrderRequest;
use Omnipay\Jibit\Message\CreateOrderRequest;

/**
 * Class Gateway
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     * @return string
     */
    public function getName()
    {
        return 'Jibit';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'testMode' => false,
            'apiKey' => '',
            'secretKey' => '',
            'accessToken' => '',
            'refreshToken' => '',
            'returnUrl' => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function initialize(array $parameters = [])
    {
        parent::initialize($parameters);

        $this->setParameter('cache', new Cache($this->getName()));

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setApiKey(string $value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return Gateway
     */
    public function setSecretKey(string $value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setReturnUrl(string $value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(CreateOrderRequest::class, $parameters);
    }

    /**
     * @param array $parameters
     * @return AbstractRequest|RequestInterface
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(VerifyOrderRequest::class, $parameters);
    }
}
