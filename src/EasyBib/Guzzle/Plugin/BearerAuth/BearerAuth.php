<?php

namespace EasyBib\Guzzle\Plugin\BearerAuth;

use EasyBib\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;
use EasyBib\OAuth2\Client\AbstractSession;
use Guzzle\Common\Event;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BearerAuth
 * @link https://github.com/fkooman/guzzle-bearer-auth-plugin
 * @package EasyBib\Guzzle\Plugin\BearerAuth
 */
class BearerAuth implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => 'onRequestBeforeSend',
            'request.exception' => 'onRequestException'
        );
    }

    /**
     * @param Event $event
     */
    public function onRequestBeforeSend(Event $event)
    {
        if ($event['request']->getHeader('Authorization') instanceof Guzzle\Http\Message\Header) {
            return;
        }

        $event['request']->setHeader(
            'Authorization',
            sprintf('Bearer %s', $this->session->getToken())
        );
    }

    /**
     * @param Event $event
     * @throws BadResponseException
     */
    public function onRequestException(Event $event)
    {
        if (null !== $event['response']->getHeader("WWW-Authenticate")) {
            throw BearerErrorResponseException::factory($event['request'], $event['response']);
        }
        throw BadResponseException::factory($event['request'], $event['response']);
    }
}
