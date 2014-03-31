<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use Guzzle\Http\Message\Response;

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
        new TokenResponse($this->getHttpResponse($params));
    }

    /**
     * @dataProvider getValidParamSets
     * @param array $params
     * @param string $token
     */
    public function testGetToken(array $params, $token)
    {
        $incomingToken = new TokenResponse($this->getHttpResponse($params));
        $this->assertEquals($token, $incomingToken->getToken());
    }

    /**
     * @dataProvider getErrorParamSets
     * @param array $params
     * @param $expectedError
     */
    public function testGetTokenWithErrorCondition(array $params, $expectedError)
    {
        $incomingToken = new TokenResponse($this->getHttpResponse($params));
        $exceptionClass = '\EasyBib\OAuth2\Client\TokenResponse\TokenRequestErrorException';
        $this->setExpectedException($exceptionClass, $expectedError);

        $incomingToken->getToken();
    }

    public function testConstructorWithHttpError()
    {
        $httpResponse = new Response(
            504,
            ['Content-Type' => 'text/html'],
            '<html><head></head><body>Some error message</body></html>'
        );
        $exceptionClass = '\EasyBib\OAuth2\Client\TokenResponse\UnexpectedHttpErrorException';
        $this->setExpectedException($exceptionClass, 504);
        new TokenResponse($httpResponse);
    }

    public function testConstructorWithNonJson()
    {
        $httpResponse = new Response(
            200,
            [],
            '<html><head></head><body>Some error message</body></html>'
        );
        $exceptionClass = '\EasyBib\OAuth2\Client\TokenResponse\InvalidTokenResponseException';
        $this->setExpectedException($exceptionClass, 'Syntax');
        new TokenResponse($httpResponse);
    }

    /**
     * @param array $params
     * @return Response
     */
    private function getHttpResponse(array $params)
    {
        return new Response(200, [], json_encode($params));
    }
}
