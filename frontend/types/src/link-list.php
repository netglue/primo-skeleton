<?php

declare(strict_types=1);

use Primo\Cli\TypeBuilder as T;

require_once 'global-config.php';

return [
    'Link List' => [
        'title' => T::richText('Menu Title', 'A useful menu name', [T::H1], false, false, true),
        'uid' => T::uid('Unique ID', 'A Unique identifier string'),
        'links' => T::group('Links', [
            'url' => T::link('Link Target', null),
            'anchor' => T::text('Link Text'),
        ]),
    ],
];
