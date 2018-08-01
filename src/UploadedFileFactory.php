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

use GuzzleHttp\Psr7\UploadedFile as GuzzleUploadedFile;
use Nyholm\Psr7\UploadedFile as NyholmUploadedFile;
use Slim\Http\UploadedFile as SlimUploadedFile;
use Zend\Diactoros\UploadedFile as DiactorosUploadedFile;

use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

final class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        if ($size === null) {
            $size = $stream->getSize();
        }

        if (class_exists(DiactorosUploadedFile::class)) {
            return new DiactorosUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(NyholmUploadedFile::class)) {
            return new NyholmUploadedFile(
                $stream,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(SlimUploadedFile::class)) {
            $meta = $stream->getMetadata();
            $file = $meta["uri"];

            if ($file === "php://temp") {
                $file = tempnam(sys_get_temp_dir(), "factory-test");
                file_put_contents($file, (string) $stream);
            }

            return new SlimUploadedFile(
                $file,
                $clientFilename,
                $clientMediaType,
                $size,
                $error
            );
        }

        if (class_exists(GuzzleUploadedFile::class)) {
            return new GuzzleUploadedFile(
                $file,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
