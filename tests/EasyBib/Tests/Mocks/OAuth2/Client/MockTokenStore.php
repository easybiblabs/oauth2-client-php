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
     * @param int $time
     * @return void
     */
    public function setExpirationTime($time)
    {
        $this->expirationTime = $time;
    }
}
