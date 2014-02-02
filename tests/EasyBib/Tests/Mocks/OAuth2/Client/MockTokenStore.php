<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenStore\TokenStoreInterface;

class MockTokenStore implements TokenStoreInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var int
     */
    private $expirationTime;

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param int $time
     * @return void
     */
    public function setExpirationTime($time)
    {
        $this->expirationTime = $time;
    }

    /**
     * @return int
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }
}
