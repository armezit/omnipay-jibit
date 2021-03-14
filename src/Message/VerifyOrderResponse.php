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
    public function isSuccessful()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'Successful';
    }

    /**
     * Check if order is in Unknown status
     * Note that, in case of Unknown status, you must inquiry the order later
     * @return bool
     */
    public function isUnknownStatus(): bool
    {
        return $this->getCode() === 200 && $this->data['status'] === 'Unknown';
    }

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->request->getTransactionReference();
    }
}
