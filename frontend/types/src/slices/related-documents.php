<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Related Documents',
    'Show a list of similar documents',
    [
        'title' => T::richText('Title', 'Optional title', [T::H2, T::A, T::B, T::I], false),
        'intro' => T::richText('Description', 'Optional intro or description', [T::P, T::A, T::B, T::I], false),
        'tag' => T::text('Tag', 'Optional tag to limit matches'),
    ],
    [],
    'call_split'
);
