<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\TokenResponse;
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
    private $expiresAt;

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
    public function setExpiresAt($time)
    {
        $this->expiresAt = $time;
    }

    /**
     * @return int
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param TokenResponse $tokenResponse
     */
    public function updateFromTokenResponse(TokenResponse $tokenResponse)
    {
        $this->setToken($tokenResponse->getToken());
        $this->setRefreshToken($tokenResponse->getRefreshToken());
        $this->setExpiresAt($tokenResponse->getExpiresAt());
    }
}
