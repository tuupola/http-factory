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

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Nyholm\Psr7\Response as NyholmResponse;
use Slim\Http\Response as SlimResponse;
use Zend\Diactoros\Response as DiactorosResponse;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(int $code = 200, string $reason = ""): ResponseInterface
    {
        if (class_exists(DiactorosResponse::class)) {
            return new DiactorosResponse("php://memory", $code);
        }

        if (class_exists(NyholmResponse::class)) {
            return new NyholmResponse($code);
        }

        if (class_exists(SlimResponse::class)) {
            return new SlimResponse($code);
        }

        if (class_exists(GuzzleResponse::class)) {
            return new GuzzleResponse($code);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
