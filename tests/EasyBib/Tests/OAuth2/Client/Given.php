<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenStore\TokenStoreInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class Given
{
    public function iHaveAnAccessToken()
    {
        return 'ABC123';
    }

    /**
     * @param string $token
     * @param \Guzzle\Plugin\Mock\MockPlugin $mockResponses
     */
    public function iAmReadyToRespondToATokenRequest($token, MockPlugin $mockResponses)
    {
        $tokenData = json_encode([
            'access_token' => $token,
            'expires_in' => 3600,
            'token_type' => 'bearer',
            'scope' => 'USER_READ',
            'refresh_token' => 'refresh_XYZ987',
        ]);

        $rawTokenResponse = new Response(200, [], $tokenData);
        $mockResponses->addResponse($rawTokenResponse);
    }

    /**
     * @param string $token
     * @param TokenStoreInterface $tokenStore
     */
    public function iHaveAnExpiredToken($token, TokenStoreInterface $tokenStore)
    {
        $tokenStore->setToken($token);
        $tokenStore->setExpiresAt(time() - 100);
    }

    /**
     * @param $refreshToken
     * @param TokenStoreInterface $tokenStore
     */
    public function iHaveARefreshToken($refreshToken, TokenStoreInterface $tokenStore)
    {
        $tokenStore->setRefreshToken($refreshToken);
    }
}
