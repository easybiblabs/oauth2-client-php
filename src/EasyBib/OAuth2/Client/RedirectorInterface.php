<?php

namespace EasyBib\OAuth2\Client;

interface RedirectorInterface
{
    /**
     * @param $url
     */
    public function redirect($url);
}
