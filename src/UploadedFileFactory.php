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

use Interop\Http\Factory\UploadedFileFactoryInterface;

final class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        if ($size === null) {
            if (is_string($file)) {
                $size = filesize($file);
            } else {
                $stats = fstat($file);
                $size = $stats['size'];
            }
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
