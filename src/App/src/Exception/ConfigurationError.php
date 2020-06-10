<?php
declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class ConfigurationError extends RuntimeException
{
    public static function missingRepositoryUrl() : self
    {
        return new static(
            'The Prismic repository url has not been defined. It must be provided in config.prismic.api'
        );
    }
}
