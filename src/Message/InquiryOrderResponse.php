<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

/**
 * Class InquiryOrderResponse
 */
class InquiryOrderResponse extends AbstractResponse
{

    /**
     * Return order status; possible values: SUCCESS, FAILED, UNKNOWN, EXPIRED, IN_PROGRESS
     * @return string
     */
    public function getStatus(): string
    {
        return $this->data['status'];
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'SUCCESS';
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return $this->getCode() === 200 && in_array($this->data['status'], ['FAILED', 'EXPIRED'], true);
    }

    /**
     * @inheritDoc
     */
    public function isPending()
    {
        return $this->getCode() === 200 && in_array($this->data['status'], ['UNKNOWN', 'IN_PROGRESS'], true);
    }

}
