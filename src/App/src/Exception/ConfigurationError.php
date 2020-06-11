<?php
declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class ConfigurationError extends RuntimeException
{
    public static function missingRepositoryUrl() : self
    {
        return self::withMessage(
            'The Prismic repository url has not been defined. It must be provided in config.prismic.api'
        );
    }

    public static function withMessage(string $message) : self
    {
        return new static($message);
    }
}
