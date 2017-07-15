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
use Nyholm\Psr7\Factory\ServerRequestFactory as NyholmServerRequestFactory;
use Slim\Http\Request as SlimServerRequest;
use Slim\Http\Uri as SlimUri;
use Slim\Http\Headers as SlimHeaders;
use Slim\Http\Environment as SlimEnvironment;
use Zend\Diactoros\ServerRequest as DiactorosServerRequest;
use Zend\Diactoros\ServerRequestFactory as DiactorosServerRequestFactory;

use Interop\Http\Factory\ServerRequestFactoryInterface;

final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest($method, $uri)
    {
        if (class_exists(DiactorosServerRequest::class)) {
            return new DiactorosServerRequest([], [], $uri, $method);
        }

        if (class_exists(NyholmServerRequestFactory::class)) {
            return (new NyholmServerRequestFactory)->createServerRequest($method, $uri);
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

    /**
     * {@inheritdoc}
     */
    public function createServerRequestFromArray(array $server)
    {

        if (class_exists(DiactorosServerRequest::class)) {
            $normalized = DiactorosServerRequestFactory::normalizeServer($server);
            $headers = DiactorosServerRequestFactory::marshalHeaders($server);
            $uri = DiactorosServerRequestFactory::marshalUriFromServer($normalized, $headers);
            $method = DiactorosServerRequestFactory::get("REQUEST_METHOD", $server, "GET");

            return new DiactorosServerRequest($normalized, [], $uri, $method);
        }

        if (class_exists(NyholmServerRequestFactory::class)) {
            return (new NyholmServerRequestFactory)->createServerRequestFromArray($server);
        }

        if (class_exists(SlimServerRequest::class)) {
            $environment = new SlimEnvironment($server);
            return SlimServerRequest::createFromEnvironment($environment);
        }

        if (class_exists(GuzzleServerRequest::class)) {
            if (empty($server["REQUEST_METHOD"])) {
                throw new \InvalidArgumentException("HTTP request method cannot be empty");
            } else {
                $method = $server["REQUEST_METHOD"];
            }

            $backup = $_SERVER;
            $_SERVER = $server;
            $uri = GuzzleServerRequest::getUriFromGlobals();
            $_SERVER = $backup;

            return new GuzzleServerRequest($method, $uri, [], null, "1.1", $server);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
