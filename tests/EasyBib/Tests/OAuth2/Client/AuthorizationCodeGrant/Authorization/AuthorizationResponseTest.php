<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant\Authorization;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationErrorException;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\InvalidAuthorizationResponseException;

class AuthorizationResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testWithInvalidParams()
    {
        $params = [
            'jim' => 'bob',
        ];

        $this->setExpectedException(InvalidAuthorizationResponseException::class);
        new AuthorizationResponse($params);
    }

    public function testWithValidSuccessParams()
    {
        $params = [
            'code' => 'ABC123',
        ];

        $response = new AuthorizationResponse($params);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isError());
        $this->assertEquals('ABC123', $response->getCode());
    }

    public function testWithValidErrorParams()
    {
        $params = [
            'error' => 'access_denied',
        ];

        $response = new AuthorizationResponse($params);
        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->isError());

        $this->setExpectedException(
            AuthorizationErrorException::class,
            'access_denied'
        );

        $response->getCode();
    }
}
