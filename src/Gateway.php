<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Jibit\Cache;
use Omnipay\Jibit\Message\InquiryOrderRequest;
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
    public function getName(): string
    {
        return 'Jibit';
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
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
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getSecretKey(): ?string
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return string
     */
    public function getReturnUrl(): ?string
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * @param string $value
     * @return self
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setSecretKey(string $value): self
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setReturnUrl(string $value): self
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * @inheritDoc
     */
    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(CreateOrderRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function completePurchase(array $options = []): RequestInterface
    {
        return $this->createRequest(VerifyOrderRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function fetchTransaction(array $options = []): RequestInterface
    {
        return $this->createRequest(InquiryOrderRequest::class, $options);
    }

}
