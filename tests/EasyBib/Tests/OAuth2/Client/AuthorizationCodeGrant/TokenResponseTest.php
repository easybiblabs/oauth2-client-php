<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

class TokenResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidParamSets()
    {
        return [
            [
                [
                    'access_token' => 'ABC123',
                    'token_type' => 'bearer',
                ],
                'ABC123',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getErrorParamSets()
    {
        return [
            [
                [
                    'error' => 'invalid_request',
                ],
                'invalid_request',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidParamSets()
    {
        $validSet = $this->getValidParamSets()[0][0];

        $invalidSets = [];

        foreach (array_keys($validSet) as $key) {
            $set = $validSet;
            unset($set[$key]);
            $invalidSets[] = [$set];
        }

        return $invalidSets;
    }

    /**
     * @dataProvider getInvalidParamSets
     * @param array $params
     */
    public function testConstructorValidates(array $params)
    {
        $exceptionClass = '\EasyBib\OAuth2\Client\TokenResponse\InvalidTokenResponseException';
        $this->setExpectedException($exceptionClass);
        new TokenResponse($params);
    }

    /**
     * @dataProvider getValidParamSets
     * @param array $params
     * @param string $token
     */
    public function testGetToken(array $params, $token)
    {
        $incomingToken = new TokenResponse($params);
        $this->assertEquals($token, $incomingToken->getToken());
    }

    /**
     * @dataProvider getErrorParamSets
     * @param array $params
     * @param $expectedError
     */
    public function testGetTokenWithErrorCondition(array $params, $expectedError)
    {
        $incomingToken = new TokenResponse($params);
        $exceptionClass = '\EasyBib\OAuth2\Client\TokenResponse\TokenRequestErrorException';
        $this->setExpectedException($exceptionClass, $expectedError);

        $incomingToken->getToken();
    }
}
