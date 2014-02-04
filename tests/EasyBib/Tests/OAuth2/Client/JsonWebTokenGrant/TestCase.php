<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\Tests\OAuth2\Client\Given;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $apiBaseUrl = 'http://api.example.org';

    /**
     * @var Given
     */
    protected $given;

    protected $httpClient;

    protected $history;

    protected $mockResponses;

    public function setUp()
    {
        parent::setUp();

        $this->given = new Given();

        $this->httpClient = new Client($this->apiBaseUrl);
        $this->mockResponses = new MockPlugin();
        $this->history = new HistoryPlugin();
        $this->httpClient->addSubscriber($this->mockResponses);
        $this->httpClient->addSubscriber($this->history);
    }

    public function shouldHaveMadeATokenRequest()
    {

    }
}
