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
use Psr\Http\Message\RequestInterface;

class RequestFactoryTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $request = new RequestFactory;
        $this->assertInstanceOf(RequestFactory::class, $request);
    }

    public function testShouldCreateGetRequest()
    {
        $request = (new RequestFactory)->createRequest("GET", "https://example.com/");
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals("GET", $request->getMethod());
        $this->assertEquals("https://example.com/", (string) $request->getUri());
    }

    public function testShouldCreatePostRequest()
    {
        $request = (new RequestFactory)->createRequest("POST", "https://example.com/");
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals("POST", $request->getMethod());
        $this->assertEquals("https://example.com/", (string) $request->getUri());
    }
}
