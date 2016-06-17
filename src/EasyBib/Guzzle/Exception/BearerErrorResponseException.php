<?php

namespace EasyBib\Guzzle\Exception;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BearerErrorResponseException
 * @link https://github.com/fkooman/guzzle-bearer-auth-plugin
 * @package EasyBib\Guzzle\Plugin\BearerAuth\Exception
 */
class BearerErrorResponseException extends RequestException
{
    /**
     * @var string
     */
    private $bearerReason;

    /**
     * @return string
     */
    public function getBearerReason()
    {
        return $this->bearerReason;
    }

    /**
     * @param string $bearerReason
     */
    public function setBearerReason($bearerReason)
    {
        $this->bearerReason = $bearerReason;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Guzzle\Http\Exception\BadResponseException
     */
    public static function create(RequestInterface $request, ResponseInterface $response)
    {
        $label = 'Bearer error response';
        $bearerReason = self::headerToReason($response->getHeader("WWW-Authenticate"));
        $message = $label . PHP_EOL . implode(PHP_EOL, [
            '[status code] ' . $response->getStatusCode(),
            '[reason phrase] ' . $response->getReasonPhrase(),
            '[bearer reason] ' . $bearerReason,
            '[url] ' . $request->getUri(),
        ]);

        $exception = new static($message, $request, $response);
        $exception->setBearerReason($bearerReason);

        return $exception;
    }

    /**
     * @param string[]|false $headers
     * @return string
     */
    public static function headerToReason($headers)
    {
        if (!empty($headers)) {
            foreach ($headers as $value) {
                if (isset($value['error'])) {
                    return $value['error'];
                }
            }
        }

        return null;
    }
}
