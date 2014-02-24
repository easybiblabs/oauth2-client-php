<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\OAuth2\Client\AbstractSession;
use Guzzle\Http\Client;
use Guzzle\Plugin\History\HistoryPlugin;

class ResourceRequest
{
    /**
     * @var AbstractSession
     */
    private $session;

    /**
     * @param AbstractSession $session
     */
    public function __construct(AbstractSession $session)
    {
        $this->session = $session;
    }

    /**
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function execute()
    {
        $history = new HistoryPlugin();

        $httpClient = new Client();
        $httpClient->addSubscriber($history);

        $this->session->addResourceClient($httpClient);

        $request = $httpClient->get('http://example.org');
        $request->send();

        return $history->getLastRequest();
    }
}
