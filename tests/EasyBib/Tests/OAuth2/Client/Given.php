<?php

namespace EasyBib\Tests\OAuth2\Client;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;

class Given
{
    public function iHaveAnAccessToken()
    {
        return 'ABC123';
    }

    /**
     * @param $token
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
}
