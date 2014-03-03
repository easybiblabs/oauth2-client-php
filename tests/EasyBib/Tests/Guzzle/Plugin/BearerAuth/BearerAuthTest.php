<?php

namespace EasyBib\Tests\Guzzle\Plugin\BearerAuth;

use EasyBib\Guzzle\Plugin\BearerAuth\BearerAuth;
use EasyBib\OAuth2\Client\SimpleSession;
use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BearerAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \EasyBib\OAuth2\Client\SimpleSession
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->getMockBuilder('\EasyBib\OAuth2\Client\SimpleSession')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue('token_123'));
    }

    public function testMultipleSendsSetOnlyOneHeader()
    {
        $plugin = new BearerAuth($this->session);

        $request = new \Guzzle\Http\Message\Request('GET', '/');

        $event = new Event(['request' => $request]);

        $plugin->onRequestBeforeSend($event);
        $this->assertSame('Bearer token_123', $request->getHeader('Authorization') . '');
        $plugin->onRequestBeforeSend($event);
        $this->assertSame('Bearer token_123', $request->getHeader('Authorization') . '');
    }

    public function testReusedPluginInstanceStillSetsHeader()
    {
        $plugin = new BearerAuth($this->session);

        $request1 = new \Guzzle\Http\Message\Request('GET', '/');
        $request2 = new \Guzzle\Http\Message\Request('GET', '/');

        $event = new Event(['request' => $request1]);

        $plugin->onRequestBeforeSend(new Event(['request' => $request1]));
        $this->assertSame('Bearer token_123', $request1->getHeader('Authorization') . '');

        $plugin->onRequestBeforeSend(new Event(['request' => $request2]));
        $this->assertSame('Bearer token_123', $request2->getHeader('Authorization') . '');
    }
}
