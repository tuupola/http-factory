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

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamFactoryTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $stream = new StreamFactory;
        $this->assertInstanceOf(StreamFactory::class, $stream);
    }

    public function testShouldCreateStream()
    {
        $stream = (new StreamFactory)->createStream("Hello world!");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals("Hello world!", (string) $stream);
    }
}
