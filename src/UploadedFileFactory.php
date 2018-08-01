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
        StreamInterface $file,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        if ($size === null) {
            $size = $file->getSize();
        }

        if (class_exists(DiactorosUploadedFile::class)) {
            return new DiactorosUploadedFile(
                $file,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(NyholmUploadedFile::class)) {
            return new NyholmUploadedFile(
                $file,
                $size,
                $error,
                $clientFilename,
                $clientMediaType
            );
        }

        if (class_exists(SlimUploadedFile::class)) {
            if (is_resource($file)) {
                $file = stream_get_meta_data($file)["uri"];
            }

            print_r($file);

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
