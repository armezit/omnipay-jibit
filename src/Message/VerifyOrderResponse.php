<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

/**
 * Class VerifyOrderResponse
 */
class VerifyOrderResponse extends AbstractResponse
{

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->request->getTransactionReference();
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'Successful';
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'Failed';
    }

    /**
     * In case of pending, you must inquiry the order later
     * @return bool
     */
    public function isPending()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'Unknown';
    }

}
