<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

return T::slice(
    'Tagged Doc List',
    'Lists documents with a given tag',
    [
        'title' => T::richText('Title', null, [T::H2], false),
        'intro' => T::richText('Description', null, [T::P], true),
        'tag' => T::text('Tag', 'The tag you want to list'),
    ],
    [],
    'list_alt'
);
