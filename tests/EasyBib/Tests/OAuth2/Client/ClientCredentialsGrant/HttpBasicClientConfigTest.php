<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant;

use EasyBib\OAuth2\Client\InvalidClientConfigException;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasicClientConfig;

class HttpBasicConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidParamSets()
    {
        return [
            [
                [
                    'client_id' => 'ABC123',
                    'client_password' => 'XYZ987',
                ],
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
        $this->setExpectedException(InvalidClientConfigException::class);
        new HttpBasicClientConfig($params);
    }
}
