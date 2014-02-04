<?php

namespace EasyBib\Tests\OAuth2\Client;

class TokenStoreTest extends TestCase
{
    public function dataForIsRefreshable()
    {
        $token = 'ABC123';
        $refreshToken = 'XYZ987';

        return [
            [['token' => $token], false],
            [['token' => $token, 'refresh_token' => $refreshToken], true],
        ];
    }

    public function testGetToken()
    {
        $token = 'jimbob';
        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->given->myTokenExpiresLater($this->tokenSession);
        $this->assertEquals($token, $this->tokenStore->getToken());
    }

    public function testGetTokenWhenExpired()
    {
        $token = 'jimbob';
        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);

        $this->assertNull($this->tokenStore->getToken());
    }

    /**
     * @dataProvider dataForIsRefreshable
     * @param array $params
     * @param $expectedValue
     */
    public function testIsRefreshable(array $params, $expectedValue)
    {
        $this->tokenSession->replace($params);
        $this->assertSame($expectedValue, $this->tokenStore->isRefreshable());
    }
}
