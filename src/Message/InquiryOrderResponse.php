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
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return $this->getCode() === 200 && $this->data['status'] === 'SUCCESS';
    }

    /**
     * Return order status; possible values: SUCCESS, FAILED, UNKNOWN, EXPIRED, IN_PROGRESS
     * @return string
     */
    public function getStatus(): string
    {
        return $this->data['status'];
    }

}
