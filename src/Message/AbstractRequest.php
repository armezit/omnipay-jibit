<?php

/**
 * @package Omnipay\Jibit
 * @author Armin Rezayati <armin.rezayati@gmail.com>
 */

namespace Omnipay\Jibit\Message;

use Exception;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Jibit\Cache;
use Omnipay\Jibit\Gateway;

/**
 * Class AbstractRequest
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    /**
     * Live Endpoint URL
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://api.jibit.ir/ppg/v2';

    /**
     * @return string
     */
    abstract protected function getHttpMethod();

    /**
     * @param string $endpoint
     * @return string
     */
    abstract protected function createUri(string $endpoint);

    /**
     * @param array $data
     * @return AbstractResponse
     */
    abstract protected function createResponse(array $data);

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->getParameter('cache');
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
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->getParameter('refreshToken');
    }

    /**
     * @return string
     * @throws InvalidRequestException
     */
    public function getAmount()
    {
        $value = parent::getAmount();
        $value = $value ?: $this->httpRequest->query->get('Amount');
        return $value;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->getParameter('userId');
    }

    /**
     * @param Cache $cache
     * @return self
     */
    public function setCache(Cache $cache)
    {
        return $this->setParameter('cache', $cache);
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
     * @param string $accessToken
     * @return self
     */
    public function setAccessToken(string $accessToken)
    {
        return $this->setParameter('accessToken', $accessToken);
    }

    /**
     * @param string $refreshToken
     * @return self
     */
    public function setRefreshToken(string $refreshToken)
    {
        return $this->setParameter('refreshToken', $refreshToken);
    }

    /**
     * @param mixed $userId
     * @return self
     */
    public function setUserId($userId)
    {
        return $this->setParameter('userId', $userId);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        if ($this->getTestMode()) {
            throw new \InvalidArgumentException('Jibit payment gateway does not support test mode.');
        }
        return $this->liveEndpoint;
    }

    /**
     * @param bool $isForce
     * @return string
     * @throws Exception
     */
    private function generateToken($isForce = false)
    {
        $cache = $this->getCache();

        $cache->eraseExpired();

        if ($isForce === false && $cache->isCached('accessToken')) {
            return $this->setAccessToken($cache->retrieve('accessToken'));
        }

        if ($cache->isCached('refreshToken')) {
            $refreshToken = $this->refreshTokens();
            if ($refreshToken !== 'ok') {
                return $this->generateNewToken();
            }
        }

        return $this->generateNewToken();
    }

    private function refreshTokens()
    {
        $cache = $this->getCache();

        try {
            $httpResponse = $this->httpClient->request(
                'POST',
                $this->getEndpoint() . '/tokens/refresh',
                [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                json_encode([
                    'accessToken' => str_replace('Bearer ', '', $cache->retrieve('accessToken')),
                    'refreshToken' => $cache->retrieve('refreshToken'),
                ])
            );
            $json = $httpResponse->getBody()->getContents();
            $result = !empty($json) ? json_decode($json, true) : [];

            if (empty($result['accessToken'])) {
                throw new \RuntimeException('Err in refresh token.');
            }

            $cache->store('accessToken', 'Bearer ' . $result['accessToken'], 24 * 60 * 60 - 60);
            $cache->store('refreshToken', $result['refreshToken'], 48 * 60 * 60 - 60);
            $this->setAccessToken('Bearer ' . $result['accessToken']);
            $this->setRefreshToken($result['refreshToken']);
            return 'ok';

        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    private function generateNewToken()
    {
        try {
            $httpResponse = $this->httpClient->request(
                'POST',
                $this->getEndpoint() . '/tokens/generate',
                [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                ],
                json_encode([
                    'apiKey' => $this->getParameter('apiKey'),
                    'secretKey' => $this->getParameter('secretKey'),
                ])
            );
            $json = $httpResponse->getBody()->getContents();
            $result = !empty($json) ? json_decode($json, true) : [];

            if (empty($result['accessToken'])) {
                throw new \RuntimeException('Err in generate new token.');
            }

            $cache = $this->getCache();
            $cache->store('accessToken', 'Bearer ' . $result['accessToken'], 24 * 60 * 60 - 60);
            $cache->store('refreshToken', $result['refreshToken'], 48 * 60 * 60 - 60);
            $this->setAccessToken('Bearer ' . $result['accessToken']);
            $this->setRefreshToken($result['refreshToken']);
            return 'ok';

        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Send the request with specified data
     *
     * @param mixed $data The data to send.
     * @return ResponseInterface
     * @throws InvalidResponseException
     */
    public function sendData($data)
    {
        $this->generateToken();
        $accessToken = $this->getAccessToken();

        try {
            $httpResponse = $this->httpClient->request(
                $this->getHttpMethod(),
                $this->createUri($this->getEndpoint()),
                [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'Authorization' => $accessToken,
                ],
                json_encode($data)
            );
            $json = $httpResponse->getBody()->getContents();
            $result = !empty($json) ? json_decode($json, true) : [];
            $result['httpStatus'] = $httpResponse->getStatusCode();

            if (isset($result['errors']) && $result['errors'][0]['code'] === 'security.auth_required') {
                $this->generateToken(true);
                $retries = !isset($data['retries']) ? 0 : (int)$data['retries'];
                if ($retries <= 0) {
                    $data['retries'] = 1;
                    return $this->sendData($data);
                }
                $result['httpStatus'] = 401;
            }

            return $this->response = $this->createResponse($result);

        } catch (Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

}
