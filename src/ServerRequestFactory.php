<?php

/*
 * This file is part of HTTP factory package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/http-factory
 *
 */

namespace Tuupola\Http\Factory;

use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;
use Nyholm\Psr7\ServerRequest as NyholmServerRequest;
use Slim\Http\Request as SlimServerRequest;
use Slim\Http\Uri as SlimUri;
use Slim\Http\Headers as SlimHeaders;
use Slim\Http\Environment as SlimEnvironment;
use Zend\Diactoros\ServerRequest as DiactorosServerRequest;
use Zend\Diactoros\ServerRequestFactory as DiactorosServerRequestFactory;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (class_exists(DiactorosServerRequest::class)) {
            return new DiactorosServerRequest([], [], $uri, $method);
        }

        if (class_exists(NyholmServerRequest::class)) {
            return new NyholmServerRequest($method, $uri);
        }

        if (class_exists(SlimServerRequest::class)) {
            $uri = SlimUri::createFromString($uri);
            $headers = new SlimHeaders;
            $body = (new StreamFactory)->createStream("");
            return new SlimServerRequest($method, $uri, $headers, [], [], $body);
        }

        if (class_exists(GuzzleServerRequest::class)) {
            return new GuzzleServerRequest($method, $uri);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
