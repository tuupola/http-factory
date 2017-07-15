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

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use Nyholm\Psr7\Uri as NyholmUri;
use Slim\Http\Uri as SlimUri;
use Zend\Diactoros\Uri as DiactorosUri;

use Interop\Http\Factory\UriFactoryInterface;

final class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri = "")
    {
        if (class_exists(DiactorosUri::class)) {
            return new DiactorosUri($uri);
        }

        if (class_exists(NyholmUri::class)) {
            return new NyholmUri($uri);
        }

        if (class_exists(SlimUri::class)) {
            if (false === parse_url($uri)) {
                throw new \InvalidArgumentException("Invalid URI: $uri");
            }
            return SlimUri::createFromString($uri);
        }

        if (class_exists(GuzzleUri::class)) {
            return new GuzzleUri($uri);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
