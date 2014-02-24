<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\InvalidServerConfigException;
use EasyBib\OAuth2\Client\ServerConfig;

class ServerConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidParamSets()
    {
        return [
            [
                ['token_endpoint' => '/oauth/token'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidParamSets()
    {
        return [
            [[]],
            [['joe' => 'bob']],
        ];
    }

    /**
     * @dataProvider getValidParamSets
     * @param array $params
     */
    public function testValidParams(array $params)
    {
        new ServerConfig($params);
    }

    /**
     * @dataProvider getInvalidParamSets
     * @param array $params
     */
    public function testInvalidParams(array $params)
    {
        $this->setExpectedException(InvalidServerConfigException::class);
        new ServerConfig($params);
    }
}
