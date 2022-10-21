<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Class CreateOrderResponse
 */
class CreateOrderResponse extends AbstractResponse implements RedirectResponseInterface
{

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return (int)$this->getCode() === 200;
    }

    /**
     * @inheritDoc
     */
    public function isRedirect()
    {
        return isset($this->data['pspSwitchingUrl']) && !empty($this->data['pspSwitchingUrl']);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->data['pspSwitchingUrl'];
    }

    /**
     * @inheritDoc
     */
    public function getTransactionId()
    {
        return $this->data['referenceNumber'];
    }

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->data['orderIdentifier'];
    }

}
