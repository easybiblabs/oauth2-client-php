<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenStore;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class TokenStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    public function setUp()
    {
        parent::setUp();

        $this->session = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->session);
    }

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
        $this->session->set('token', $token);
        $this->assertEquals($token, $this->tokenStore->getToken());

        $this->session->set('expires_at', time() + 100);
        $this->assertEquals($token, $this->tokenStore->getToken());
    }

    public function testGetTokenWhenExpired()
    {
        $token = 'jimbob';
        $this->session->set('token', $token);
        $this->session->set('expires_at', time() - 100);

        $this->assertNull($this->tokenStore->getToken());
    }

    /**
     * @dataProvider dataForIsRefreshable
     * @param array $params
     * @param $expectedValue
     */
    public function testIsRefreshable(array $params, $expectedValue)
    {
        $this->session->replace($params);
        $this->assertSame($expectedValue, $this->tokenStore->isRefreshable());
    }
}
