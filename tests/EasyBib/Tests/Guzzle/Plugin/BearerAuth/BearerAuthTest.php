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

        $request = $this->getMockBuilder('\Guzzle\Http\Message\Request')
            ->setConstructorArgs(['GET', '/'])
            ->getMock();

        $request->expects($this->once())
            ->method('setHeader');

        $event = new Event(['request' => $request]);
        $plugin->onRequestBeforeSend($event);
        $plugin->onRequestBeforeSend($event);
    }
}
