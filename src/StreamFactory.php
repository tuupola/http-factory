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

use GuzzleHttp\Psr7\Stream as GuzzleStream;
use Nyholm\Psr7\Stream as NyholmStream;
use Slim\Http\Stream as SlimStream;
use Zend\Diactoros\Stream as DiactorosStream;

use Interop\Http\Factory\StreamFactoryInterface;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createStream($content = "")
    {
        $resource = fopen("php://temp", "r+");
        $stream = $this->createStreamFromResource($resource);
        $stream->write($content);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromFile($filename, $mode = "r")
    {
        $resource = fopen($filename, $mode);
        return $this->createStreamFromResource($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromResource($resource)
    {
        if (class_exists(DiactorosStream::class)) {
            return new DiactorosStream($resource);
        }

        if (class_exists(NyholmStream::class)) {
            return NyholmStream::createFromResource($resource);
        }

        if (class_exists(SlimStream::class)) {
            return new SlimStream($resource);
        }

        if (class_exists(GuzzleStream::class)) {
            return new GuzzleStream($resource);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
