<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Standard Prose',
    'A regular block of written content',
    [
        'content' => T::richText(
            'Your words',
            null,
            T::blocksAll(),
            true,
            true,
            false,
            DEFAULT_TEXT_LABELS
        ),
    ],
    [],
    'text_fields'
);
