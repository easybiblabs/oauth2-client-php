<?php

namespace EasyBib\OAuth2\Client;

interface RedirectorInterface
{
    /**
     * @param $url
     * @return void
     */
    public function redirect($url);
}
